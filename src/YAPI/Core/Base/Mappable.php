<?php
namespace YAPI\Core\Base;

/**
 * YAPI\Core\Base\Mappable
 * ----------------------------------------------------------------------
 * Implements methods to allow lazy object-to-array conversion, so it can
 * be serialized by `json_encode`.
 *
 * @package     YAPI\Core\Base
 * @author      Fabio Y. Goto <lab@yuiti.dev>
 * @since       0.0.1
 */
class Mappable implements \JsonSerializable
{
    /**
     * Returns an array with all public and protected properties.
     *
     * @return array
     */
    public function toArray(): array
    {
        $list = get_object_vars( $this );
        foreach ( $list as $k => $v ) {
            // Exclude properties coming from Doctrine
            if ( preg_match( "/^__/", $k ) ) unset( $list[ $k ] );
        }
        return $list;
    }

    /**
     * Specifies which of the object's properties should be serialized to JSON.
     *
     * If needed, should be overridden.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
