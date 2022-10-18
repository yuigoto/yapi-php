<?php
namespace YAPI\Core\Utils;

use YAPI\Core\Interfaces\BaseFormatter;
use YAPI\Core\Interfaces\BaseValidator;

class Cpf implements BaseFormatter, BaseValidator
{
    public static function format( $value ): string
    {
        return $value;
    }

    public static function validate( $value ): bool
    {
        return true;
    }
}
