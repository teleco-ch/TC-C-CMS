<?php
session_start();

// Config lade
$config = include(__DIR__ . '/../../config.php');

// WebsiteID us der URL oder session hole 
$websiteId = isset($_GET['websiteId']) ? (int)$_GET['websiteId'] : (isset($_SESSION['websiteId']) ? (int)$_SESSION['websiteId'] : 1);
$_SESSION['websiteId'] = $websiteId;

// Website config ider config finde
$websiteConfig = array_filter($config, function($site) use ($websiteId) {
    return isset($site['id']) && $site['id'] === $websiteId;
});
$websiteConfig = reset($websiteConfig);

// Sqlite datenbank ahfigge
$db = new SQLite3($websiteConfig['database']);

// Backup erstelle oder ganzi datehbank lôsche *evil laugh* 😈 jk mer propbieret sehr schlecht IDs wieder sekuentiel zmache
if (isset($_POST['backup']) || isset($_POST['delete_row']) || isset($_POST['reassign_ids'])) {
    $source = $websiteConfig['database'];
    $backupDir = __DIR__ . '/../../' . $websiteConfig['backup_folder'];
    $timestamp = date('Y-m-d-H-i-s');
    $destination = $backupDir . '/' . $timestamp . '.db';

    // machen das backup directory wenns nid git
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    // omg file deht ineh kopiere 😱
    if (copy($source, $destination)) {
        $backupMessage = "Database backup created successfully: " . htmlspecialchars($destination);
    } else {
        $backupMessage = "Failed to create database backup.";
    }
}

$selectedTable = $_POST['table'] ?? $_SESSION['selectedTable'] ?? '';
$columns = [];
$rows = [];
$sortOrder = $_GET['sortOrder'] ?? 'ASC';

$tablesResult = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
$tables = [];
while ($row = $tablesResult->fetchArray(SQLITE3_ASSOC)) {
    $tables[] = $row['name'];
}

if ($selectedTable && in_array($selectedTable, $tables)) {
    $_SESSION['selectedTable'] = $selectedTable;
    $columnsResult = $db->query("PRAGMA table_info($selectedTable)");
    while ($row = $columnsResult->fetchArray(SQLITE3_ASSOC)) {
        $columns[] = $row['name'];
    }
    if ($selectedTable == 'content' && in_array('date', $columns)) {
        $orderBy = "ORDER BY date DESC"; // das hier machen default sorting nach datum und ziiht de neuschti row zerscht will susch isch das unmanagebar
    } else {
        $orderBy = in_array('date', $columns) ? "ORDER BY date $sortOrder" : "ORDER BY id ASC";
    }
    $rowsResult = $db->query("SELECT * FROM $selectedTable $orderBy");
    while ($row = $rowsResult->fetchArray(SQLITE3_ASSOC)) {
        $rows[] = $row;
    }
} else {
    $selectedTable = '';
    $_SESSION['selectedTable'] = '';
}

if (isset($_POST['edit_row'])) {
    header("Location: edit_row.php?table=$selectedTable&id=" . $_POST['id']);
    exit();
}

