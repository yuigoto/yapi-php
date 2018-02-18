<?php
namespace API;

use API\Controllers\AuthController;
use API\Controllers\DummyController;
use API\Controllers\HealthcheckController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Http\Response;
use YAPI\Core\ResponseTemplate;

/**
 * YAPI/SLIM : API\RouteHandler
 * ----------------------------------------------------------------------
 * Handles application/controller routing.
 *
 * @package     API
 * @since       0.0.1
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 */
class RouteHandler
{
    /**
     * RouteHandler constructor.
     *
     * @param App $app
     */
    public function __construct(&$app)
    {
        // Root access not allowed
        $app->get('/', [$this, 'index']);
        
        // Root access not allowed
        $app->group('/api', function () use ($app) {
            // Set routes for application endpoints
            new HealthcheckController($app);
            new AuthController($app);
            new DummyController($app);
        });
    }
    
    /**
     * Root request.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function index(
        Request $request,
        Response $response,
        array $args
    ) {
        // Build response object
        $body = new ResponseTemplate(
            "SUCCESS",
            [
                'cup_of_tea' => 'c|_|'
            ],
            getenv('PROJECT_NAME').' @ '.getenv('PROJECT_ADDR')
        );
        
        $return = $response->withJson(
            $body,
            200
        );
        return $return;
    }
    
    /**
     * Test for not allowed routes.
     *
     * @throws \Exception
     */
    public function notAllowed () {
        // Build response object
        throw new \Exception('Oops! Looks like you\'ve found a dead end!', 400);
    }
}
