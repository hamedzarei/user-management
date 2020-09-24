<?php

/**
 * Created by PhpStorm.
 * User: zrhm7232
 * Date: 1/31/20
 * Time: 12:30 PM
 */
namespace App\Helpers;

class IdentityHelper
{
    public static $TYPES = [
        'mobile',
        'email'
    ];
    public static $TYPE_REGEX = [
        '/\d{10,12}/m',
        '/.*\@[\w_-]+\.\w+/m'
    ];
    public static function getType($identity)
    {
        $detected_type = '';

        $types = self::$TYPES;

        foreach ($types as $index => $type) {
            if (preg_match(self::$TYPE_REGEX[$index], $identity)) {
                $detected_type = $type;
                break;
            }
        }

        return $detected_type;
    }
}