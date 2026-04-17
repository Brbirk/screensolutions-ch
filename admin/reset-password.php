<?php
require_once 'config.php';

$token = $_GET['token'] ?? '';
$success = '';
$error = '';
$validToken = false;

// Token prüfen
if ($token) {
    $validToken = verifyResetToken($token);
    if (!$validToken) {
        $error = 'Der Reset-Link ist ungültig oder abgelaufen.';
    }
}

// Neues Passwort setzen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (!verifyResetToken($token)) {
        $error = 'Der Reset-Link ist ungültig oder abgelaufen.';
        $validToken = false;
    } elseif (strlen($newPass) < 8) {
        $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
        $validToken = true;
    } elseif ($newPass !== $confirmPass) {
        $error = 'Die Passwörter stimmen nicht überein.';
        $validToken = true;
    } else {
        changePassword($newPass);
        clearResetToken();
        $success = 'Passwort erfolgreich geändert! Du kannst dich jetzt anmelden.';
        $validToken = false;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin – Passwort zurücksetzen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Raleway', sans-serif;
            background: #1a1a2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            border-radius: 12px;
            padding: 3rem;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-box h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #1a1a2e;
        }
        .login-box p {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .login-box label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.4rem;
        }
        .login-box input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            margin-bottom: 1.2rem;
            transition: border-color 0.2s;
        }
        .login-box input:focus {
            outline: none;
            border-color: #e84393;
        }
        .login-box button {
            width: 100%;
            padding: 0.85rem;
            background: #e84393;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-box button:hover {
            background: #d63384;
        }
        .success {
            background: #e6f9ed;
            color: #1a7a3a;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }
        .error {
            background: #ffe0e0;
            color: #c00;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo img {
            height: 50px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            color: #888;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #e84393;
        }
        .form-hint {
            display: block;
            font-size: 0.75rem;
            color: #999;
            margin-top: -0.8rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="../images/logo-screensolutions-white-ki.png" alt="screensolutions" style="background:#1a1a2e;padding:10px 20px;border-radius:8px;">
        </div>
        <h1>Passwort zurücksetzen</h1>

        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
            <a href="login.php" class="back-link" style="margin-top:0;font-weight:600;color:#e84393;">→ Zum Login</a>
        <?php elseif ($error && !$validToken): ?>
            <div class="error"><?= $error ?></div>
            <a href="forgot-password.php" class="back-link">← Neuen Reset-Link anfordern</a>
        <?php elseif ($validToken): ?>
            <p>Gib dein neues Passwort ein.</p>

            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <label for="new_password">Neues Passwort</label>
                <input type="password" id="new_password" name="new_password" required minlength="8">
                <span class="form-hint">Mindestens 8 Zeichen</span>
                <label for="confirm_password">Passwort bestätigen</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                <button type="submit">🔒 Neues Passwort setzen</button>
            </form>
        <?php else: ?>
            <div class="error">Kein gültiger Token angegeben.</div>
            <a href="forgot-password.php" class="back-link">← Reset-Link anfordern</a>
        <?php endif; ?>
    </div>
</body>
</html>
