<?php

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Menu.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/Order.php';
require_once __DIR__ . '/AdminLog.php';
require_once __DIR__ . '/Database.php';

function loadConfig(): array
{
    return require __DIR__ . '/../config/config.php';
}

function createDb(): Database
{
    $config = loadConfig();
    return new Database($config);
}

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function requireAdmin(): void
{
    if (empty($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: index.php');
        exit;
    }
}

function requireStudent(): void
{
    if (empty($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
        header('Location: admin-login.php');
        exit;
    }
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function flash(string $name, string $message = ''): ?string
{
    if ($message !== '') {
        $_SESSION['flash'][$name] = $message;
        return null;
    }

    if (!empty($_SESSION['flash'][$name])) {
        $msg = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        return $msg;
    }

    return null;
}
