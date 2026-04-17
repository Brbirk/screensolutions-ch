<?php
require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $creds = loadCredentials();

    // E-Mail prüfen
    if ($email === ($creds['email'] ?? ADMIN_EMAIL)) {
        $token = generateResetToken();

        // Reset-Link per E-Mail senden
        $resetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['REQUEST_URI'])
            . '/reset-password.php?token=' . $token;

        $subject = 'screensolutions Admin – Passwort zurücksetzen';
        $message = "Hallo,\n\n";
        $message .= "Du hast eine Passwort-Zurücksetzung angefordert.\n\n";
        $message .= "Klicke auf folgenden Link, um dein Passwort zurückzusetzen:\n";
        $message .= $resetUrl . "\n\n";
        $message .= "Der Link ist 1 Stunde gültig.\n\n";
        $message .= "Falls du dies nicht angefordert hast, ignoriere diese E-Mail.\n\n";
        $message .= "— screensolutions Admin";

        $headers = "From: noreply@screensolutions.ch\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($email, $subject, $message, $headers);
        $success = 'Falls die E-Mail-Adresse korrekt ist, erhältst du in Kürze einen Reset-Link.';
    } else {
        // Gleiche Meldung wie bei Erfolg (Sicherheit: keine Info ob E-Mail existiert)
        $success = 'Falls die E-Mail-Adresse korrekt ist, erhältst du in Kürze einen Reset-Link.';
    }
}

// Bereits eingeloggt? → Einstellungen
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: settings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin – Passwort vergessen</title>
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
        .login-box input[type="email"] {
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
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="../images/logo-screensolutions-white-ki.png" alt="screensolutions" style="background:#1a1a2e;padding:10px 20px;border-radius:8px;">
        </div>
        <h1>Passwort vergessen</h1>
        <p>Gib deine Admin-E-Mail-Adresse ein. Du erhältst einen Link zum Zurücksetzen deines Passworts.</p>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <label for="email">E-Mail-Adresse</label>
            <input type="email" id="email" name="email" required autofocus placeholder="info@screensolutions.ch">
            <button type="submit">🔗 Reset-Link senden</button>
        </form>
        <?php endif; ?>

        <a href="login.php" class="back-link">← Zurück zum Login</a>
    </div>
</body>
</html>
