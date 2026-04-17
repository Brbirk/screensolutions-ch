<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    $creds = loadCredentials();

    // Aktuelles Passwort prüfen
    if (!password_verify($currentPass, $creds['password'])) {
        $error = 'Das aktuelle Passwort ist falsch.';
    } elseif (strlen($newPass) < 8) {
        $error = 'Das neue Passwort muss mindestens 8 Zeichen lang sein.';
    } elseif ($newPass !== $confirmPass) {
        $error = 'Die Passwörter stimmen nicht überein.';
    } elseif ($currentPass === $newPass) {
        $error = 'Das neue Passwort muss sich vom aktuellen unterscheiden.';
    } else {
        changePassword($newPass);
        $success = 'Passwort erfolgreich geändert!';
    }
}

$creds = loadCredentials();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin – Einstellungen</title>
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
            <a href="settings.php" class="navbar__link navbar__link--active">⚙️ Einstellungen</a>
            <a href="../index.html" class="navbar__link" target="_blank">🌐 Website</a>
            <a href="logout.php" class="navbar__link navbar__link--logout">⏻ Logout</a>
        </div>
    </nav>

    <div class="content">
        <h1>Einstellungen</h1>
        <p class="subtitle">Passwort ändern und Kontodaten verwalten</p>

        <div class="settings-grid">
            <!-- Passwort ändern -->
            <div class="settings-card">
                <h2>🔒 Passwort ändern</h2>

                <?php if ($success): ?>
                    <div class="settings-msg settings-msg--success"><?= $success ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="settings-msg settings-msg--error"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" class="settings-form">
                    <div class="form-group">
                        <label for="current_password">Aktuelles Passwort</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Neues Passwort</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                        <span class="form-hint">Mindestens 8 Zeichen</span>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Neues Passwort bestätigen</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                    <button type="submit" class="btn-save">💾 Passwort speichern</button>
                </form>
            </div>

            <!-- Konto-Info -->
            <div class="settings-card">
                <h2>👤 Konto-Informationen</h2>
                <div class="info-row">
                    <span class="info-row__label">Benutzername</span>
                    <span class="info-row__value"><?= htmlspecialchars($creds['username']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-row__label">E-Mail (für Reset)</span>
                    <span class="info-row__value"><?= htmlspecialchars($creds['email'] ?? ADMIN_EMAIL) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-row__label">Passwort zuletzt geändert</span>
                    <span class="info-row__value"><?= htmlspecialchars($creds['last_changed'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-row__label">Konto erstellt</span>
                    <span class="info-row__value"><?= htmlspecialchars($creds['created'] ?? '—') ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
