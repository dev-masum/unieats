<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';

$db = createDb()->getConnection();
$userModel = new User($db);
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');

    if ($username === '' || $email === '' || $password === '' || $fullname === '') {
        $message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } else {
        $success = $userModel->register($username, $email, $password, $fullname);
        if ($success) {
            header('Location: index.php');
            exit;
        }
        $message = 'Registration failed. Username or email may already exist.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>UniEats</h1>
            <p>Create your student account</p>
        </header>
        <main>
            <div class="card">
                <h2>Register</h2>
                <?php if ($message): ?>
                    <div class="alert"><?= escape($message) ?></div>
                <?php endif; ?>
                <form method="post" action="register.php">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Register</button>
                </form>
                <p>Already have an account? <a href="index.php">Login here</a>.</p>
            </div>
        </main>
    </div>
</body>

</html>