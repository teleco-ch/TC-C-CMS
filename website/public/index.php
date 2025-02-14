<?php

// Load configuration
$config = include(__DIR__ . '/../../config.php');

// Define the ID of this site
$siteId = 1;

// Find the website configuration
$websiteConfig = array_filter($config, function($site) use ($siteId) {
    return isset($site['id']) && $site['id'] === $siteId;
});
$websiteConfig = reset($websiteConfig);

// Include components
foreach ($websiteConfig['components'] as $component) {
    include($component);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teleco.ch - <?php echo ucfirst($page); ?></title>
    <meta name="description" content="<?php echo $metaDescription; ?>">
    <link rel="stylesheet" href="assets/css/tc.css">
</head>
<body>
    <header>
        <div class="header-content" <?php if ($navbarTableExists && $navbarHasEntries) echo 'style="display:none;"'; ?>>
            <img src="assets/svg/teleco.svg" alt="Teleco Logo" class="logo">
            <h1>Teleco.ch</h1>
        </div>
    </header>
    
    <?php if ($navbarTableExists && $navbarHasEntries): ?>
        <nav class="navbar">
            <?php render_navbar($navbarItems, $page); ?>
        </nav>
        <?php render_mobile_navbar($navbarItems, $menuExpanded); ?>
    <?php endif; ?>
    <hr>

    <div class="container" style="<?php echo $leftSidebarHasItems || $rightSidebarHasItems ? '' : 'margin-left: 0px; margin-right: 0px; flex-wrap: nowrap; margin-top: 0px; margin-bottom: 0px; max-width: 100%; box-sizing: border-box; padding: 10px;'; ?>">
        <!-- Left Sidebar -->
        <?php if ($leftSidebarHasItems): ?>
            <div class="sidebar">
                <?php get_sidebar($db, 'left', $page, $sub); ?>
            </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="content" id="content" style="<?php echo $leftSidebarHasItems || $rightSidebarHasItems ? '' : 'width: calc(100%);'; ?>">
            <!-- Comment indicating if left elements were detected -->
                 <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <?php
foreach ($breadcrumbs as $index => $crumb) {
    if ($index > 0) echo ' > ';
    echo '<a href="?page=' . htmlspecialchars($crumb['page']) . '">';
    echo htmlspecialchars($crumb['title']);
    echo '</a>';
}
        ?>
    </div>
            <?php if ($leftSidebarHasItems): ?>
                <!-- Left sidebar elements detected -->
            <?php else: ?>
                <!-- No left sidebar elements detected -->
            <?php endif; ?>
            <?php if ($hasContent): ?>
                <?php
                $result = $query->execute();
                $lastPostId = 0;
                while ($row = $result->fetchArray()):
                    $lastPostId++;
                ?>
                    <div class="box" id="post-<?php echo $lastPostId; ?>">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo $row['content']; ?></p> <!-- Always render content as HTML -->
                        <hr>
                        <p>Date: <?php echo $row['date']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="box">
                    <h3>404 - Page Not Found</h3>
                    <p>Sorry, the page you are looking for does not exist.</p>
                </div>
            <?php endif; ?>

            <div id="load-container">
    <?php if ($hasContent && $offset + $limit < $totalPosts): ?>
        <form method="get" action="index.php#post-<?php echo $lastPostId; ?>">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
            <?php if ($sub): ?>
                <input type="hidden" name="sub" value="<?php echo htmlspecialchars($sub); ?>">
            <?php endif; ?>
            <input type="hidden" name="offset" value="<?php echo $offset + $limit; ?>">
            <button type="submit">Load More (<?php echo min($offset + $limit, $totalPosts) . " out of " . $totalPosts; ?>)</button>
        </form>
    <?php endif; ?>
</div>
        </div>
        
        <!-- Right Sidebar -->
        <?php if ($rightSidebarHasItems): ?>
            <div class="sidebar-right">
                <?php get_sidebar($db, 'right', $page, $sub); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        &copy; 2025 Teleco.ch. All Rights Reserved.
    </footer>
</body>
</html>