<?php
require_once 'config.php';
requireLogin();

$message = '';
$messageType = '';

// Template fuer neue Seite
function getPageTemplate($title, $description, $heading, $filename) {
    $navLinks = [
        'index.php' => 'KI',
        'fotografie.php' => 'Fotografie',
        'webdesign.php' => 'Webdesign',
        'agentur.php' => 'Agentur',
    'blog.php' => 'Blog'
    ];
    
    $navHtml = '';
    foreach ($navLinks as $href => $label) {
        $cleanHref = ($href === 'index.php') ? '/' : '/' . str_replace('.php', '', $href);
        $active = ($href === $filename) ? ' nav__link--active' : '';
        $navHtml .= '                    <a href="' . $cleanHref . '" class="nav__link' . $active . '">' . $label . '</a>' . "\n";
    }
    
    $slug = pathinfo($filename, PATHINFO_FILENAME);
    $url = 'https://screensolutions.ch/' . str_replace('.php', '', $filename);
    
    return '<?php
\$pageTitle = \'' . htmlspecialchars($title) . ' | screensolutions\';
\$pageDescription = \'' . htmlspecialchars($title) . ' - screensolutions\';
\$pageCanonical = \'https://screensolutions.ch/' . str_replace('.php', '', $filename) . '\';
\$activePage = \'\';
include \'_header.php\';
?>

    <section class="hero hero--firma" style="min-height: 30vh; padding: 120px 0 40px;">
        <div class="hero__content">
            <h1>' . htmlspecialchars($title) . '</h1>
        </div>
    </section>

    <section class="section--dark">
        <div class="container">
            <p>Inhalt kommt hier...</p>
        </div>
    </section>

    <section class="contact">
        <div class="container">
            <h2>Kontakt</h2>
            <p>Bereit fuer den naechsten Schritt?</p>
            <a href="mailto:info@screensolutions.ch" class="btn btn--primary">Kontakt aufnehmen</a>
        </div>
    </section>

<?php include \'_footer.php\'; ?>'
}

// POST: Seite erstellen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = trim($_POST['filename'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $heading = trim($_POST['heading'] ?? '');
    $addToNav = isset($_POST['add_to_nav']);
    
    // Validierung
    if (empty($filename) || empty($title) || empty($heading)) {
        $message = 'Bitte alle Pflichtfelder ausf&uuml;llen.';
        $messageType = 'error';
    } else {
        // Dateiname bereinigen
        $filename = preg_replace('/[^a-z0-9-]/', '', strtolower($filename));
        if (empty($filename)) {
            $message = 'Ung&uuml;ltiger Dateiname.';
            $messageType = 'error';
        } else {
            $filename .= '.php';
            $filepath = BASE_DIR . '/' . $filename;
            
            if (file_exists($filepath)) {
                $message = 'Die Datei <strong>' . htmlspecialchars($filename) . '</strong> existiert bereits.';
                $messageType = 'error';
            } else {
                // Seite aus Template erstellen
                $html = getPageTemplate($title, $description, $heading, $filename);
                
                if (file_put_contents($filepath, $html)) {
                    // Navigation auf anderen Seiten aktualisieren wenn gewuenscht
                    if ($addToNav) {
                        $navItem = $title;
                        // Kurzform fuer Navigation
                        if (strlen($navItem) > 15) {
                            $navItem = explode(' ', $navItem)[0];
                        }
                        updateNavigation($filename, $navItem);
                    }
                    
                    // Sitemap aktualisieren
                    updateSitemap($filename);
                    
                    $message = 'Seite <strong>' . htmlspecialchars($filename) . '</strong> erfolgreich erstellt!';
                    $messageType = 'success';
                } else {
                    $message = 'Fehler beim Erstellen der Datei.';
                    $messageType = 'error';
                }
            }
        }
    }
}

// Navigation auf bestehenden Seiten aktualisieren
function updateNavigation($newFile, $navLabel) {
    $pages = glob(BASE_DIR . '/*.php');
    foreach ($pages as $page) {
        $basename = basename($page);
        if ($basename === $newFile || $basename === 'danke.php') continue;
        
        $content = file_get_contents($page);
        if ($content === false) continue;
        
        // Neuen Nav-Link vor </nav> einfuegen
        $newLink = '                    <a href="/' . str_replace('.php', '', $newFile) . '" class="nav__link">' . htmlspecialchars($navLabel) . '</a>';
        
        // Pruefen ob der Link schon existiert
        if (strpos($content, 'href="' . $newFile . '"') !== false) continue;
        
        // Vor dem letzten </nav> einfuegen
        $navPos = strrpos($content, '</nav>');
        if ($navPos !== false) {
            $content = substr($content, 0, $navPos) . $newLink . "\n                " . substr($content, $navPos);
            file_put_contents($page, $content);
        }
    }
}

