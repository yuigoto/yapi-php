<?php
namespace YAPI\Core;

/**
 * YAPI/SLIM : YAPI\Core\ResponseError
 * ----------------------------------------------------------------------
 * ResponseError object.
 *
 * @package     YAPI\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class ResponseError implements \JsonSerializable
{
    /**
     * Response error code.
     *
     * @var int
     */
    public $code;
    
    /**
     * Error title/name.
     *
     * @var string
     */
    public $title;
    
    /**
     * Error description (short).
     *
     * @var string
     */
    public $description;
    
    /**
     * Response error data, stack trace or anything that might be helpful
     * for debugging the error.
     *
     * @var mixed|null
     */
    public $data;
    
    /**
     * ResponseError constructor.
     *
     * @param int $code
     * @param string $title
     * @param string $description
     * @param mixed $data
     */
    public function __construct(
        int $code,
        string $title,
        string $description,
        $data = null
    ) {
        $this->code         = $code;
        $this->data         = $data;
        $this->title        = $title;
        $this->description  = $description;
    }
    
    /**
     * Specifies which entity's data should be serialized to JSON.
     *
     * In this case, all of the object's properties.
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        // Get all object vars
        $list = get_object_vars($this);
        
        // Remove generated vars from Doctrine
        foreach ($list as $k => $v) {
            if (preg_match("/^\_\_/", $k)) unset($list[$k]);
        }
        
        // Return it
        return $list;
    }
}
