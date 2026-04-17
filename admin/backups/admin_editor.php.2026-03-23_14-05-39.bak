<?php
require_once 'config.php';
requireLogin();

$file = $_GET['file'] ?? '';
$filePath = safePath($file);

// Datei speichern (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $saveFile = $data['file'] ?? '';
    $content = $data['content'] ?? '';

    $savePath = safePath($saveFile);
    // Falls Datei noch nicht existiert, safeNewPath verwenden
    if ($savePath === false) {
        $savePath = safeNewPath($saveFile);
    }

    if ($savePath === false || !isEditable(basename($savePath))) {
        echo json_encode(['success' => false, 'message' => 'Ungültige Datei.']);
        exit;
    }

    // Verzeichnis erstellen falls noetig
    $dir = dirname($savePath);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    // Backup erstellen (nur wenn Datei existiert)
    $backupDir = __DIR__ . '/backups';
    if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
    $backupName = str_replace(['/', '\\'], '_', $saveFile) . '.' . date('Y-m-d_H-i-s') . '.bak';
    if (is_file($savePath)) copy($savePath, $backupDir . '/' . $backupName);

    // Datei speichern
    if (file_put_contents($savePath, $content) !== false) {
        echo json_encode(['success' => true, 'message' => 'Datei gespeichert!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern.']);
    }
    exit;
}

// Prüfen ob Datei existiert und editierbar ist
if ($filePath === false || !is_file($filePath) || !isEditable(basename($filePath))) {
    header('Location: files.php');
    exit;
}

$content = file_get_contents($filePath);
$extension = getExtension(basename($filePath));
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin – Editor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar__brand">
            <img src="../images/logo-screensolutions-white-ki.png" alt="screensolutions" class="navbar__logo">
            <span class="navbar__title">Admin</span>
        </div>
        <div class="navbar__menu">
            <a href="dashboard.php" class="navbar__link">🏠 Dashboard</a>
            <a href="files.php" class="navbar__link">📁 Dateien</a>
            <a href="files.php?dir=images" class="navbar__link">🖼 Bilder</a>
            <a href="settings.php" class="navbar__link">⚙️ Einstellungen</a>
            <a href="../index.html" class="navbar__link" target="_blank">🌐 Website</a>
            <a href="logout.php" class="navbar__link navbar__link--logout">⏻ Logout</a>
        </div>
    </nav>

    <div class="content">
        <h1>Editor</h1>

        <div class="editor-wrap">
            <div class="editor-toolbar">
                <div class="editor-toolbar__info">
                    📝 <strong><?= htmlspecialchars($file) ?></strong>
                    <span style="margin-left:1rem;color:#999;font-size:0.8rem;"><?= strtoupper($extension) ?> · <?= formatSize(strlen($content)) ?></span>
                </div>
                <div class="editor-toolbar__actions">
                    <a href="files.php<?= dirname($file) && dirname($file) !== '.' ? '?dir=' . urlencode(dirname($file)) : '' ?>" class="btn-back">← Zurück</a>
                    <button onclick="saveFile()" class="btn-save" id="btn-save">💾 Speichern</button>
                </div>
            </div>
            <textarea id="code-editor" spellcheck="false"><?= htmlspecialchars($content) ?></textarea>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        const editor = document.getElementById('code-editor');
        const toast = document.getElementById('toast');
        const btnSave = document.getElementById('btn-save');
        const currentFile = <?= json_encode($file) ?>;
        let originalContent = editor.value;
        let saving = false;

        // Tab-Taste im Editor
        editor.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 2;
            }
            // Ctrl+S / Cmd+S zum Speichern
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveFile();
            }
        });

        function showToast(message, type) {
            toast.textContent = message;
            toast.className = 'toast toast--' + type + ' toast--visible';
            setTimeout(() => { toast.className = 'toast'; }, 3000);
        }

        function saveFile() {
            if (saving) return;
            saving = true;
            btnSave.textContent = '⏳ Speichern...';
            btnSave.disabled = true;

            fetch('editor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    file: currentFile,
                    content: editor.value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    originalContent = editor.value;
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(() => {
                showToast('Verbindungsfehler!', 'error');
            })
            .finally(() => {
                saving = false;
                btnSave.textContent = '💾 Speichern';
                btnSave.disabled = false;
            });
        }

        // Warnung bei ungespeicherten Änderungen
        window.addEventListener('beforeunload', function(e) {
            if (editor.value !== originalContent) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>
