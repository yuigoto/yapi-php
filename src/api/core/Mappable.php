<?php
namespace API\Core;

/**
 * YAPI : API\Core\Mappable
 * ----------------------------------------------------------------------
 * Implements methods to allow lazy object-to-array conversion and, by 
 * implementing `\JsonSerializable`, allows JSON serialization too.
 * 
 * Properties must be either `public` or `protected` to be serialized.
 * 
 * @package     API\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class Mappable implements \JsonSerializable 
{
    /**
     * Returns an array of all the object's public and protected properties.
     * 
     * Excludes properties defined by Doctrine (started by __).
     *
     * @return array
     */
    public function toArray(): array 
    {
        $list = get_object_vars($this);

        // Remove any generated properties from Doctrine
        foreach ($list as $k => $v) {
            if (preg_match("/^\_\_/", $k)) unset($list[$k]);
        }
        return $list;
    }

    /**
     * Specifies which of the object's parameters should be serialized 
     * to JSON, when using `json_encode` in an instance.
     * 
     * Currently serves as an alias to `toArray()`, but can be overridden.
     *
     * @return array
     */
    public function jsonSerialize(): array 
    {
        return $this->toArray();
    }
}
