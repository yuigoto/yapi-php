<?php
namespace YAPI\Core\Utils;

class Number
{
    /**
     * @param int|float $value
     * @param int|float $target
     * @param int|float $step
     * @return int|float
     */
    public static function approach (
        $value,
        $target,
        $step
    ) {
        return ( $value > $target )
            ? max( $value + abs( $step ), $target )
            : min ( $value + abs( $step ), $target );
    }

    /**
     * @param int|float $value
     * @param int|float $min
     * @param int|float $max
     * @return int|float
     */
    public static function clamp (
        $value,
        $min,
        $max
    ) {
        return ( $value < $min ) ? $min : ( ( $value > $max ) ? $max : $value );
    }
}
