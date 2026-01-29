<?php
namespace Core;

class Validator
{
    public static function required($value)
    {
        return !($value === null || $value === '');
    }

    public static function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function minLength($value, $min)
    {
        return strlen($value) >= $min;
    }

    public static function positiveNumber($value)
    {
        return is_numeric($value) && $value > 0;
    }

    public static function nonNegativeNumber($value)
    {
        return is_numeric($value) && $value >= 0;
    }

    public static function integer($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
