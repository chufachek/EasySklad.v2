<?php
header('Content-Type: text/plain; charset=utf-8');

$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
$scriptFilename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
$https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : '';
$forwardedProto = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : '';
$routerHtaccess = getenv('ROUTER_HTACCESS');

echo "PING OK\n";
echo 'PHP_VERSION=' . PHP_VERSION . "\n";
echo 'DOCUMENT_ROOT=' . $root . "\n";
echo 'SCRIPT_FILENAME=' . $scriptFilename . "\n";
echo 'REQUEST_URI=' . $uri . "\n";
echo 'SCRIPT_NAME=' . $script . "\n";
echo 'HTTPS=' . $https . "\n";
echo 'X_FORWARDED_PROTO=' . $forwardedProto . "\n";
echo 'CWD=' . getcwd() . "\n";
echo 'ROUTER_HTACCESS=' . ($routerHtaccess !== false ? $routerHtaccess : '') . "\n";
echo "END\n";
