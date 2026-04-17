<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (verifyLogin($user, $pass)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Ungültiger Benutzername oder Passwort.';
    }
}

// Bereits eingeloggt?
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>screensolutions Admin – Login</title>
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
        }
        .login-box label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.4rem;
        }
        .login-box input[type="text"],
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
        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            color: #888;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: #e84393;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="../images/logo-screensolutions-white-ki.png" alt="screensolutions" style="background:#1a1a2e;padding:10px 20px;border-radius:8px;">
        </div>
        <h1>Admin Login</h1>
        <p>Melde dich an, um die Website zu verwalten.</p>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Benutzername</label>
            <input type="text" id="username" name="username" required autofocus>
            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Anmelden</button>
        </form>
        <a href="forgot-password.php" class="forgot-link">Passwort vergessen?</a>
    </div>
</body>
</html>
