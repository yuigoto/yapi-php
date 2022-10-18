<?php
namespace YAPI\Core\Interfaces;

interface BaseValidator
{
    /**
     * @param $value
     * @return bool
     */
    public static function validate ( $value ): bool;
}
