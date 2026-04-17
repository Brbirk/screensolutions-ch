<?php
require_once 'config.php';
requireLogin();

$dir = $_GET['dir'] ?? '';
$fullPath = $dir ? safePath($dir) : BASE_DIR;

if ($fullPath === false) {
    header('Location: files.php');
    exit;
}

// Upload verarbeiten
$uploadMsg = '';
$uploadType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_file'])) {
    $file = $_FILES['upload_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        // Sonderzeichen entfernen, Leerzeichen durch Bindestrich ersetzen
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '-', $filename);
        $targetPath = $fullPath . '/' . $filename;

        // Prüfen ob Zielverzeichnis innerhalb BASE_DIR liegt
        $targetReal = realpath($fullPath);
        if ($targetReal !== false && strpos($targetReal, BASE_DIR) === 0) {
            // Doppelte Dateinamen vermeiden
            if (file_exists($targetPath)) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $base = pathinfo($filename, PATHINFO_FILENAME);
                $counter = 1;
                while (file_exists($targetPath)) {
                    $filename = $base . '-' . $counter . '.' . $ext;
                    $targetPath = $fullPath . '/' . $filename;
                    $counter++;
                }
            }

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $uploadMsg = '✅ "' . htmlspecialchars($filename) . '" erfolgreich hochgeladen!';
                $uploadType = 'success';
            } else {
                $uploadMsg = '❌ Fehler beim Hochladen.';
                $uploadType = 'error';
            }
        } else {
            $uploadMsg = '❌ Ungültiges Verzeichnis.';
            $uploadType = 'error';
        }
    } else {
        $uploadMsg = '❌ Upload-Fehler (Code: ' . $file['error'] . ')';
        $uploadType = 'error';
    }
}

// Verzeichnisinhalt lesen
$items = [];
$entries = scandir($fullPath);
$hasImages = false;

foreach ($entries as $entry) {
    if ($entry === '.' || $entry === '..') continue;
    if (in_array($entry, BLOCKED_DIRS)) continue;

    $itemPath = $fullPath . '/' . $entry;
    $relativePath = $dir ? $dir . '/' . $entry : $entry;
    $isImg = is_file($itemPath) && isImage($entry);
    if ($isImg) $hasImages = true;

    $items[] = [
        'name' => $entry,
        'path' => $relativePath,
        'is_dir' => is_dir($itemPath),
        'size' => is_file($itemPath) ? filesize($itemPath) : 0,
        'modified' => filemtime($itemPath),
        'editable' => is_file($itemPath) && isEditable($entry),
        'is_image' => $isImg,
        'extension' => getExtension($entry)
    ];
}

// Sortierung: Ordner zuerst, dann alphabetisch
usort($items, function($a, $b) {
    if ($a['is_dir'] !== $b['is_dir']) return $b['is_dir'] - $a['is_dir'];
    return strcasecmp($a['name'], $b['name']);
});

// Breadcrumb erstellen
$breadcrumbs = [['name' => 'Stammverzeichnis', 'path' => '']];
if ($dir) {
    $parts = explode('/', $dir);
    $current = '';
    foreach ($parts as $part) {
        $current .= ($current ? '/' : '') . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $current];
    }
}

