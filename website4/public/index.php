<?php
// loads the config
$config = include(__DIR__ . '/../../config.php');

// go set your id here otehrwise ur site wont load the stuff it needs
$siteId = 4;

// website go search what stuffs it need to load
$websiteConfig = array_filter($config, function($site) use ($siteId) {
    return isset($site['id']) && $site['id'] === $siteId;
});
$websiteConfig = reset($websiteConfig);

// include the components so the site actually gets rendered
foreach ($websiteConfig['components'] as $component) {
    include($component);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dialon - <?php echo ucfirst($page); ?></title>
    <meta name="description" content="<?php echo $metaDescription; ?>">
    <link rel="stylesheet" href="assets/css/dialon.css">
    <?php if (!$navbarTableExists || $navbarHasEntries): ?>
        <link rel="stylesheet" href="assets/css/navbar.css">
    <?php endif; ?>
    <?php if (!$navbarTableExists || !$navbarHasEntries): ?>
        <link rel="stylesheet" href="assets/css/hamburger.css">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/css/extras.css">

    <style>
    <?php if ($navbarTableExists && $navbarHasEntries): ?>
    .chat-toggle-btn {
        bottom: 20px;
    }
    <?php endif; ?>
    </style>
</head>

<body>
    <header>
        <div class="header-content" <?php if ($navbarTableExists && $navbarHasEntries) echo 'style="display:none;"'; ?>>
            <img src="assets/img/dialon.png" alt="Teleco Logo" class="logo">
            <h1>dialon.ch</h1>
        </div>
    </header>
    
    <?php if ($navbarTableExists && $navbarHasEntries): ?>
        <?php render_navbar_js($db); ?>
    <?php endif; ?>

    <?php if (!$navbarTableExists || !$navbarHasEntries): ?>
    <!-- burger king chnopf -->
    <button class="hamburger-menu" onclick="toggleMenu()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24" aria-hidden="true">
            <path d="M0 88C0 74.7 10.7 64 24 64H424c13.3 0 24 10.7 24 24s-10.7 24-24 24H24C10.7 112 0 101.3 0 88zM0 248C0 234.7 10.7 224 24 224H424c13.3 0 24 10.7 24 24s-10.7 24-24 24H24C10.7 272 0 261.3 0 248zM424 448H24c-13.3 0-24-10.7-24-24s10.7-24 24-24H424c13.3 0 24 10.7 24 24s-10.7 24-24 24z"/>
        </svg>
    </button>
    <!-- burger king -->
    <div class="menu-overlay">
        <div class="menu-content">
            <h2>Navigation</h2>
            <?php get_sidebar($db, 'left', $page, $sub); ?>
            <?php get_sidebar($db, 'right', $page, $sub); ?>
        </div>
    </div>
<?php endif; ?>

    <div class="container" style="<?php echo $leftSidebarHasItems || $rightSidebarHasItems ? '' : 'margin-left: 0px; margin-right: 0px; flex-wrap: nowrap; margin-top: 0px; margin-bottom: 0px; max-width: 100%; box-sizing: border-box; padding: 10px;'; ?>">
        <!-- dis da left sidebar -->
        <?php if ($leftSidebarHasItems): ?>
            <div class="sidebar">
                <?php get_sidebar($db, 'left', $page, $sub); ?>
            </div>
        <?php endif; ?>

        <!-- dis da main content row down the middle -->
        <div class="content" id="content" style="<?php echo $leftSidebarHasItems || $rightSidebarHasItems ? '' : 'width: calc(100%);'; ?>">
            <!-- my terrible way of doing breadcrumbs -->
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
            <?php if ($hasContent): ?>
                <?php
                $result = $query->execute();
                $lastPostId = 0;
                while ($row = $result->fetchArray()):
                    $lastPostId++;
                ?>
                    <div class="box" id="post-<?php echo $lastPostId; ?>">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo $row['content']; ?></p> <!-- used to have a function to disable and enable rendering html in post, got very annoyed with its db not doing what its supposed to do so now it loads html eitherway and html_custom is nolonger a thing im das datenbank -->
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
                        <button class="load-more-button" type="submit">Load More (<?php echo min($offset + $limit, $totalPosts) . " out of " . $totalPosts; ?>)</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- dis da right seitenleisteh! -->
        <?php if ($rightSidebarHasItems): ?>
            <div class="sidebar-right">
                <?php get_sidebar($db, 'right', $page, $sub); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- cat support meow meow meow meow -->
    <button class="chat-toggle-btn" onclick="toggleChat()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24" aria-hidden="true">
            <path d="M192 208c0-17.7-14.3-32-32-32h-16c-35.4 0-64 28.7-64 64v48c0 35.4 28.7 64 64 64h16c17.7 0 32-14.3 32-32V208zm176 144c35.4 0 64-28.7 64-64v-48c0-35.4-28.7-64-64-64h-16c-17.7 0-32 14.3-32 32v112c0 17.7 14.3 32 32 32h16zM256 0C113.2 0 4.6 118.8 0 256v16c0 8.8 7.2 16 16 16h16c8.8 0 16-7.2 16-16v-16c0-114.7 93.3-208 208-208s208 93.3 208 208h-.1c.1 2.4 .1 165.7 .1 165.7 0 23.4-18.9 42.3-42.3 42.3H320c0-26.5-21.5-48-48-48h-32c-26.5 0-48 21.5-48 48s21.5 48 48 48h181.7c49.9 0 90.3-40.4 90.3-90.3V256C507.4 118.8 398.8 0 256 0z"/>
        </svg>
    </button>

    <!-- mau mau support dumpster -->
    <div class="chat-container" id="chat-container">
        <div class="chat-header">
            Virtual Assistant Chat
            <button class="chat-close-btn" onclick="toggleChat()">✖</button> <!-- mau mau tschau -->
        </div>
        <div class="chat-log" id="chat-log"></div>
        <div class="chat-input-container">
            <input type="text" id="user-input" class="chat-input" style="background-color: var(--secondary-color);" placeholder="Type your message here...">
            <button class="send-btn" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <!-- js sache sind hier und so  -->
    <script src="assets/js/chat.js"></script>
    <script src="assets/js/menu.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.querySelector('.mobile-navbar-menu');
    const toggleButton = document.querySelector('.navbar-toggle');

    window.toggleNavbar = function() {
        if (!mobileMenu) return;

        if (mobileMenu.classList.contains('open')) {
            mobileMenu.style.maxHeight = "0px";
            mobileMenu.classList.remove('open');
        } else {
            mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";
            mobileMenu.classList.add('open');
        }
    };

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileMenu.classList.contains('open')) {
            mobileMenu.classList.remove('open');
            mobileMenu.style.maxHeight = "0px";
        }
    });
});
</script>

</body>
</html>
