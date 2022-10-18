<?php
namespace YAPI\Core\Utils;

class Sanitize
{
    /**
     * @param $value
     * @return string
     */
    public static function toNumbers ( $value )
    {
        return ( string ) preg_replace( "/\D/", "", $value );
    }
}
