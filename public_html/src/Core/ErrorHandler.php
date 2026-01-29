<?php
namespace Core;

class ErrorHandler
{
    public static function register()
    {
        set_error_handler(array(__CLASS__, 'handleError'));
        set_exception_handler(array(__CLASS__, 'handleException'));
    }

    public static function handleError($severity, $message, $file, $line)
    {
        $errorMessage = '[' . date('c') . '] ' . $message . ' in ' . $file . ':' . $line;
        error_log($errorMessage . "\n", 3, config('app.log_file'));
        Response::error('SERVER_ERROR', 'Server error', 500);
    }

    public static function handleException($exception)
    {
        $errorMessage = '[' . date('c') . '] ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine();
        error_log($errorMessage . "\n", 3, config('app.log_file'));
        Response::error('SERVER_ERROR', 'Server error', 500);
    }
}
