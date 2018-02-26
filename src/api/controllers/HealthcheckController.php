<?php
namespace API\Controllers;

use API\Api;
use API\Core\ResponseTemplate;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Http\Response;

/**
 * YAPI : API\Controllers\HealthcheckController
 * ----------------------------------------------------------------------
 * Handles a simple healthcheck, to see if things are running a bit.
 * 
 * @package     API\Controllers
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class HealthcheckController 
{
    /**
     * HealthcheckController constructor.
     *
     * @param App $app
     */
    public function __construct(App &$app)
    {
        // Define healthcheck
        $app->any('/healthcheck', [$this, 'healthcheck']);
    }

    /**
     * Handles the `/healthcheck` endpoint.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function healthcheck(
        Request $request, 
        Response $response, 
        array $args
    ) {
        // Build response body
        $body = new ResponseTemplate(
            200,
            [
                'info' => [
                    'name'      => Api::API_NAME.' @ '.$_SERVER['SERVER_ADDR'], 
                    'author'    => Api::API_AUTHOR, 
                    'version'   => Api::API_VERSION, 
                    'license'   => Api::API_LICENSE, 
                    'copyright' => Api::API_RIGHTS
                ], 
                'message' => 'Hello, World!'
            ], 
            true
        );

        // Set response
        $return = $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($body, 200);
        return $return;
    }
}
