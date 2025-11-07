<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function onlyNumbers($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}