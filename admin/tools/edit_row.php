<?php
session_start();

// Load configuration
$config = include(__DIR__ . '/../../config.php');

// Get the website ID from the session
$websiteId = $_SESSION['websiteId'] ?? 1;

// Find the website configuration
$websiteConfig = array_filter($config, function($site) use ($websiteId) {
    return isset($site['id']) && $site['id'] === $websiteId;
});
$websiteConfig = reset($websiteConfig);

// Connect to SQLite database
$db = new SQLite3($websiteConfig['database']);

// Get the table and ID from the request
$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? 0;

$columns = [];
$columnsResult = $db->query("PRAGMA table_info($table)");
while ($row = $columnsResult->fetchArray(SQLITE3_ASSOC)) {
    $columns[] = $row['name'];
}

$stmt = $db->prepare("SELECT * FROM $table WHERE id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$result = $stmt->execute();

if ($result) {
    $row = $result->fetchArray(SQLITE3_ASSOC);
} else {
    $row = null;
}

if (isset($_POST['save_changes'])) {
    foreach ($columns as $column) {
        $value = $_POST[$column] ?: null;
        if ($column == 'date' && empty($value)) {
            $value = date('Y-m-d H:i:s');
        }
        if ($table == 'content' && $column == 'custom_html') {
            $value = $value ? 1 : 0;
        }
        $stmt = $db->prepare("UPDATE $table SET $column = :value WHERE id = :id");
        $stmt->bindValue(':value', $value, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }
    header("Location: edit_database.php?table=$table");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Row</title>
</head>
<body>
    <h1>Edit Row in Table: <?php echo htmlspecialchars($table); ?></h1>
    <form method="post">
        <?php foreach ($columns as $column): ?>
            <label for="<?php echo htmlspecialchars($column); ?>"><?php echo htmlspecialchars($column); ?>:</label>
            <?php if ($table == 'content' && $column == 'content'): ?>
                <textarea id="<?php echo htmlspecialchars($column); ?>" name="<?php echo htmlspecialchars($column); ?>" style="resize: both;"><?php echo htmlspecialchars($row[$column] ?? ''); ?></textarea>
            <?php else: ?>
                <input type="text" id="<?php echo htmlspecialchars($column); ?>" name="<?php echo htmlspecialchars($column); ?>" value="<?php echo htmlspecialchars($row[$column] ?? ''); ?>">
            <?php endif; ?>
            <br>
        <?php endforeach; ?>
        <button type="submit" name="save_changes">Save Changes</button>
    </form>
    <a href="edit_database.php?table=<?php echo htmlspecialchars($table); ?>">Back to Table</a>
</body>
</html>
