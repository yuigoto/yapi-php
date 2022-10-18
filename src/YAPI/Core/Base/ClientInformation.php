<?php

namespace YAPI\Core\Base;

class ClientInformation extends Mappable
{
    protected $http_user_agent;
    protected $http_connection;
    protected $http_host;
    protected $http_referer;
    protected $remote_addr;
    protected $remote_host;
    protected $request_method;
    protected $request_uri;
    protected $date = null;

    public function __construct (
        bool $with_date = false
    ) {
        $this->http_user_agent = $_SERVER[ 'HTTP_USER_AGENT' ];
        $this->http_connection = $_SERVER[ 'HTTP_CONNECTION' ];
        $this->http_host = $_SERVER[ 'HTTP_HOST' ];
        $this->http_referer = ( isset( $_SERVER[ 'HTTP_REFERER' ] ) )
            ? $_SERVER[ 'HTTP_REFERER' ] : null;
        $this->remote_addr = $_SERVER[ 'REMOTE_ADDR' ];
        $this->remote_host = ( isset( $_SERVER[ 'REMOTE_HOST' ] ) )
            ? $_SERVER[ 'REMOTE_HOST' ] : null;
        $this->request_method = $_SERVER[ 'REQUEST_METHOD' ];
        $this->request_uri = ( isset( $_SERVER[ 'REQUEST_URI' ] ) )
            ? $_SERVER[ 'REQUEST_URI' ] : null;
        if ( $with_date === true ) $this->date = date( 'c' );
    }
}