// Anzeigemodus: gallery wenn Bilder vorhanden
$viewMode = isset($_GET['view']) ? $_GET['view'] : ($hasImages ? 'gallery' : 'list');
$imageItems = array_filter($items, fn($i) => $i['is_image']);
$nonImageItems = array_filter($items, fn($i) => !$i['is_image']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin – Dateien</title>
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
            <a href="files.php" class="navbar__link <?= !$hasImages ? 'navbar__link--active' : '' ?>">📁 Dateien</a>
            <a href="files.php?dir=images" class="navbar__link <?= $dir === 'images' ? 'navbar__link--active' : '' ?>">🖼 Bilder</a>
            <a href="settings.php" class="navbar__link">⚙️ Einstellungen</a>
            <a href="../index.html" class="navbar__link" target="_blank">🌐 Website</a>
            <a href="logout.php" class="navbar__link navbar__link--logout">⏻ Logout</a>
        </div>
    </nav>

    <div class="content">
        <div class="page-header">
            <h1>Dateien</h1>
            <div class="page-header__actions">
                <?php if ($hasImages): ?>
                <div class="view-toggle">
                    <a href="?dir=<?= urlencode($dir) ?>&view=gallery" class="view-toggle__btn <?= $viewMode === 'gallery' ? 'view-toggle__btn--active' : '' ?>" title="Galerie">🖼</a>
                    <a href="?dir=<?= urlencode($dir) ?>&view=list" class="view-toggle__btn <?= $viewMode === 'list' ? 'view-toggle__btn--active' : '' ?>" title="Liste">☰</a>
                </div>
                <?php endif; ?>
                <button class="btn-upload" onclick="document.getElementById('upload-area').classList.toggle('upload-area--visible')">📤 Hochladen</button>
            </div>
        </div>

        <?php if ($uploadMsg): ?>
        <div class="upload-msg upload-msg--<?= $uploadType ?>"><?= $uploadMsg ?></div>
        <?php endif; ?>

        <div class="upload-area" id="upload-area">
            <form method="POST" enctype="multipart/form-data" class="upload-form" id="upload-form">
                <div class="upload-dropzone" id="dropzone">
                    <div class="upload-dropzone__icon">📁</div>
                    <p class="upload-dropzone__text">Datei hierher ziehen oder <label for="file-input" class="upload-dropzone__label">durchsuchen</label></p>
                    <p class="upload-dropzone__hint">Max. 20 MB · Alle Dateitypen erlaubt</p>
                    <input type="file" name="upload_file" id="file-input" class="upload-dropzone__input">
                </div>
                <div class="upload-preview" id="upload-preview" style="display:none;">
                    <div class="upload-preview__info">
                        <span class="upload-preview__name" id="preview-name"></span>
                        <span class="upload-preview__size" id="preview-size"></span>
                    </div>
                    <img id="preview-img" class="upload-preview__img" style="display:none;">
                    <div class="upload-preview__actions">
                        <button type="submit" class="btn-save">📤 Hochladen</button>
                        <button type="button" class="btn-back" onclick="resetUpload()">Abbrechen</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="breadcrumb">
            <?php foreach ($breadcrumbs as $i => $bc): ?>
                <?php if ($i > 0): ?><span>/</span><?php endif; ?>
                <?php if ($i < count($breadcrumbs) - 1): ?>
                    <a href="files.php<?= $bc['path'] ? '?dir=' . urlencode($bc['path']) : '' ?>"><?= htmlspecialchars($bc['name']) ?></a>
                <?php else: ?>
                    <strong><?= htmlspecialchars($bc['name']) ?></strong>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($viewMode === 'gallery' && $hasImages): ?>
        <!-- GALERIE-ANSICHT -->

        <!-- Ordner & Nicht-Bilder als Liste -->
        <?php $foldersAndFiles = array_filter($items, fn($i) => $i['is_dir'] || !$i['is_image']); ?>
        <?php if (!empty($foldersAndFiles)): ?>
        <div class="file-grid" style="margin-bottom:1.5rem;">
            <?php if ($dir): ?>
            <div class="file-row">
                <div class="file-icon">⬆️</div>
                <div class="file-name">
                    <a href="files.php<?= dirname($dir) && dirname($dir) !== '.' ? '?dir=' . urlencode(dirname($dir)) : '' ?>">.. (zurück)</a>
                </div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <?php endif; ?>
            <?php foreach ($foldersAndFiles as $item): ?>
            <div class="file-row">
                <div class="file-icon">
                    <?php if ($item['is_dir']): ?>📁
                    <?php elseif ($item['extension'] === 'html'): ?>📄
                    <?php elseif ($item['extension'] === 'css'): ?>🎨
                    <?php elseif ($item['extension'] === 'js'): ?>⚡
                    <?php elseif ($item['extension'] === 'php'): ?>🐘
                    <?php else: ?>📎
                    <?php endif; ?>
                </div>
                <div class="file-name">
                    <?php if ($item['is_dir']): ?>
                        <a href="files.php?dir=<?= urlencode($item['path']) ?>"><?= htmlspecialchars($item['name']) ?></a>
                    <?php else: ?>
                        <?= htmlspecialchars($item['name']) ?>
                    <?php endif; ?>
                </div>
                <div><?= $item['is_dir'] ? '—' : formatSize($item['size']) ?></div>
                <div><?= date('d.m.Y H:i', $item['modified']) ?></div>
                <div>
                    <?php if ($item['editable']): ?>
                        <a href="editor.php?file=<?= urlencode($item['path']) ?>" class="btn-edit">Bearbeiten</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif ($dir): ?>
        <div class="file-grid" style="margin-bottom:1.5rem;">
            <div class="file-row">
                <div class="file-icon">⬆️</div>
                <div class="file-name">
                    <a href="files.php<?= dirname($dir) && dirname($dir) !== '.' ? '?dir=' . urlencode(dirname($dir)) : '' ?>">.. (zurück)</a>
                </div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bilder-Galerie -->
        <?php if (!empty($imageItems)): ?>
        <div class="gallery-header">
            <h2>🖼 Bilder (<?= count($imageItems) ?>)</h2>
        </div>
        <div class="image-gallery">
            <?php foreach ($imageItems as $item): ?>
            <div class="gallery-card">
                <div class="gallery-card__preview" onclick="openLightbox('<?= htmlspecialchars('../' . $item['path']) ?>', '<?= htmlspecialchars($item['name']) ?>')">
                    <?php if ($item['extension'] === 'svg'): ?>
                        <img src="../<?= htmlspecialchars($item['path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <img src="../<?= htmlspecialchars($item['path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="gallery-card__info">
                    <span class="gallery-card__name" title="<?= htmlspecialchars($item['name']) ?>"><?= htmlspecialchars($item['name']) ?></span>
                    <span class="gallery-card__meta"><?= strtoupper($item['extension']) ?> · <?= formatSize($item['size']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- LISTEN-ANSICHT -->
        <div class="file-grid">
            <div class="file-row file-row--header">
                <div class="file-icon"></div>
                <div>Name</div>
                <div>Grösse</div>
                <div>Geändert</div>
                <div></div>
            </div>

            <?php if ($dir): ?>
            <div class="file-row">
                <div class="file-icon">⬆️</div>
                <div class="file-name">
                    <a href="files.php<?= dirname($dir) && dirname($dir) !== '.' ? '?dir=' . urlencode(dirname($dir)) : '' ?>">.. (zurück)</a>
                </div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <?php endif; ?>

            <?php foreach ($items as $item): ?>
            <div class="file-row">
                <div class="file-icon">
                    <?php if ($item['is_dir']): ?>📁
                    <?php elseif ($item['is_image']): ?>
                        <img src="../<?= htmlspecialchars($item['path']) ?>" class="file-icon__thumb" alt="">
                    <?php elseif ($item['extension'] === 'html'): ?>📄
                    <?php elseif ($item['extension'] === 'css'): ?>🎨
                    <?php elseif ($item['extension'] === 'js'): ?>⚡
                    <?php elseif ($item['extension'] === 'php'): ?>🐘
                    <?php else: ?>📎
                    <?php endif; ?>
                </div>
                <div class="file-name">
                    <?php if ($item['is_dir']): ?>
                        <a href="files.php?dir=<?= urlencode($item['path']) ?>"><?= htmlspecialchars($item['name']) ?></a>
                    <?php elseif ($item['is_image']): ?>
                        <a href="javascript:void(0)" onclick="openLightbox('../<?= htmlspecialchars($item['path']) ?>', '<?= htmlspecialchars($item['name']) ?>')"><?= htmlspecialchars($item['name']) ?></a>
                    <?php else: ?>
                        <?= htmlspecialchars($item['name']) ?>
                    <?php endif; ?>
                </div>
                <div><?= $item['is_dir'] ? '—' : formatSize($item['size']) ?></div>
                <div><?= date('d.m.Y H:i', $item['modified']) ?></div>
                <div>
                    <?php if ($item['editable']): ?>
                        <a href="editor.php?file=<?= urlencode($item['path']) ?>" class="btn-edit">Bearbeiten</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
        <div class="lightbox__content">
            <button class="lightbox__close" onclick="closeLightbox(event)">✕</button>
            <img id="lightbox-img" src="" alt="">
            <div class="lightbox__caption" id="lightbox-caption"></div>
        </div>
    </div>

    <script>
        // === UPLOAD ===
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const uploadPreview = document.getElementById('upload-preview');
        const previewName = document.getElementById('preview-name');
        const previewSize = document.getElementById('preview-size');
        const previewImg = document.getElementById('preview-img');

        // Drag & Drop
        ['dragenter', 'dragover'].forEach(ev => {
            dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.add('upload-dropzone--active');
            });
        });

        ['dragleave', 'drop'].forEach(ev => {
            dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.remove('upload-dropzone--active');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showPreview(files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                showPreview(fileInput.files[0]);
            }
        });

        function showPreview(file) {
            previewName.textContent = file.name;
            previewSize.textContent = formatFileSize(file.size);
            uploadPreview.style.display = 'block';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.style.display = 'none';
            }
        }

        function resetUpload() {
            fileInput.value = '';
            uploadPreview.style.display = 'none';
            previewImg.style.display = 'none';
            previewImg.src = '';
        }

        function formatFileSize(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return bytes + ' B';
        }

        // Upload-Bereich automatisch öffnen nach Meldung
        <?php if ($uploadMsg): ?>
        document.getElementById('upload-area').classList.add('upload-area--visible');
        <?php endif; ?>

        // === LIGHTBOX ===
        function openLightbox(src, name) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-caption').textContent = name;
            document.getElementById('lightbox').classList.add('lightbox--visible');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            if (e.target.id === 'lightbox' || e.target.classList.contains('lightbox__close')) {
                document.getElementById('lightbox').classList.remove('lightbox--visible');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.getElementById('lightbox').classList.remove('lightbox--visible');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>
