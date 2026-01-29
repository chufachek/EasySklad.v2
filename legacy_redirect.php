<?php

require_once __DIR__ . '/public_html/src/Core/helpers.php';

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

function legacy_redirect($path, $status = 302)
{
    redirect($path, $status);
}
