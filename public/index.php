<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';

$db = createDb()->getConnection();
$userModel = new User($db);
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $message = 'Please enter both username and password.';
    } else {
        $user = $userModel->login($username, $password);
        if ($user) {
            if ($user['UserType'] === 'student') {
                $_SESSION['user_id'] = (int) $user['UserID'];
                $_SESSION['user_type'] = $user['UserType'];
                header('Location: menu.php');
                exit;
            } else {
                $message = 'Please use the Admin login page.';
            }
        } else {
            $message = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>UniEats</h1>
            <p>Smart campus food ordering</p>
        </header>
        <main>
            <div class="card" style="position: relative;">
                <a href="admin-login.php" target="_blank" class="button secondary" style="position: absolute; top: 24px; right: 24px; text-decoration: none; padding: 6px 12px; font-size: 0.85rem;">Admin Login</a>
                <h2>Login</h2>
                <?php if ($message): ?>
                    <div class="alert"><?= escape($message) ?></div>
                <?php endif; ?>
                <form method="post" action="index.php">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Login</button>
                </form>
                <p>Don’t have an account? <a href="register.php">Register here</a>.</p>
            </div>
        </main>
    </div>
</body>

</html>