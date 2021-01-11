<?php
/**
 * Created by PhpStorm.
 * User: adams
 * Date: 14.11.2018
 * Time: 1:02
 */

namespace chw;


class constants
{
    static $token_server = "a";
    static $token_debug = "v";
    static $admin_ip = "ip;

    public static function validate($token)
    {
        if ($token === self::$token_server or $token === self::$token_debug) return true; else return false;
    }
}