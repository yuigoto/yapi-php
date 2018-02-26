<?php
namespace API\Core;

/**
 * YAPI : API\Core\ClientInformation
 * ----------------------------------------------------------------------
 * Defines a serializable object containing the user client information.
 * 
 * @package     API\Core
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class ClientInformation extends Mappable 
{
    /**
     * HTTP User Agent.
     * 
     * @var string
     */
    protected $http_user_agent;

    /**
     * HTTP Connection.
     *
     * @var string
     */
    protected $http_connection;

    /**
     * HTTP Host.
     *
     * @var string
     */
    protected $http_host;

    /**
     * HTTP Referer.
     *
     * @var string
     */
    protected $http_referer;

    /**
     * Remote user IP address.
     * 
     * @var string
     */
    protected $remote_addr;

    /**
     * Remote user hostname.
     * 
     * @var string
     */
    protected $remote_host;

    /**
     * HTTP request method.
     * 
     * @var string
     */
    protected $request_method;

    /**
     * Server request URI.
     *
     * @var string
     */
    protected $request_uri;

    /**
     * ClientInformation constructor.
     * 
     * @param boolean $with_date 
     *      Optional, for debugging purposes, should date 
     *      be included?
     */
    public function __construct(bool $with_date = false)
    {
        $this->http_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->http_connection = $_SERVER['HTTP_CONNECTION'];
        $this->http_host = $_SERVER['HTTP_HOST'];
        $this->http_referer = (isset($_SERVER['HTTP_REFERER']))
            ? $_SERVER['HTTP_REFERER'] : null;
        $this->remote_addr = $_SERVER['REMOTE_ADDR'];
        $this->remote_host = (isset($_SERVER['REMOTE_HOST']))
            ? $_SERVER['REMOTE_HOST'] : null;
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_uri = (isset($_SERVER['REQUEST_URI']))
            ? $_SERVER['REQUEST_URI'] : null;
        if ($with_date === true) $this->date = date('c');
    }
}
