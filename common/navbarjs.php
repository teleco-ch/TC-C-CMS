<?php
function render_navbar_js($db) {
    // Fetch navbar items from the database and render them for behindertes dreckiges js navbar ... ich bin mal fullstack php dev gsi vor 5 jahr und jetzt machi das hier... 🤡
    $navbarQuery = $db->query("SELECT * FROM navbar ORDER BY id");
    $navbarItems = [];
    while ($row = $navbarQuery->fetchArray(SQLITE3_ASSOC)) {
        $navbarItems[] = $row;
    }

    echo '<nav class="navbar">';
    echo '<div class="navbar-container">';
    echo '<div class="navbar-left">';
    foreach ($navbarItems as $item) {
        $link = htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($item['type'] === 'logo') {
            echo '<img class="navbar-logo" src="' . $link . '" alt="' . $name . '">';
        } elseif ($item['type'] === 'title') {
            echo '<a href="index.php" class="navbar-title">' . $name . '</a>';
        } elseif ($item['align'] === 'left' && $item['type'] === 'link') {
            echo '<a class="navbar-link" href="' . $link . '">' . $name . '</a>';
        } elseif ($item['align'] === 'left' && $item['type'] === 'button') {
            echo '<button class="buttonify" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
        } elseif ($item['align'] === 'left' && $item['type'] === 'text field') {
            echo '<input type="text" placeholder="' . $name . '">';
        } elseif ($item['align'] === 'left' && $item['type'] === 'search') {
            echo '<input type="search" placeholder="' . $name . '">';
        } elseif ($item['align'] === 'left' && $item['type'] === 'drop down') {
            echo '<div class="navbar-dropdown">';
            echo '<button class="navbar-dropbtn" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
            echo '<div class="navbar-dropdown-content">';
            foreach ($navbarItems as $dropdownItem) {
                if ($dropdownItem['type'] === 'drop down entry' && $dropdownItem['name'] === $item['name']) {
                    $dropdownLink = htmlspecialchars($dropdownItem['link'] ?? '', ENT_QUOTES, 'UTF-8');
                    $dropdownLinkText = htmlspecialchars($dropdownItem['link_text'] ?? '', ENT_QUOTES, 'UTF-8');
                    echo '<a class="dropdown-link" href="' . $dropdownLink . '">' . $dropdownLinkText . '</a>';
                }
            }
            echo '</div>';
            echo '</div>';
        }
    }
    echo '</div>';

    echo '<div class="navbar-center">';
    foreach ($navbarItems as $item) {
        $link = htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($item['align'] === 'center') {
            if ($item['type'] === 'link') {
                echo '<a class="navbar-link" href="' . $link . '">' . $name . '</a>';
            } elseif ($item['type'] === 'button') {
                echo '<button class="buttonify" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
            } elseif ($item['type'] === 'text field') {
                echo '<input type="text" placeholder="' . $name . '">';
            } elseif ($item['type'] === 'search') {
                echo '<input type="search" placeholder="' . $name . '">';
            } elseif ($item['type'] === 'drop down') {
                echo '<div class="navbar-dropdown">';
                echo '<button class="navbar-dropbtn" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
                echo '<div class="navbar-dropdown-content">';
                foreach ($navbarItems as $dropdownItem) {
                    if ($dropdownItem['type'] === 'drop down entry' && $dropdownItem['name'] === $item['name']) {
                        $dropdownLink = htmlspecialchars($dropdownItem['link'] ?? '', ENT_QUOTES, 'UTF-8');
                        $dropdownLinkText = htmlspecialchars($dropdownItem['link_text'] ?? '', ENT_QUOTES, 'UTF-8');
                        echo '<a class="dropdown-link" href="' . $dropdownLink . '">' . $dropdownLinkText . '</a>';
                    }
                }
                echo '</div>';
                echo '</div>';
            }
        }
    }
    echo '</div>';

    echo '<div class="navbar-right">';
    foreach ($navbarItems as $item) {
        $link = htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($item['align'] === 'right') {
            if ($item['type'] === 'link') {
                echo '<a class="navbar-link" href="' . $link . '">' . $name . '</a>';
            } elseif ($item['type'] === 'button') {
                echo '<button class="buttonify" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
            } elseif ($item['type'] === 'text field') {
                echo '<input type="text" placeholder="' . $name . '">';
            } elseif ($item['type'] === 'search') {
                echo '<input type="search" placeholder="' . $name . '">';
            } elseif ($item['type'] === 'drop down') {
                echo '<div class="navbar-dropdown">';
                echo '<button class="navbar-dropbtn" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
                echo '<div class="navbar-dropdown-content">';
                foreach ($navbarItems as $dropdownItem) {
                    if ($dropdownItem['type'] === 'drop down entry' && $dropdownItem['name'] === $item['name']) {
                        $dropdownLink = htmlspecialchars($dropdownItem['link'] ?? '', ENT_QUOTES, 'UTF-8');
                        $dropdownLinkText = htmlspecialchars($dropdownItem['link_text'] ?? '', ENT_QUOTES, 'UTF-8');
                        echo '<a class="dropdown-link" href="' . $dropdownLink . '">' . $dropdownLinkText . '</a>';
                    }
                }
                echo '</div>';
                echo '</div>';
            }
        }
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="navbar-mobile">';
    echo '<div class="navbar-left">';
    foreach ($navbarItems as $item) {
        $link = htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($item['type'] === 'logo') {
            echo '<img class="navbar-logo" src="' . $link . '" alt="' . $name . '">';
        } elseif ($item['type'] === 'title') {
            echo '<a href="index.php" class="navbar-title">' . $name . '</a>';
        }
    }
    echo '</div>';
    echo '<button class="navbar-toggle" onclick="toggleNavbar()">☰</button>';
    echo '</div>';

    echo '<div class="mobile-navbar-menu">';
    foreach ($navbarItems as $item) {
        $link = htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($item['type'] === 'link') {
            echo '<a class="navbar-link bruh-why" href="' . $link . '">' . $name . '</a>';
        } elseif ($item['type'] === 'drop down') {
            echo '<span class="mobile-navbar-dropdown" onclick="location.href=\'' . $link . '\'">' . $name . '</span>';
        } elseif ($item['type'] === 'drop down entry') {
            $dropdownLink = htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8');
            $dropdownLinkText = htmlspecialchars($item['link_text'] ?? '', ENT_QUOTES, 'UTF-8');
            echo '<a class="dropdown-link" href="' . $dropdownLink . '" class="mobile-dropdown-entry">↳ ' . $dropdownLinkText . '</a>';
        } elseif ($item['type'] === 'button') {
            echo '<button class="buttonify" onclick="location.href=\'' . $link . '\'">' . $name . '</button>';
        } elseif ($item['type'] === 'text field') {
            echo '<input type="text" placeholder="' . $name . '">';
        } elseif ($item['type'] === 'search') {
            echo '<input type="search" placeholder="' . $name . '">';
        }
    }
    echo '</div>';
    echo '</nav>';
}
?>