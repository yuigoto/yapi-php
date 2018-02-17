<?php
namespace API\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Container;
use YAPI\Core\ResponseTemplate;

/**
 * YAPI/SLIM : API\Controllers\DummyController
 * ----------------------------------------------------------------------
 * DummyController, use as an example on how the API's controllers are to
 * be used.
 *
 * This class must be called by the RouteHandler, inside the appropriate place.
 *
 * @package     API\Controllers
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class DummyController
{
    /**
     * Slim application instance.
     *
     * @var App
     */
    private $app;
    
    /**
     * Slim application Container handler.
     *
     * @var Container
     */
    private $container;
    
    /**
     * DummyController constructor.
     *
     * @param App $app
     *      Slim application handler
     */
    public function __construct(App &$app)
    {
        // Set variables
        $this->app          = $app;
        $this->container    = $app->getContainer();
        
        // Handle routes here
        $this->app->get('/dummy', function(Request $request, Response $response, $args) {
            $return = new ResponseTemplate(
                "SUCCESS",
                [
                    "hello" => "Oh HAI! Dummy Template Works"
                ],
                "Hello, World!"
            );
            
            return $response->withJson($return, 200)
                ->withHeader('Content-Type', 'application/json');
        });
        
        $this->app->get('/dummy/demo', [$this, 'testMethod']);
    }
    
    /**
     * Test method for a route handler.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function testMethod(Request $request, Response $response, $args)
    {
        $return = new ResponseTemplate(
            "SUCCESS",
            [
                "hello" => "Test Method for Dummy Template Reporting! :D"
            ],
            "Hello, Dummy Boy!"
        );
    
        return $response->withJson($return, 200)
                        ->withHeader('Content-Type', 'application/json');
    }
}
