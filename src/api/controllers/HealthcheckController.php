<?php
namespace API\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use YAPI\Core\ResponseTemplate;

/**
 * YAPI/SLIM : API\Controllers\HealthcheckController
 * ----------------------------------------------------------------------
 * Handles the '/healthcheck' api endpoint and everything beneath it.
 *
 * @package     API\Controllers
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class HealthcheckController
{
    /**
     * HealthcheckController constructor.
     *
     * @param App $app
     *      Slim application instance
     */
    public function __construct(App &$app)
    {
        // Define healthcheck route
        $app->any('/healthcheck', [$this, 'healthcheck']);
    }

    /**
     * Handles the base healthcheck endpoint.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function healthcheck(Request $request, Response $response, $args)
    {
        // Build response object
        $body = new ResponseTemplate(
            "SUCCESS",
            [
                'name'      => 'YAPI (Yuiti\'s API)',
                'author'    => 'Fabio Y. Goto <lab@yuiti.com.br>',
                'version'   => '0.0.1',
                'license'   => 'MIT',
                'copyright' => '©2018 Fabio Y. Goto'
            ],
            'Hello, World!'
        );
        
        // Set JSON response
        $return = $response
            ->withJson($body, 200)
            ->withHeader('Content-Type', 'application/json');
        return $return;
    }
}
