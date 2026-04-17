<?php
require_once 'config.php';

header('Content-Type: application/json');

$dataFile = __DIR__ . '/../blog-data.json';

function loadBlog() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return ['articles' => [], 'nextId' => 1];
    }
    return json_decode(file_get_contents($dataFile), true);
}

function saveBlog($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function createSlug($title) {
    $slug = mb_strtolower($title, 'UTF-8');
    $slug = str_replace(['ä','ö','ü','ß'], ['ae','oe','ue','ss'], $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Public endpoint: list published articles (no login needed)
if ($method === 'GET' && $action === 'list') {
    $data = loadBlog();
    $published = array_filter($data['articles'], fn($a) => $a['published']);
    usort($published, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
    if ($limit > 0) $published = array_slice($published, 0, $limit);
    
    echo json_encode(['success' => true, 'articles' => array_values($published)]);
    exit;
}

// Public endpoint: get single article by slug
if ($method === 'GET' && $action === 'get') {
    $slug = $_GET['slug'] ?? '';
    $data = loadBlog();
    $article = null;
    foreach ($data['articles'] as $a) {
        if ($a['slug'] === $slug && $a['published']) {
            $article = $a;
            break;
        }
    }
    if ($article) {
        echo json_encode(['success' => true, 'article' => $article]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Artikel nicht gefunden']);
    }
    exit;
}

// All other actions require login
requireLogin();

// Admin: list ALL articles (including unpublished)
if ($method === 'GET' && $action === 'admin-list') {
    $data = loadBlog();
    usort($data['articles'], fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    echo json_encode(['success' => true, 'articles' => $data['articles']]);
    exit;
}

// Admin: get single article by ID (including unpublished)
if ($method === 'GET' && $action === 'admin-get') {
    $id = (int)($_GET['id'] ?? 0);
    $data = loadBlog();
    $article = null;
    foreach ($data['articles'] as $a) {
        if ($a['id'] === $id) { $article = $a; break; }
    }
    if ($article) {
        echo json_encode(['success' => true, 'article' => $article]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Artikel nicht gefunden']);
    }
    exit;
}

// Create article
if ($method === 'POST' && $action === 'create') {
    $input = json_decode(file_get_contents('php://input'), true);
    $data = loadBlog();
    
    $article = [
        'id' => $data['nextId']++,
        'title' => $input['title'] ?? 'Ohne Titel',
        'slug' => createSlug($input['title'] ?? 'ohne-titel'),
        'excerpt' => $input['excerpt'] ?? '',
        'content' => $input['content'] ?? '',
        'image' => $input['image'] ?? '',
        'author' => $input['author'] ?? 'screensolutions',
        'date' => $input['date'] ?? date('Y-m-d'),
        'published' => $input['published'] ?? true
    ];
    
    $data['articles'][] = $article;
    saveBlog($data);
    echo json_encode(['success' => true, 'article' => $article, 'message' => 'Artikel erstellt']);
    exit;
}

// Update article
if ($method === 'POST' && $action === 'update') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($input['id'] ?? 0);
    $data = loadBlog();
    
    foreach ($data['articles'] as &$a) {
        if ($a['id'] === $id) {
            if (isset($input['title'])) {
                $a['title'] = $input['title'];
                $a['slug'] = createSlug($input['title']);
            }
            if (isset($input['excerpt'])) $a['excerpt'] = $input['excerpt'];
            if (isset($input['content'])) $a['content'] = $input['content'];
            if (isset($input['image'])) $a['image'] = $input['image'];
            if (isset($input['author'])) $a['author'] = $input['author'];
            if (isset($input['date'])) $a['date'] = $input['date'];
            if (isset($input['published'])) $a['published'] = $input['published'];
            
            saveBlog($data);
            echo json_encode(['success' => true, 'article' => $a, 'message' => 'Artikel aktualisiert']);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Artikel nicht gefunden']);
    exit;
}

// Delete article
if ($method === 'POST' && $action === 'delete') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($input['id'] ?? 0);
    $data = loadBlog();
    
    $data['articles'] = array_values(array_filter($data['articles'], fn($a) => $a['id'] !== $id));
    saveBlog($data);
    echo json_encode(['success' => true, 'message' => 'Artikel geloescht']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unbekannte Aktion']);
?>