// Sitemap aktualisieren
function updateSitemap($newFile) {
    $sitemapPath = BASE_DIR . '/sitemap.xml';
    if (!file_exists($sitemapPath)) return;
    
    $content = file_get_contents($sitemapPath);
    $newEntry = "  <url>\n    <loc>https://screensolutions.ch/" . $newFile . "</loc>\n    <lastmod>" . date('Y-m-d') . "</lastmod>\n    <changefreq>monthly</changefreq>\n    <priority>0.7</priority>\n  </url>\n";
    
    $content = str_replace('</urlset>', $newEntry . '</urlset>', $content);
    file_put_contents($sitemapPath, $content);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin &ndash; Neue Seite</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar__logo">
            <img src="../images/logo-screensolutions-white-ki.png" alt="screensolutions">
            <span>Admin</span>
        </a>
        <div class="navbar__links">
            <a href="dashboard.php" class="nav-btn">🏠 Dashboard</a>
            <a href="files.php" class="nav-btn">📁 Dateien</a>
            <a href="files.php?dir=images" class="nav-btn">🖼 Bilder</a>
            <a href="settings.php" class="nav-btn">⚙️ Einstellungen</a>
            <a href="/" class="nav-btn" target="_blank">🌐 Website</a>
            <a href="logout.php" class="nav-btn nav-btn--logout">⏻ Logout</a>
        </div>
    </nav>

    <div class="content">
        <div class="page-header">
            <div>
                <h1>Neue Seite erstellen</h1>
                <p class="subtitle">Erstelle eine neue HTML-Seite mit dem Standard-Template.</p>
            </div>
            <a href="dashboard.php" class="btn btn--secondary">&larr; Zur&uuml;ck</a>
        </div>

        <?php if ($message): ?>
            <div class="msg msg--<?= $messageType ?>">
                <?= $message ?>
                <?php if ($messageType === 'success'): ?>
                    <div style="margin-top: 10px;">
                        <a href="editor.php?file=<?= urlencode($filename) ?>" class="btn btn--primary">Im Editor &ouml;ffnen</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 700px;">
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="filename">Dateiname *</label>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="text" id="filename" name="filename" placeholder="z.B. leistungen" 
                               pattern="[a-z0-9-]+" title="Nur Kleinbuchstaben, Zahlen und Bindestriche"
                               value="<?= htmlspecialchars($_POST['filename'] ?? '') ?>" required
                               style="flex:1;">
                        <span style="color:#666; font-weight:500;">.php</span>
                    </div>
                    <small style="color:#888;">Nur Kleinbuchstaben, Zahlen und Bindestriche. Wird zu dateiname.php</small>
                </div>

                <div class="form-group">
                    <label for="title">Seitentitel (SEO) *</label>
                    <input type="text" id="title" name="title" placeholder="z.B. Unsere Leistungen" 
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required
                           maxlength="60">
                    <small style="color:#888;">Erscheint im Browser-Tab und in Google. Max. 60 Zeichen.</small>
                </div>

                <div class="form-group">
                    <label for="description">Meta-Beschreibung (SEO)</label>
                    <textarea id="description" name="description" rows="2" 
                              placeholder="Kurze Beschreibung f&uuml;r Google (max. 160 Zeichen)"
                              maxlength="160"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <small style="color:#888;">Wird in den Google-Suchergebnissen angezeigt. Max. 160 Zeichen.</small>
                </div>

                <div class="form-group">
                    <label for="heading">&Uuml;berschrift (H1) *</label>
                    <input type="text" id="heading" name="heading" placeholder="z.B. Das bieten wir an" 
                           value="<?= htmlspecialchars($_POST['heading'] ?? '') ?>" required>
                    <small style="color:#888;">Die Haupt&uuml;berschrift auf der Seite (Hero-Bereich).</small>
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="add_to_nav" value="1">
                        <span>Zur Navigation hinzuf&uuml;gen</span>
                    </label>
                    <small style="color:#888;">F&uuml;gt einen Link zu dieser Seite in die Navigation aller bestehenden Seiten ein.</small>
                </div>

                <button type="submit" class="btn btn--primary" style="margin-top:10px;">📄 Seite erstellen</button>
            </form>
        </div>

        <div class="card" style="max-width: 700px; margin-top: 20px;">
            <h3 style="margin-bottom:10px;">ℹ️ Hinweis</h3>
            <p style="color:#666; line-height:1.6;">
                Die neue Seite wird mit dem Standard-Template erstellt (Header, Navigation, Hero, Kontaktformular, Footer). 
                Nach dem Erstellen kannst du den Inhalt im <strong>Editor</strong> anpassen. 
                Alle SEO-Tags (Open Graph, Twitter, Canonical) werden automatisch gesetzt.
            </p>
        </div>
    </div>
</body>
</html>