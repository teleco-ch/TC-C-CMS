<?php
if ($navbarTableExists && $navbarHasEntries) {
    // Fetch navbar items from the database and render them for nicht behindertes navbar ohni js <3
    $navbarItems = [];
    $navbarResult = $db->query("SELECT * FROM navbar ORDER BY id");
    while ($row = $navbarResult->fetchArray(SQLITE3_ASSOC)) {
        $navbarItems[] = $row;
    }

    // Function to render the navbar items im grosse navbar für normali mensche
    function render_navbar($items, $currentPage) {
        $leftItems = array_filter($items, fn($item) => $item['align'] === 'left');
        $centerItems = array_filter($items, fn($item) => $item['align'] === 'center');
        $rightItems = array_filter($items, fn($item) => $item['align'] === 'right');

        echo '<div class="navbar-left">';
        render_navbar_items($leftItems, $currentPage);
        echo '</div>';
        echo '<div class="navbar-center">';
        render_navbar_items($centerItems, $currentPage);
        echo '</div>';
        echo '<div class="navbar-right">';
        render_navbar_items($rightItems, $currentPage);
        echo '</div>';
    }

    function render_navbar_items($items, $currentPage) {
        foreach ($items as $item) {
            $activeClass = ($item['link'] == "?page=$currentPage") ? ' class="active"' : '';
            switch ($item['type']) {
                case 'title':
                    echo '<span class="navbar-title">' . htmlspecialchars($item['name']) . '</span>';
                    break;
                case 'link':
                    echo '<a href="' . htmlspecialchars($item['link']) . '"' . $activeClass . '>' . htmlspecialchars($item['name']) . '</a>';
                    break;
                case 'drop down':
                    echo '<div class="navbar-dropdown">';
                    echo '<a class="navbar-dropbtn" href="#">' . htmlspecialchars($item['name']) . '</a>';
                    echo '<div class="navbar-dropdown-content">';
                    foreach ($items as $dropdownItem) {
                        if ($dropdownItem['type'] === 'drop down entry' && $dropdownItem['name'] === $item['name']) {
                            echo '<a href="' . htmlspecialchars($dropdownItem['link']) . '">' . htmlspecialchars($dropdownItem['link_text']) . '</a>';
                        }
                    }
                    echo '</div>';
                    echo '</div>';
                    break;
                case 'text field':
                    echo '<input type="text" placeholder="' . htmlspecialchars($item['name']) . '">';
                    break;
                case 'button':
                    echo '<button onclick="location.href=\'' . htmlspecialchars($item['link']) . '\'">' . htmlspecialchars($item['name']) . '</button>';
                    break;
                case 'custom':
                    echo $item['link_text'];
                    break;
                case 'logo':
                    echo '<img src="' . htmlspecialchars($item['link']) . '" alt="' . htmlspecialchars($item['name']) . '" class="navbar-logo">';
                    break;
                case 'search':
                    echo '<input type="search" placeholder="' . htmlspecialchars($item['name']) . '">';
                    break;
            }
        }
    }

    // Function to render the mobile navbar für behindertes hamburger menu
    function render_mobile_navbar($items, $menuExpanded) {
        echo '<div class="mobile-navbar-panel">';
        echo '<div class="navbar-header">';

        foreach ($items as $item) {
            if ($item['type'] === 'logo') {
                echo '<img src="' . htmlspecialchars($item['link']) . '" alt="' . htmlspecialchars($item['name']) . '" class="navbar-logo">';
            }
            if ($item['type'] === 'title') {
                echo '<span class="navbar-title">' . htmlspecialchars($item['name']) . '</span>';
            }
        }

        $toggleState = $menuExpanded ? 0 : 1;
        echo '<form method="get" action="" style="display:inline;">';
        echo '<input type="hidden" name="menu" value="' . $toggleState . '">';
        echo '<button type="submit" class="navbar-toggle">' . ($menuExpanded ? 'Collapse' : 'Expand') . '</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';

        // Separate the menu from the navbar panel will mir ned ganz normali mensche sind und euses menu mit post uf und zue gaht
        if ($menuExpanded) {
            echo '<div class="mobile-navbar-menu">';
            render_navbar_items(array_filter($items, fn($item) => $item['type'] !== 'logo' && $item['type'] !== 'title'), '');
            echo '</div>';
        }
    }
}
?>