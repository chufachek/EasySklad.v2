<?php
require_once __DIR__ . '/public_html/config/config.php';

require_once __DIR__ . '/vendor/autoload.php';

use Bramus\Router\Router;
use Core\ErrorHandler;
use Core\Request;

ErrorHandler::register();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (defined('FORCE_HTTPS') && FORCE_HTTPS) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');

    if (!$isHttps) {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }
}

$request = new Request();
$router = new Router();

require __DIR__ . '/routes/web.php';
require __DIR__ . '/routes/api.php';

$router->set404(function () {
    http_response_code(404);
    echo 'Страница не найдена';
});

$router->run();
