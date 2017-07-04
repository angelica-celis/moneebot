<?php

namespace App\Classes;

class Helper
{
    /**
     * Print nice number without scientific format
     *
     * @param $num
     * @return string
     */
    public static function niceNumPrint($num)
    {
        return (self::isFloat($num) ? rtrim(sprintf('%.9f', $num), '0') : $num);
    }

    /**
     * Check if number is float
     *
     * @param $num
     * @return bool
     */
    public static function isFloat($num)
    {
        return is_float($num) || is_numeric($num) && ((float)$num != (int)$num);
    }
}