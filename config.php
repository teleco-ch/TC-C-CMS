<?php
return [
    [
        'id' => 1,
        'name' => 'Demo Site 1 Sidebar : einfach geil',
        'database' => __DIR__ . '/website/content.db',
        'folder' => 'website',
        'backup_folder' => 'dbbackups/website',
        'components' => [
            __DIR__ . '/common/functions.php',
            __DIR__ . '/common/navbar.php',
        ],
    ],
    [
        'id' => 2,
        'name' => 'Dome Site 2 Navbar : fick geil jaman"',
        'database' => __DIR__ . '/website2/content2.db',
        'folder' => 'website2',
        'backup_folder' => 'dbbackups/website2',
        'components' => [
            __DIR__ . '/common/functions.php',
            __DIR__ . '/common/navbar.php',
        ],
    ],    
    [
        'id' => 3,
        'name' => 'Dome Site 3 Navbar w JS : geh kotzen',
        'database' => __DIR__ . '/website3/content3.db',
        'folder' => 'website3',
        'backup_folder' => 'dbbackups/website3',
        'components' => [
            __DIR__ . '/common/functions.php',
            __DIR__ . '/common/navbarjs.php',
        ],
    ],
    [
        'id' => 4,
        'name' => 'Demo Site 4 Sidebar w JS flatterschiss',
        'database' => __DIR__ . '/website4/content4.db',
        'folder' => 'website4',
        'backup_folder' => 'dbbackups/website4',
        'components' => [
            __DIR__ . '/common/functions.php',
            __DIR__ . '/common/navbarjs.php',
        ],
    ]
];
?>
