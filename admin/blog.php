<?php
require_once 'config.php';
requireLogin();

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$isNew = isset($_GET['new']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>screensolutions Admin - Blog</title>
<link rel="stylesheet" href="admin-style.css">
<style>
        .content { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 10px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn { padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; font-size: 0.95rem; }
        .btn-primary { background: #4a90d9; color: #fff; }
        .btn-primary:hover { background: #357abd; }
        .btn-success { background: #1b8a4a; color: #fff; }
        .btn-danger { background: #e8435f; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-secondary:hover { background: #5a6268; }
        .article-item { background: #fff; border-radius: 10px; padding: 18px 22px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .article-item h3 { margin: 0 0 4px 0; color: #333; font-size: 1.1rem; }
        .article-item small { color: #888; }
        .article-actions { display: flex; gap: 8px; }
        .article-actions .btn { padding: 6px 14px; font-size: 0.85rem; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.78rem; font-weight: 600; margin-left: 10px; vertical-align: middle; }
        .badge-live { background: #d4edda; color: #155724; }
        .badge-draft { background: #fff3cd; color: #856404; }
        .editor-form { background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 0.95rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; background: #fff; color: #333; box-sizing: border-box; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #4a90d9; outline: none; box-shadow: 0 0 0 2px rgba(74,144,217,0.2); }
        .form-group textarea { font-family: monospace; resize: vertical; }
        .form-row { display: flex; gap: 18px; }
        .form-row .form-group { flex: 1; }
        .form-actions { display: flex; gap: 10px; margin-top: 20px; }
        .toast { position: fixed; top: 20px; right: 20px; padding: 14px 24px; border-radius: 8px; color: #fff; font-weight: 600; z-index: 9999; opacity: 0; transition: opacity 0.3s; }
        .toast.show { opacity: 1; }
        .toast-success { background: #1b8a4a; }
        .toast-error { background: #e8435f; }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="navbar__brand">
            <img src="../images/logo-screensolutions-white-ki.png" alt="screensolutions" class="navbar__logo">
            <span class="navbar__title">Admin</span>
        </div>
        <div class="navbar__menu">
            <a href="dashboard.php" class="navbar__link navbar__link">🏠 Dashboard</a>
            <a href="blog.php" class="navbar__link navbar__link--active">Blog</a>
        <a href="files.php" class="navbar__link">📁 Dateien</a>
            <a href="files.php?dir=images" class="navbar__link">🖼 Bilder</a>
            <a href="settings.php" class="navbar__link">⚙️ Einstellungen</a>
            <a href="../index.html" class="navbar__link" target="_blank">🌐 Website</a>
            <a href="logout.php" class="navbar__link navbar__link--logout">⏻ Logout</a>
        </div>
    </nav>

<div class="blog-admin">
    <div id="toast" class="toast"></div>
    
    <!-- LIST VIEW -->
    <div id="list-view">
        <div class="page-header">
            <h1>Blog-Artikel</h1>
            <a href="?new" class="btn btn--primary">+ Neuer Artikel</a>
        </div>
        <div id="article-list" class="article-list">
            <div class="empty-state"><p>Artikel werden geladen...</p></div>
        </div>
    </div>
    
    <!-- EDITOR VIEW -->
    <div id="editor-view" style="display:none;">
        <div class="page-header">
            <h1 id="editor-title">Neuer Artikel</h1>
            <a href="blog.php" class="btn-cancel">Abbrechen</a>
        </div>
        <div class="editor-form">
            <input type="hidden" id="article-id" value="0">
            <div class="form-group">
                <label>Titel</label>
                <input type="text" id="field-title" placeholder="Titel des Artikels">
            </div>
            <div class="form-group">
                <label>Kurztext (Teaser)</label>
                <textarea id="field-excerpt" placeholder="Kurze Zusammenfassung fuer die Uebersicht (2-3 Saetze)"></textarea>
            </div>
            <div class="form-group">
                <label>Inhalt (HTML)</label>
                <textarea id="field-content" class="content-editor" placeholder="<p>Artikel-Inhalt hier...</p>"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Bild-Pfad (optional)</label>
                    <input type="text" id="field-image" placeholder="z.B. images/blog/mein-bild.jpg">
                </div>
                <div class="form-group">
                    <label>Autor</label>
                    <input type="text" id="field-author" value="Bruno Birkhofer">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Datum</label>
                    <input type="date" id="field-date">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="field-published">
                        <option value="true">Veroeffentlicht</option>
                        <option value="false">Entwurf</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn-save" onclick="saveArticle()">Speichern</button>
                <a href="blog.php" class="btn-cancel">Abbrechen</a>
            </div>
        </div>
    </div>
</div>

<script>
const API = 'blog-api.php';

function showToast(msg, isError) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast' + (isError ? ' error' : '');
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}

async function loadArticles() {
    try {
        const r = await fetch(API + '?action=admin-list');
        const data = await r.json();
        const list = document.getElementById('article-list');
        
        if (data.articles.length === 0) {
            list.innerHTML = '<div class="empty-state"><p>Noch keine Artikel vorhanden.</p><a href="?new" class="btn btn--primary">Ersten Artikel erstellen</a></div>';
            return;
        }
        
        list.innerHTML = data.articles.map(a => {
            const status = a.published 
                ? '<span class="article-item__status article-item__status--published">Live</span>'
                : '<span class="article-item__status article-item__status--draft">Entwurf</span>';
            const dateStr = new Date(a.date).toLocaleDateString('de-CH');
            return '<div class="article-item">'
                + '<div class="article-item__info">'
                + '<div class="article-item__title">' + a.title + status + '</div>'
                + '<div class="article-item__meta">' + dateStr + ' &middot; ' + a.author + '</div>'
                + '</div>'
                + '<div class="article-item__actions">'
                + (a.published ? '<a href="/blog/' + a.slug + '" target="_blank" class="btn-sm btn-view">Ansehen</a>' : '')
                + '<a href="?edit=' + a.id + '" class="btn-sm btn-edit">Bearbeiten</a>'
                + '<button class="btn-sm btn-delete" onclick="deleteArticle(' + a.id + ',\'' + a.title.replace(/'/g, "\\'") + '\')">Loeschen</button>'
                + '</div></div>';
        }).join('');
    } catch(e) {
        document.getElementById('article-list').innerHTML = '<div class="empty-state"><p>Fehler beim Laden.</p></div>';
    }
}

async function loadArticleForEdit(id) {
    try {
        const r = await fetch(API + '?action=admin-get&id=' + id);
        const data = await r.json();
        if (data.success) {
            const a = data.article;
            document.getElementById('article-id').value = a.id;
            document.getElementById('field-title').value = a.title;
            document.getElementById('field-excerpt').value = a.excerpt;
            document.getElementById('field-content').value = a.content;
            document.getElementById('field-image').value = (a.image || '').replace(/^\/images\//, '');
            document.getElementById('field-author').value = a.author;
            document.getElementById('field-date').value = a.date;
            document.getElementById('field-published').value = a.published ? 'true' : 'false';
            document.getElementById('editor-title').textContent = 'Artikel bearbeiten';
        }
    } catch(e) {
        showToast('Fehler beim Laden des Artikels', true);
    }
}

async function saveArticle() {
    const id = parseInt(document.getElementById('article-id').value);
    const payload = {
        title: document.getElementById('field-title').value,
        excerpt: document.getElementById('field-excerpt').value,
        content: document.getElementById('field-content').value,
        image: document.getElementById('field-image').value,
        author: document.getElementById('field-author').value,
        date: document.getElementById('field-date').value,
        published: document.getElementById('field-published').value === 'true'
    };
    
    if (!payload.title.trim()) {
        showToast('Bitte Titel eingeben', true);
        return;
    }
    
    const action = id > 0 ? 'update' : 'create';
    if (id > 0) payload.id = id;
    
    try {
        const r = await fetch(API + '?action=' + action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await r.json();
        if (data.success) {
            showToast(data.message);
            setTimeout(() => window.location.href = 'blog.php', 1000);
        } else {
            showToast(data.message || 'Fehler', true);
        }
    } catch(e) {
        showToast('Speichern fehlgeschlagen', true);
    }
}

async function deleteArticle(id, title) {
    if (!confirm('Artikel "' + title + '" wirklich loeschen?')) return;
    
    try {
        const r = await fetch(API + '?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        const data = await r.json();
        if (data.success) {
            showToast('Artikel geloescht');
            loadArticles();
        } else {
            showToast(data.message || 'Fehler', true);
        }
    } catch(e) {
        showToast('Loeschen fehlgeschlagen', true);
    }
}

// Init
const params = new URLSearchParams(window.location.search);
if (params.has('new') || params.has('edit')) {
    document.getElementById('list-view').style.display = 'none';
    document.getElementById('editor-view').style.display = 'block';
    document.getElementById('field-date').value = new Date().toISOString().split('T')[0];
    
    if (params.has('edit')) {
        loadArticleForEdit(parseInt(params.get('edit')));
    }
} else {
    loadArticles();
}
</script>
</body>
</html>