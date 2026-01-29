<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Core/helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$originalRequestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

$basePath = '';
if (defined('APP_BASE_PATH')) {
    $basePath = APP_BASE_PATH;
}
if ($basePath === '') {
    $basePath = detect_base_path();
}
if ($basePath === '/') {
    $basePath = '';
}

$GLOBALS['app_base_path'] = $basePath;
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', $basePath);
}

$requestUri = $originalRequestUri;
$fallbackRequested = (isset($_GET['page']) || strpos($requestUri, 'index.php') !== false);
$htaccessEnabled = getenv('ROUTER_HTACCESS') === '1';
$routingMode = ($htaccessEnabled && !$fallbackRequested) ? 'CLEAN' : 'FALLBACK';

$GLOBALS['routing_mode'] = $routingMode;

if (defined('FORCE_HTTPS') && FORCE_HTTPS && !is_https()) {
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
    if ($host) {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }
}

if ($fallbackRequested) {
    $page = isset($_GET['page']) ? trim($_GET['page'], '/') : '';
    $virtualPath = $page === '' ? '/' : '/' . $page;
    if ($basePath !== '') {
        $virtualPath = $basePath . $virtualPath;
    }
    $_SERVER['REQUEST_URI'] = $virtualPath;
}

require_once __DIR__ . '/src/Core/Router.php';

$request = new Core\Request();
$router = new Bramus\Router\Router();
if ($basePath) {
    $router->setBasePath($basePath);
}

require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/api.php';

$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$matchInfo = $router->debugMatch($requestMethod, isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
$resolvedRoute = $matchInfo['matched'] ? $matchInfo['pattern'] : '404';
$httpsStatus = is_https() ? '1' : '0';
app_log('REQUEST_URI=' . $originalRequestUri
    . ' RESOLVED=' . $resolvedRoute
    . ' HTTPS=' . $httpsStatus
    . ' BASE_PATH=' . $basePath
    . ' ROUTING=' . $routingMode);

$router->set404(function () {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Страница не найдена';
});

$router->run();
