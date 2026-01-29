<?php
header('Content-Type: text/plain; charset=utf-8');

$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';

echo "PING OK\n";
echo 'REQUEST_URI=' . $uri . "\n";
echo 'SCRIPT_NAME=' . $script . "\n";
echo 'DOCUMENT_ROOT=' . $root . "\n";
