<?php
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Core/helpers.php';
require_once __DIR__ . '/src/Core/Router.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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

$htaccessEnabled = getenv('ROUTER_HTACCESS') === '1';
$GLOBALS['routing_mode'] = $htaccessEnabled ? 'CLEAN' : 'FALLBACK';

$router = new Bramus\Router\Router();
if ($basePath) {
    $router->setBasePath($basePath);
}

$request = new Core\Request();

require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/api.php';

$paths = array(
    '/',
    '/login',
    '/register',
    '/logout',
    '/app',
    '/app/dashboard',
    '/app/profile',
    '/__health',
);

$authRequired = array(
    '/app',
    '/app/dashboard',
    '/app/profile',
);

echo "ROUTE SELF-TEST\n";
echo 'basePath=' . $basePath . "\n";
echo 'routingMode=' . routing_mode() . "\n\n";

echo str_pad('URI', 30) . str_pad('MATCHED', 10) . str_pad('HANDLER', 25) . "NOTES\n";
echo str_repeat('-', 80) . "\n";

foreach ($paths as $path) {
    $testUri = $basePath . $path;
    if ($testUri === '') {
        $testUri = '/';
    }

    $result = $router->debugMatch('GET', $testUri);
    $matchedText = $result['matched'] ? 'yes' : 'no';
    $handler = $result['handler'] ? $result['handler'] : '-';
    $notes = in_array($path, $authRequired, true) ? 'requires auth' : '';

    echo str_pad($path, 30) . str_pad($matchedText, 10) . str_pad($handler, 25) . $notes . "\n";
}

echo "\nEND\n";
