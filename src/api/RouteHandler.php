<?php
namespace API;

use API\Controllers\DummyController;
use API\Controllers\HealthcheckController;
use API\Core\ResponseTemplate;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use API\Controllers\AuthController;

/**
 * YAPI : API\RouteHandler
 * ----------------------------------------------------------------------
 * Handles application routes and controller assignment.
 * 
 * @package     API
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class RouteHandler 
{
    /**
     * RouteHandler constructor.
     *
     * @param Api $app 
     *      API\Api instance, passed as a reference
     */
    public function __construct(&$app) 
    {
        // Keep a reference to this instance
        $ctrl = $this;

        // Root access isn't allowed
        $app->get('/', [$this, 'index']);

        // Group all API endpoints within the `/api` path
        $app->group('/api', function () use ($app, $ctrl) {
            // API root route
            $app->map(['GET', 'POST'], '', [$ctrl, 'apiRoot']);

            // Application Routes
            new AuthController($app);
            new DummyController($app);
            new HealthcheckController($app);
        });
    }

    /**
     * Handles request to the API's root address.
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
        $res = new ResponseTemplate(
            200, 
            [
                'message' => 'Hello, Computer!'
            ], 
            true
        );

        $return = $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($res, 200);
        return $return;
    }

    /**
     * Handles request to the API's root address.
     *
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function apiRoot(
        Request $request, 
        Response $response, 
        array $args
    ) {
        // Build response object
        $res = new ResponseTemplate(
            200, 
            [
                'name' => Api::API_NAME.' @ '.$_SERVER['SERVER_ADDR'], 
                'version' => Api::API_VERSION
            ], 
            true
        );

        $return = $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($res, 200);
        return $return;
    }

    /**
     * Test for a not allowed endpoint.
     *
     * @return void
     * @throws \Exception
     */
    public function __test() 
    {
        throw new \Exception('Oops! It is a dead end!', 400);
    }
}
