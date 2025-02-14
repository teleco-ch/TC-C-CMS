<?php
session_start();

// Load configuration
$config = include(__DIR__ . '/../../config.php');

// Get the website ID from the request or session
$websiteId = isset($_GET['websiteId']) ? (int)$_GET['websiteId'] : (isset($_SESSION['websiteId']) ? (int)$_SESSION['websiteId'] : 1);
$_SESSION['websiteId'] = $websiteId;

// Find the website configuration
$websiteConfig = array_filter($config, function($site) use ($websiteId) {
    return isset($site['id']) && $site['id'] === $websiteId;
});
$websiteConfig = reset($websiteConfig);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = realpath(__DIR__ . '/../../' . $websiteConfig['folder']);
$currentDir = isset($_GET['dir']) ? realpath($rootDir . '/' . $_GET['dir']) : $rootDir;

// Ensure the current directory is within the root directory
if (strpos($currentDir, $rootDir) !== 0) {
    $currentDir = $rootDir;
}

// Handle file upload
if (isset($_POST['upload'])) {
    $targetFile = $currentDir . '/' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        $message = "File uploaded successfully.";
    } else {
        $message = "Failed to upload file.";
    }
}

// Handle file deletion
if (isset($_POST['delete'])) {
    $fileToDelete = $currentDir . '/' . $_POST['filename'];
    if (unlink($fileToDelete)) {
        $message = "File deleted successfully.";
    } else {
        $message = "Failed to delete file.";
    }
}

// Handle file renaming
if (isset($_POST['rename'])) {
    $oldName = $currentDir . '/' . $_POST['oldname'];
    $newName = $currentDir . '/' . $_POST['newname'];
    if (rename($oldName, $newName)) {
        $message = "File renamed successfully.";
    } else {
        $message = "Failed to rename file.";
    }
}

// Handle folder creation
if (isset($_POST['create_folder'])) {
    $newFolder = $currentDir . '/' . $_POST['foldername'];
    if (mkdir($newFolder, 0755, true)) {
        $message = "Folder created successfully.";
    } else {
        $message = "Failed to create folder.";
    }
}

// Handle folder deletion
if (isset($_POST['delete_folder'])) {
    $folderToDelete = $currentDir . '/' . $_POST['foldername'];
    if (rmdir($folderToDelete)) {
        $message = "Folder deleted successfully.";
    } else {
        $message = "Failed to delete folder.";
    }
}

// Handle folder backup
if (isset($_POST['backup_folder'])) {
    $backupDir = realpath(__DIR__ . '/../../' . $websiteConfig['backup_folder']);
    $backupFile = $backupDir . '/' . basename($currentDir) . '_' . date('Ymd_His') . '.tar';
    $command = "tar -cf $backupFile -C " . escapeshellarg($currentDir) . " .";
    exec($command, $output, $returnVar);
    if ($returnVar === 0) {
        $message = "Folder backed up successfully.";
    } else {
        $message = "Failed to back up folder.";
    }
}

// Get list of files and directories
$items = scandir($currentDir);

// Get relative path for display
$relativePath = str_replace($rootDir, '', $currentDir);
if ($relativePath === '') {
    $relativePath = '/';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Files</title>
</head>
<body>
    <div class="container">
        <form method="post" action="../index.php">
            <button type="submit">Back to Admin Panel</button>
        </form>
        <h1>File Manager</h1>
        <form method="get" action="manage_files.php">
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
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <h2>Current Directory: <?php echo htmlspecialchars($relativePath); ?></h2>
        <form method="post">
            <button type="submit" name="refresh">Refresh</button>
        </form>
        <table border="1">
            <thead>
                <tr>
                    <th colspan="2"></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($currentDir !== $rootDir): ?>
                    <tr>
                        <td colspan="2"><a href="?dir=<?php echo urlencode(dirname($relativePath)); ?>&websiteId=<?php echo $websiteId; ?>">.. (Parent Directory)</a></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="2" style="text-align: center; background-color: yellow;"><strong>Folders</strong></td>
                </tr>
                <?php foreach ($items as $item): ?>
                    <?php if ($item === '.' || $item === '..') continue; ?>
                    <?php if (is_dir($currentDir . '/' . $item)): ?>
                        <tr>
                            <td><a href="?dir=<?php echo urlencode(ltrim($relativePath . '/' . $item, '/')); ?>&websiteId=<?php echo $websiteId; ?>"><?php echo htmlspecialchars($item); ?></a></td>
                            <td class="actions">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="foldername" value="<?php echo htmlspecialchars($item); ?>">
                                    <button type="submit" name="delete_folder">Delete</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="oldname" value="<?php echo htmlspecialchars($item); ?>">
                                    <input type="text" name="newname" placeholder="New name">
                                    <button type="submit" name="rename">Rename</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2" style="text-align: center; background-color: orange;"><strong>Files</strong></td>
                </tr>
                <?php foreach ($items as $item): ?>
                    <?php if ($item === '.' || $item === '..') continue; ?>
                    <?php if (!is_dir($currentDir . '/' . $item)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item); ?></td>
                            <td class="actions">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="filename" value="<?php echo htmlspecialchars($item); ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="oldname" value="<?php echo htmlspecialchars($item); ?>">
                                    <input type="text" name="newname" placeholder="New name">
                                    <button type="submit" name="rename">Rename</button>
                                </form>
                                <a href="../../<?php echo htmlspecialchars($websiteConfig['folder'] . '/' . $relativePath . '/' . $item); ?>" download>Download</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2">
                        <h2>Upload File</h2>
                        <form method="post" enctype="multipart/form-data">
                            <input type="file" name="file">
                            <button type="submit" name="upload">Upload</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h2>Create Folder</h2>
                        <form method="post">
                            <input type="text" name="foldername" placeholder="Folder name">
                            <button type="submit" name="create_folder">Create</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h2>Backup Folder</h2>
                        <form method="post">
                            <button type="submit" name="backup_folder">Backup Folder</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
