<?php
/**
 * @package dynamichat
 */
namespace Inc\Base;

class Deactivate
{
    function __construct()
    {
        if (false) {}
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}