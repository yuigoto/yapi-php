<?php
namespace API\Core;

/**
 * API : API\Core\ResponseTemplate
 * ----------------------------------------------------------------------
 * Serializable response template object, use it to standardize JSON 
 * responses.
 * 
 * @package     API\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class ResponseTemplate extends Mappable
{
    /**
     * Response HTTP code.
     *
     * @var int
     */
    protected $code;

    /**
     * Response payload.
     *
     * @var array
     */
    protected $result;

    /**
     * Client information object.
     *
     * @var ClientInformation
     */
    protected $client;

    /**
     * ResponseTemplate constructor
     *
     * @param integer $code 
     *      Replicates the HTTP response code
     * @param array|object $result 
     *      Response payload data
     * @param bool $client 
     *      Optional, set it to TRUE to return the user client information
     */
    public function __construct(
        int $code, 
        $result, 
        bool $client = false
    ) {
        $this->code = $code;
        $this->result = $result;
        $this->client = ($client === true) 
            ? (new ClientInformation())->toArray() : [];
    }
}
