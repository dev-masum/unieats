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
            if ($user['UserType'] === 'admin') {
                $_SESSION['user_id'] = (int) $user['UserID'];
                $_SESSION['user_type'] = $user['UserType'];
                header('Location: admin-dashboard.php');
                exit;
            } else {
                $message = 'Invalid admin credentials.';
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
    <title>UniEats Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>UniEats</h1>
            <p>Admin Control Panel</p>
        </header>
        <main>
            <div class="card">
                <h2>Admin Login</h2>
                <?php if ($message): ?>
                    <div class="alert"><?= escape($message) ?></div>
                <?php endif; ?>
                <form method="post" action="admin-login.php">
                    <label for="username">Admin Username</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>
