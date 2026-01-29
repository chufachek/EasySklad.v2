<?php
namespace Core;

use PDO;

class Db
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            $dsn = 'mysql:host=' . config('db.host') . ';dbname=' . config('db.name') . ';charset=' . config('db.charset');
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            );
            self::$instance = new PDO($dsn, config('db.user'), config('db.pass'), $options);
        }
        return self::$instance;
    }
}
