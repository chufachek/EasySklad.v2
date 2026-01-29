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
    $forwardedProto = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $forwardedProto = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
    }
    $forwardedSsl = strtolower($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || $forwardedProto === 'https'
        || $forwardedSsl === 'on';

    if (!$isHttps) {
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
        if ($host) {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: https://' . $host . $uri, true, 301);
            exit;
        }
    }
}

$request = new Request();
$router = new Router();
$basePath = '';
if (!empty($_SERVER['SCRIPT_NAME'])) {
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
}
if ($basePath === '/') {
    $basePath = '';
}
if ($basePath) {
    $router->setBasePath($basePath);
}

require __DIR__ . '/routes/web.php';
require __DIR__ . '/routes/api.php';

$router->set404(function () {
    http_response_code(404);
    echo 'Страница не найдена';
});

$router->run();