if (isset($_POST['delete_row'])) {
    $idsToDelete = $_POST['ids'] ?? [];
    foreach ($idsToDelete as $id) {
        $stmt = $db->prepare("DELETE FROM $selectedTable WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }
    $message = "Selected rows deleted successfully (most likely).";
}

if (isset($_POST['insert_row'])) {
    $values = $_POST['values'];
    $highestIdResult = $db->querySingle("SELECT MAX(id) as max_id FROM $selectedTable");
    $highestId = $highestIdResult ? $highestIdResult : 0;
    $values['id'] = $highestId + 1;
    if ($selectedTable == 'content') {
        if (empty($values['date'])) {
            $values['date'] = date('Y-m-d H:i:s');
        }
    }
    $columnsString = implode(", ", array_keys($values));
    $placeholders = implode(", ", array_fill(0, count($values), "?"));
    $stmt = $db->prepare("INSERT INTO $selectedTable ($columnsString) VALUES ($placeholders)");
    $index = 1;
    foreach ($values as $value) {
        $stmt->bindValue($index, $value ?: null, SQLITE3_TEXT);
        $index++;
    }
    $stmt->execute();
    $message = "Row inserted successfully. (maybe)";
}

if (isset($_POST['swap_row'])) {
    $id = $_POST['id'];
    $targetId = $_POST['target_id'];
    $db->exec("BEGIN TRANSACTION");
    $db->exec("UPDATE $selectedTable SET id = -1 WHERE id = $id");
    $db->exec("UPDATE $selectedTable SET id = $id WHERE id = $targetId");
    $db->exec("UPDATE $selectedTable SET id = $targetId WHERE id = -1");
    $db->exec("COMMIT");
    $message = "Row swapped successfully for sure.";
}

if (isset($_POST['push_row'])) {
    $id = $_POST['id'];
    $targetId = $_POST['target_id'];
    $tempTable = $selectedTable . '_temp';
    $db->exec("BEGIN TRANSACTION");
    $db->exec("CREATE TEMPORARY TABLE $tempTable AS SELECT * FROM $selectedTable");
    $db->exec("UPDATE $tempTable SET id = -1 WHERE id = $id");
    if ($id < $targetId) {
        $db->exec("UPDATE $tempTable SET id = id - 1 WHERE id > $id AND id <= $targetId");
    } else {
        $db->exec("UPDATE $tempTable SET id = id + 1 WHERE id < $id AND id >= $targetId");
    }
    $db->exec("UPDATE $tempTable SET id = $targetId WHERE id = -1");
    $db->exec("DELETE FROM $selectedTable");
    $db->exec("INSERT INTO $selectedTable SELECT * FROM $tempTable");
    $db->exec("DROP TABLE $tempTable");
    $db->exec("COMMIT");
    $message = "Row pushed successfully (perhaps).";
}

if (isset($_POST['reassign_ids'])) {
    $tempBackup = $backupDir . '/' . $timestamp . '_temp.db';
    copy($source, $tempBackup);

    try {
        $db->exec("BEGIN TRANSACTION");
        $tempTable = $selectedTable . '_temp';
        $db->exec("CREATE TEMPORARY TABLE $tempTable AS SELECT * FROM $selectedTable");
        $db->exec("DELETE FROM $selectedTable");

        if (in_array('date', $columns)) {
            $orderBy = "ORDER BY date ASC";
        } else {
            $orderBy = "ORDER BY id ASC";
        }

        $rowsResult = $db->query("SELECT * FROM $tempTable $orderBy");
        $newId = 1;
        while ($row = $rowsResult->fetchArray(SQLITE3_ASSOC)) {
            $row['id'] = $newId++;
            $columnsString = implode(", ", array_keys($row));
            $placeholders = implode(", ", array_fill(0, count($row), "?"));
            $stmt = $db->prepare("INSERT INTO $selectedTable ($columnsString) VALUES ($placeholders)");
            $index = 1;
            foreach ($row as $value) {
                $stmt->bindValue($index, $value ?: null, SQLITE3_TEXT);
                $index++;
            }
            $stmt->execute();
        }

        $db->exec("DROP TABLE $tempTable");
        $db->exec("COMMIT");
        $message = "IDs reassigned successfully.";
    } catch (Exception $e) {
        copy($tempBackup, $source);
        $message = "Failed to reassign IDs. Database restored from backup. (probably)";
    } finally {
        unlink($tempBackup);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Database</title>
</head>
<body>
    <form method="post" action="../index.php">
        <button type="submit">Back to Admin Panel</button>
    </form>
    <h1>Edit Database</h1>
    <form method="get" action="edit_database.php">
        <label for="websiteId">Select Website:</label>
        <select name="websiteId" id="websiteId" onchange="this.form.submit()">
            <?php foreach ($config as $site): ?>
                <?php if (isset($site['id'])): ?>
                    <option value="<?php echo $site['id']; ?>" <?php echo ($websiteId == $site['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($site['name']); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </form>
    <form method="post">
        <button type="submit" name="backup">Backup das Database</button>
    </form>
    <?php if (isset($backupMessage)): ?>
        <p><?php echo $backupMessage; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="table">Select das Table:</label>
        <select id="table" name="table" onchange="this.form.submit()">
            <option value="">-- Select a table --</option>
            <?php foreach ($tables as $table): ?>
                <option value="<?php echo htmlspecialchars($table); ?>" <?php echo ($selectedTable == $table) ? 'selected' : ''; ?>><?php echo htmlspecialchars($table); ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <form method="post">
        <button type="submit" name="refresh">Refresh</button>
    </form>
    <?php if ($selectedTable): ?>
        <h2>Editing Table: <?php echo htmlspecialchars($selectedTable); ?></h2>
        <?php if ($selectedTable == 'content'): ?>
            <form method="post">
                <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <?php foreach ($columns as $column): ?>
                                <th>
                                    <?php echo htmlspecialchars($column); ?>
                                    <?php if ($column == 'date'): ?>
                                        <a href="?table=<?php echo htmlspecialchars($selectedTable); ?>&sortOrder=<?php echo $sortOrder == 'ASC' ? 'DESC' : 'ASC'; ?>">Sort by Date</a>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <?php foreach ($columns as $column): ?>
                                <td>
                                    <?php if ($column == 'id'): ?>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]" disabled>
                                    <?php elseif ($column == 'date'): ?>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]" value="<?php echo date('Y-m-d H:i:s'); ?>" disabled>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]">
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <button type="submit" name="insert_row">Add Row</button>
                            </td>
                        </tr>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>"></td>
                                <?php foreach ($columns as $column): ?>
                                    <td><?php echo htmlspecialchars($row[$column] ?? ''); ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="edit_row">Edit</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_row">Delete</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="target_id" placeholder="Target ID">
                                        <button type="submit" name="swap_row">Swap</button>
                                        <button type="submit" name="push_row">Push</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="delete_row">Maybe Delete Selected Rows</button>
                <button type="submit" name="reassign_ids">Maybe Reassign IDs</button>
            </form>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <?php foreach ($columns as $column): ?>
                                <th>
                                    <?php echo htmlspecialchars($column); ?>
                                    <?php if ($column == 'date'): ?>
                                        <a href="?table=<?php echo htmlspecialchars($selectedTable); ?>&sortOrder=<?php echo $sortOrder == 'ASC' ? 'DESC' : 'ASC'; ?>">Sort by Datum</a>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>"></td>
                                <?php foreach ($columns as $column): ?>
                                    <td><?php echo htmlspecialchars($row[$column] ?? ''); ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="edit_row">Edit</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_row">Bye</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="text" name="target_id" placeholder="Target ID">
                                        <button type="submit" name="swap_row">Swap</button>
                                        <button type="submit" name="push_row">Push</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td></td>
                            <?php foreach ($columns as $column): ?>
                                <td>
                                    <?php if ($column == 'id'): ?>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]" disabled>
                                    <?php elseif ($column == 'date'): ?>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]" value="<?php echo date('Y-m-d H:i:s'); ?>" disabled>
                                    <?php elseif ($selectedTable == 'content' && $column == 'custom_html'): ?>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]" value="0">
                                    <?php else: ?>
                                        <input type="text" name="values[<?php echo htmlspecialchars($column); ?>]">
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <button type="submit" name="insert_row">Add Row</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" name="delete_row">Delete Selected Rows</button>
                <button type="submit" name="reassign_ids">Reassign IDs</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>No das table selected. Please select das table to editieren.</p>
    <?php endif; ?>
</body>
</html>