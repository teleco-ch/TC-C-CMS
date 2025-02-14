<?php
// Start session und so
session_start();

// Config lade
$config = include(__DIR__ . '/../config.php');

// Sqlite 
$db = new SQLite3(__DIR__ . '/../content.db');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/tc.css">
    <style> /* nachher gihts denne keis styling meh im adminier panel will das blaot oder so isch*/
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .header-content {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-content img {
            margin-right: 10px;
            width: 50px; 
            height: auto; 
        }
        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
        }
        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            padding: 15px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .content h1 {
            margin-top: 0;
        }
        .content p {
            margin-bottom: 20px;
        }
        .content form {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
    <div class="header-content">
        <img src="assets/svg/teleco.svg" alt="Teleco Logo" class="logo">
        <h1>Adminier Panel</h1>
    </div>
    <div class="container">
        <div class="sidebar">
            <h2>Adminier Panel</h2>
            <form method="GET" action="index.php">
                <label for="websiteId">Select Website:</label>
                <select name="websiteId" id="websiteId" onchange="this.form.submit()">
                    <?php foreach ($config as $site): ?>
                        <?php if (isset($site['id'])): ?>
                            <option value="<?php echo $site['id']; ?>" <?php echo (isset($_GET['websiteId']) && $_GET['websiteId'] == $site['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($site['name']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="backup_database.php?websiteId=<?php echo isset($_GET['websiteId']) ? (int)$_GET['websiteId'] : 1; ?>">Backup Database</a>
            <a href="tools/edit_database.php">Edit das Database</a>
            <a href="tools/manage_files.php">Manger das Filet</a>
        </div>
        <div class="content">
            <h1>Welcome to the TC-C-CMS admin Panel</h1>
            <p>Feast your eyes! This is the last time you will see properish CSS in the admin panel.</p>
            <p>Made with lots of love for PHP and pure hatred against JavaScript (has none) by T.B <br> TC-C-CMS means Teleco's Crappy Content Management System btw lol hahahaha ich chan das alles nümmeh!  </p>
            <?php if (isset($_GET['backupMessage'])): ?>
                <p><?php echo htmlspecialchars($_GET['backupMessage']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>