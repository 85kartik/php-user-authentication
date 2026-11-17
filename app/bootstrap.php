<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();
header('Cache-Control: no-store');

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function go(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function database(): mysqli
{
    $connection = new mysqli(
        getenv('DB_HOST') ?: '127.0.0.1',
        getenv('DB_USER') ?: 'auth_app',
        getenv('DB_PASSWORD') ?: '',
        getenv('DB_NAME') ?: 'auth_demo'
    );
    $connection->set_charset('utf8mb4');
    return $connection;
}

function post(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : '';
}

function csrfField(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . escape($_SESSION['csrf']) . '">';
}

function checkCsrf(): void
{
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], post('csrf'))) {
        http_response_code(403);
        exit('This form expired. Reload the page and try again.');
    }
}
