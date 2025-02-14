<?php
// sqlite datehbank ahfigge
$db = new SQLite3($websiteConfig['database']);

// so luegele ob mer navbar sache mache oder ob d'websihte wahrschiinlich so sidebar style wird
$navbarTableExists = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='navbar'");
$navbarHasEntries = $db->querySingle("SELECT COUNT(*) FROM navbar") > 0;

// mis wunderschöne url handling ✨
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'index';
$sub = isset($_GET['sub']) ? htmlspecialchars($_GET['sub']) : null;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 10; // Number of posts per load
$menuExpanded = isset($_GET['menu']) ? (bool)$_GET['menu'] : false;

// overflow protection oder so cha kei cybersecurity
if ($offset < 0) {
    $offset = 0;
}

// schlimmste funktion eveer für breadcrumbs aber es funktioniert leider
function get_full_path($db, $page, $sub = null) {
    $path = [];
    $current_page = $sub ?: $page; // If sub is set of posts benutzen sub suscht halt page

    while ($current_page) {
        $query = $db->prepare('SELECT parent, title, page FROM pages WHERE page = :page');
        $query->bindValue(':page', $current_page, SQLITE3_TEXT);
        $result = $query->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result) {
            array_unshift($path, [
                'title' => $result['title'], 
                'page' => $result['page']
            ]);
            $current_page = $result['parent']; // eltere vor d'füess schiebe
        } else {
            break;
        }
    }
    return $path;
}

$breadcrumbs = get_full_path($db, $page, $sub);

// siihte metadata mache und so
$metaQuery = $db->prepare('SELECT meta_description, parent FROM pages WHERE page = :page AND (parent IS NULL OR parent = :parent)');
$metaQuery->bindValue(':page', $page, SQLITE3_TEXT);
$metaQuery->bindValue(':parent', $sub, SQLITE3_TEXT);
$metaResult = $metaQuery->execute()->fetchArray(SQLITE3_ASSOC);
$metaDescription = $metaResult ? htmlspecialchars($metaResult['meta_description'] ?? '', ENT_QUOTES, 'UTF-8') : 'Default description of the website.';

// mal gügsle wiviel posts es git hmmm 🤔 lol
$countQuery = $db->prepare('SELECT COUNT(*) as total FROM content WHERE page = :page AND (parent IS NULL OR parent = :sub)');
$countQuery->bindValue(':page', $page, SQLITE3_TEXT);
$countQuery->bindValue(':sub', $sub, SQLITE3_TEXT);
$totalPosts = $countQuery->execute()->fetchArray(SQLITE3_ASSOC)['total'];

// offset dörf nid grösser sii als total posts will susch depressione
if ($offset > $totalPosts) {
    $offset = $totalPosts - ($totalPosts % $limit);
}

// ALLI POSTS ABRÜefe bitte ned ahlange susch hühl ich
$query = $db->prepare('SELECT title, content, date FROM content WHERE page = :page ORDER BY date DESC LIMIT :limit OFFSET :offset');
$query->bindValue(':page', $sub ? $sub : $page, SQLITE3_TEXT);
$query->bindValue(':limit', $offset + $limit, SQLITE3_INTEGER);
$query->bindValue(':offset', 0, SQLITE3_INTEGER); // immer alli posts holeh will was isch normali site pagination
$result = $query->execute();

// het page überhaupt was drufeh oder so ?
$hasContent = $result->fetchArray() !== false;
if ($hasContent) {
    // Reset the result pointer
    $result->reset();
}

// haben das linke sidebar sache?
$leftSidebarHasItems = $db->querySingle("SELECT COUNT(*) FROM sidebar WHERE position='left'") > 0;

// haben das rechte sidebar sache?
$rightSidebarHasItems = $db->querySingle("SELECT COUNT(*) FROM sidebar WHERE position='right'") > 0;

// das isch fett schaisse und ich ha kein plan ki het gseit das fixts und so ich ha kei lust meh gha ha nach 2 stund 😭
function sameQueryParams(string $url1, string $url2): bool {
    $parsed1 = parse_url($url1);
    $parsed2 = parse_url($url2);

    $params1 = [];
    $params2 = [];
    if (!empty($parsed1['query'])) {
        parse_str($parsed1['query'], $params1);
    }
    if (!empty($parsed2['query'])) {
        parse_str($parsed2['query'], $params2);
    }

    return $params1 == $params2;
}

// isch das total behindert und komplett losti ahgangs wiihs ? ja ... aber es funktioniert leider echt kei bock meh fr fr die grüene sind schuld
function get_sidebar($db, $position, $page, $sub) {
    $query = $db->prepare('
        SELECT title, link, link_text
        FROM sidebar
        WHERE position = :position
        ORDER BY id
    ');
    $query->bindValue(':position', $position, SQLITE3_TEXT);
    $result = $query->execute();

    if (!$result || !$result->fetchArray(SQLITE3_ASSOC)) {
        return; // effizienz versuech will die ganz funktion müll isch ach mannn 😭
    }
    $result->reset();
    $lastTitle = "";
    // Bob der baumeister spileh und $activeLink zemme bastle
    $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? ''; // luege das ned null isch will susch motzts
    parse_str($queryString, $queryParams);

    $activeParts = ["?page=" . ($queryParams['page'] ?? '')];
    if (!empty($queryParams['sub'])) {
        if (is_array($queryParams['sub'])) {
            foreach ($queryParams['sub'] as $subParam) {
                $activeParts[] = "sub=" . $subParam;
            }
        } else {
            $activeParts[] = "sub=" . $queryParams['sub'];
        }
    }
    $activeLink = implode("&", $activeParts);

    $foundActive = false;

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // new post oh mein gott so spannend mached mer grad neue title
        if ($row['title'] !== $lastTitle) {
            if ($lastTitle !== "") {
                echo '</div>';
            }
            echo '<div class="box"><h3>' . htmlspecialchars($row['title']) . '</h3>';
            $lastTitle = $row['title'];
        }

        // externi links z'erchäne wär inteligent schickiert
        if (filter_var($row['link'], FILTER_VALIDATE_URL)) {
            echo '<p><a href="'
                 . htmlspecialchars($row['link'])
                 . '" target="_blank">'
                 . htmlspecialchars($row['link_text'])
                 . '</a></p>';
        } else {
            // robuster mache und so lönd mich ihn rueh
            $activeClass = '';
            if (!$foundActive && sameQueryParams($row['link'], $activeLink)) {
                $activeClass = 'active';
                $foundActive = true;
            }

            echo '<p><a href="'
                 . htmlspecialchars($row['link'], ENT_QUOTES, 'UTF-8')
                 . '" class="' . $activeClass . '">'
                 . htmlspecialchars($row['link_text'])
                 . '</a></p>';
        }
    }
    if ($lastTitle !== "") {
        echo '</div>';
    }
}
?>