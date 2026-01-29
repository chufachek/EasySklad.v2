<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

function loadEnv($path)
{
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if ($key !== '') {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
            }
        }
    }
}

loadEnv(BASE_PATH . '/.env');

$config = array(
    'db' => array(
        'host' => getenv('DB_HOST') ? getenv('DB_HOST') : '127.0.0.1',
        'name' => getenv('DB_NAME') ? getenv('DB_NAME') : 'easy_sklad',
        'user' => getenv('DB_USER') ? getenv('DB_USER') : 'root',
        'pass' => getenv('DB_PASS') ? getenv('DB_PASS') : '',
        'charset' => getenv('DB_CHARSET') ? getenv('DB_CHARSET') : 'utf8mb4',
    ),
    'jwt' => array(
        'secret' => getenv('JWT_SECRET') ? getenv('JWT_SECRET') : 'change_me',
        'ttl' => getenv('JWT_TTL') ? intval(getenv('JWT_TTL')) : 86400,
    ),
    'app' => array(
        'env' => getenv('APP_ENV') ? getenv('APP_ENV') : 'local',
        'base_url' => getenv('APP_URL') ? getenv('APP_URL') : 'http://localhost',
        'log_file' => BASE_PATH . '/storage/logs/app.log',
        'max_companies_per_owner' => getenv('MAX_COMPANIES_PER_OWNER') ? intval(getenv('MAX_COMPANIES_PER_OWNER')) : 1,
    ),
    'cors' => array(
        'allowed_origin' => getenv('CORS_ALLOWED_ORIGIN') ? getenv('CORS_ALLOWED_ORIGIN') : '*',
    ),
);

function config($key)
{
    global $config;
    $parts = explode('.', $key);
    $value = $config;
    foreach ($parts as $part) {
        if (!isset($value[$part])) {
            return null;
        }
        $value = $value[$part];
    }
    return $value;
}

spl_autoload_register(function ($class) {
    $prefixes = array('Core', 'Middleware', 'Controllers', 'Models', 'Services');
    foreach ($prefixes as $prefix) {
        if (strpos($class, $prefix . '\\') === 0) {
            $relative = str_replace('\\', '/', $class);
            $file = BASE_PATH . '/public_html/src/' . $relative . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
});
