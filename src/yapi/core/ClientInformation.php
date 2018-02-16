<?php
namespace YAPI\Core;

/**
 * YAPI/SLIM : YAPI\Core\ClientInformation
 * ----------------------------------------------------------------------
 * Generates a Client Information object for JSON responses.
 *
 * @package     YAPI\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class ClientInformation
{
    /**
     * HTTP User agent string.
     *
     * @var string
     */
    public $http_agent;
    
    /**
     * HTTP Connection header.
     *
     * @var string
     */
    public $http_connection;
    
    /**
     * HTTP Host header.
     *
     * @var string
     */
    private $http_host;
    
    /**
     * HTTP referer address.
     *
     * @var string
     */
    private $http_referer;
    
    /**
     * Remote user IP address,
     *
     * @var string
     */
    private $remote_addr;
    
    /**
     * Remote user hostname.
     *
     * @var string
     */
    private $remote_host;
    
    /**
     * Request method.
     *
     * @var string
     */
    private $request_method;
    
    /**
     * ClientInformation constructor.
     */
    public function __construct()
    {
        // Set all values
        $this->http_agent       = $_SERVER['HTTP_USER_AGENT'];
        $this->http_connection  = $_SERVER['HTTP_CONNECTION'];
        $this->http_host        = $_SERVER['HTTP_HOST'];
        $this->http_referer     = (isset($_SERVER['HTTP_REFERER']))
            ? $_SERVER['HTTP_REFERER'] : null;
        $this->remote_addr      = $_SERVER['REMOTE_ADDR'];
        $this->remote_host      = (isset($_SERVER['REMOTE_HOST']))
            ? $_SERVER['REMOTE_HOST'] : null;
        $this->request_method   = $_SERVER['REQUEST_METHOD'];
    }
}
