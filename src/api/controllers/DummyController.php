<?php
namespace API\Controllers;

use API\Core\ResponseTemplate;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Container;
use Slim\Http\Response;

/**
 * YAPI : API\Controllers\DummyController
 * ----------------------------------------------------------------------
 * Dummy controller, use it as an example for building controllers and 
 * setting endpoints.
 * 
 * Must be called by the route handler class.
 * 
 * @package     API\Controllers
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class DummyController 
{
    /**
     * Slim application reference. For internal use.
     *
     * @var App
     */
    protected $app;

    /**
     * Slim dependency container reference. For internal use.
     *
     * @var Container
     */
    protected $container;

    /**
     * DummyController constructor.
     *
     * @param App $app
     */
    public function __construct(App &$app) 
    {
        // Set references
        $this->app = $app;
        $this->container = $app->getContainer();

        // Set app reference for use in groups
        $app = $this->app;

        // Pass a reference to self
        $ctrl = $this;

        // Handle them routes
        $this->app->group("/dummy", function () use ($app, $ctrl) {
            $app->map(['GET', 'POST'], '', [$ctrl, 'index']);
        });
    }

    /**
     * Endpoint index.
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
        // Set response body
        $body = new ResponseTemplate(
            200, 
            [
                'message' => 'Hello, Dummy!'
            ], 
            true
        );

        // Return
        $return = $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($body, 200);
        return $return;
    }
}
