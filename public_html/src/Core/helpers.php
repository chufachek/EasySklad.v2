<?php

function detect_base_path()
{
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $scriptName = str_replace('\\', '/', $scriptName);
    $basePath = rtrim(dirname($scriptName), '/');

    if ($basePath === '/' || $basePath === '.') {
        $basePath = '';
    }

    return $basePath;
}

function base_path()
{
    return isset($GLOBALS['app_base_path']) ? $GLOBALS['app_base_path'] : '';
}

function base_url($path = '')
{
    $basePath = base_path();

    if ($path === '' || $path === '/') {
        if (is_fallback_routing()) {
            return $basePath . '/index.php';
        }
        return $basePath !== '' ? $basePath . '/' : '/';
    }

    $path = '/' . ltrim($path, '/');

    if (is_fallback_routing() && !is_static_path($path)) {
        $page = ltrim($path, '/');
        return $basePath . '/index.php?page=' . $page;
    }

    return $basePath . $path;
}

function redirect($path, $status = 302)
{
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        $target = $path;
    } elseif (base_path() !== '' && (strpos($path, base_path() . '/') === 0 || $path === base_path())) {
        $target = $path;
    } else {
        $target = base_url($path);
    }

    header('Location: ' . $target, true, $status);
    exit;
}

function is_https()
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $port = (!empty($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT']) === 443);

    $forwardedProto = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO']);
        $forwardedProto = strtolower(trim($parts[0]));
    }

    $forwardedSsl = !empty($_SERVER['HTTP_X_FORWARDED_SSL']) ? strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) : '';

    return ($https || $port || $forwardedProto === 'https' || $forwardedSsl === 'on');
}

function routing_mode()
{
    return isset($GLOBALS['routing_mode']) ? $GLOBALS['routing_mode'] : 'clean';
}

function is_fallback_routing()
{
    return routing_mode() === 'fallback';
}

function is_static_path($path)
{
    $path = '/' . ltrim($path, '/');

    if (strpos($path, '/assets/') === 0) {
        return true;
    }

    return (bool)preg_match('/\\.[a-z0-9]+$/i', $path);
}

function app_log($message)
{
    $logFile = function_exists('config') ? config('app.log_file') : null;
    if (!$logFile) {
        $logFile = dirname(__DIR__, 2) . '/storage/logs/app.log';
    }

    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $line = date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND);
}

function render($page, $data = array())
{
    extract($data);

    require __DIR__ . '/../../views/layout/head.php';
    require __DIR__ . '/../../views/layout/header.php';
    require __DIR__ . '/../../views/layout/sidebar.php';
    require __DIR__ . '/../../views/pages/' . $page . '.php';
    require __DIR__ . '/../../views/layout/footer.php';
}
