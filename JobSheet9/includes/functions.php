<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', '0');
    session_start();
}

function read_json_array(string $filename): array
{
    if (isset($_SESSION['app_data'][$filename]) && is_array($_SESSION['app_data'][$filename])) {
        return $_SESSION['app_data'][$filename];
    }

    $path = __DIR__ . '/../data/' . $filename . '.json';

    if (!file_exists($path)) {
        $_SESSION['app_data'][$filename] = [];
        return $_SESSION['app_data'][$filename];
    }

    $content = file_get_contents($path);
    if ($content === false || trim($content) === '') {
        $_SESSION['app_data'][$filename] = [];
        return $_SESSION['app_data'][$filename];
    }

    $data = json_decode($content, true);
    $_SESSION['app_data'][$filename] = is_array($data) ? $data : [];
    return $_SESSION['app_data'][$filename];
}

function write_json_array(string $filename, array $data): void
{
    $_SESSION['app_data'][$filename] = $data;
}

function redirect(string $target): void
{
    header('Location: ' . $target);
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value === null) {
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }

    $_SESSION[$key] = $value;
    return $value;
}

function require_login(): void
{
    if (empty($_SESSION['petugas'])) {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        $depth = max(0, count(array_filter(explode('/', str_replace('\\', '/', $scriptDir)))) - 1);
        redirect(str_repeat('../', $depth) . 'login.php');
    }
}

function resolve_base_path(): string
{
    $root = dirname(__DIR__);
    $scriptDir = dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__);
    $relative = ltrim(str_replace('\\', '/', substr($scriptDir, strlen($root))), '/');

    if ($relative === '') {
        return '';
    }

    return str_repeat('../', substr_count($relative, '/') + 1);
}

function set_default_credentials(): void
{
    if (!isset($_SESSION['petugas'])) {
        $_SESSION['petugas'] = [
            'username' => 'petugas',
            'nama' => 'Akbar',
        ];
    }
}
