<?php
session_start();

// Config lade
$config = include(__DIR__ . '/../config.php');

// WebsiteID us der URL hole
$websiteId = isset($_GET['websiteId']) ? (int)$_GET['websiteId'] : 1;

// Website config ider config finde
$websiteConfig = array_filter($config, function($site) use ($websiteId) {
    return isset($site['id']) && $site['id'] === $websiteId;
});
$websiteConfig = reset($websiteConfig);

// omg en request mache zum backup erstelle und so wie bin ich so tüüf gfalle 😭
$source = $websiteConfig['database'];
$backupDir = __DIR__ . '/../' . $websiteConfig['backup_folder'];
$timestamp = date('Y-m-d-H-i-s');
$destination = $backupDir . '/' . $timestamp . '.db';

// stell der vor es git kei backup folder 😱 denn meun mer de mache 
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// und jetzt stell der vor mer kopieret die datei deht ineh 😱
if (copy($source, $destination)) {
    $backupMessage = "Database backup created successfully: " . htmlspecialchars($destination);
} else {
    $backupMessage = "Failed to create database backup.";
}

// zrug zum sexxi admin panel mit de message mir sind so guet und sexi , ich wird niemals eh frau ha im lebe ich bin single af
header("Location: index.php?backupMessage=" . urlencode($backupMessage));
exit();
?>
