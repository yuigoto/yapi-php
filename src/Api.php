<?php
use API\RouteHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr7Middlewares\Middleware\TrailingSlash;
use Slim\App;
use Slim\Container;
use Slim\Http\Response;
use YAPI\Core\ResponseTemplate;
use YAPI\Core\ResponseError;
use YAPI\Core\Utilities;

/**
 * YAPI/SLIM : Api
 * ----------------------------------------------------------------------
 * Application entrypoint. Fires a runnable `\Slim\App` object, which can
 * be returned.
 *
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class Api
{
    /**
     * Slim application instance handle.
     *
     * @var App
     */
    private $app;
    
    /**
     * Slim container instance handle.
     *
     * It handles all dependencies that need to be injected into our app.
     * 
     * @var Container
     */
    private $container;
    
    /**
     * App constructor.
     */
    public function __construct()
    {
        // Loading environment variables
        $dotenv = new \Dotenv\Dotenv(YX_PATH);
        $dotenv->load();
        
        // Set container configuration
        $config = [
            'settings' => [
                'displayErrorDetails' => true,
                'debug'               => true
            ]
        ];
        
        // Create container
        $container = new Container($config);
        
        // Set container
        $this->container = $container;
        
        // Inject dependencies into the container
        try {
            $this->dependencies($this->container);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        // Fire Slim App
        $app = new App($this->container);
        
        // Fire RouteHandler
        new RouteHandler($app);
        
        // Add post TrailingSlash middleware
        $app->add(new TrailingSlash(false));
        
        // Add Authentication Middleware
        $this->authentication($app);
        
        // Set app
        $this->app = $app;
    }
    
    /**
     * Returns the application instance.
     *
     * @return App
     */
    public function get()
    {
        return $this->app;
    }
    
    /**
     * Returns the container instance.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * Applies the authentication middleware on the application, before it runs.
     *
     * @param App $app
     *      Application instance
     */
    private function authentication(&$app)
    {
        // Container alias
        $container = $app->getContainer();
        
        // Set JWT Authentication for all routes, except authentication
        $app->add(new \Slim\Middleware\JwtAuthentication([
            "secure"        => false,
            "secret"        => Utilities::securitySaltRetrieve(),
            "path"          => ["/api"],
            "passthrough"   => [
                "/api/auth",
                "/api/healthcheck",
                "/api/bootstrap"
            ],
            "regexp"        => "/(.*)/",
            "header"        => "X-Token",
            "realm"         => "Protected",
            // Success callback
            "callback"      => function (Request $request, Response $response, $args) use ($container) {
                $container['jwt'] = $args['decoded'];
            },
            // Error callback
            "error"         => function (Request $request, Response $response, $args) {
                // Create new error
                $error = new ResponseError(
                    401,
                    "Invalid access token provided",
                    $args['message'],
                    $args
                );
                
                // Build response
                $return = new ResponseTemplate(
                    "ERROR",
                    [
                        "error" => $error
                    ],
                    "Invalid access token provided"
                );
                return $response->withJson($return, 401)
                    ->withHeader('Content-Type', 'application/json');
            }
        ]));
    }
    
    /**
     * Inject some dependencies into the application's container.
     *
     * @param Container $container
     *      Application container
     * @throws \Doctrine\ORM\ORMException
     */
    private function dependencies(&$container)
    {
        // Base EntityManager configuration
        $entity_config = Setup::createAnnotationMetadataConfiguration(
            [
                YX_SRC_PATH.'/api/models/entity/'
            ],
            YX_DEV_MODE
        );
        
        // Database configuration for the EntityManager
        try {
            // If no driver was declared, halt
            if (getenv('DB_DRIVER') === false) {
                throw new \Exception('Database driver failed', 500);
            }
            
            // Define connection
            if (getenv('DB_DRIVER') === 'pdo_sqlite') {
                $connection = [
                    'driver'    => getenv('DB_DRIVER'),
                    'path'      => YX_PATH.'/'.getenv('DB_HOSTNAME')
                ];
            } else {
                $connection = [
                    'driver'    => getenv('DB_DRIVER'),
                    'user'      => getenv('DB_USERNAME'),
                    'password'  => getenv('DB_PASSWORD'),
                    'host'      => getenv('DB_HOSTNAME'),
                    'dbname'    => getenv('DB_DATABASE'),
                    'charset'   => 'utf8mb4'
                ];
            }
        } catch (Exception $e) {
            // Create new response and error objects
            $error = new ResponseError(
                500,
                'Database Error',
                $e->getMessage().
                $e
            );
            
            // Set header
            header('Content-Type', 'application/json');
            
            // Return response
            echo json_encode(new ResponseTemplate(
                "ERROR",
                ['error' => $error],
                'Database driver failed, halting application.'
            ));
            die;
        }
        
        // EntityManager
        // --------------------------------------------------------------
        $entityManager = EntityManager::create(
            $connection,
            $entity_config
        );
        $container['em'] = $entityManager;
        
        // Exception Handler
        // ------------------------------------------------------------
        $container['errorHandler'] = function ($c) {
            return function (
                Request $request,
                Response $response,
                Exception $exception
            ) use ($c) {
                // Set exception status code
                $statusCode = ($exception->getCode())
                    ? $exception->getCode() : 500;
    
                // Set error body
                $error = new ResponseError(
                    $statusCode,
                    Utilities::httpStatusCodeName($statusCode),
                    $exception->getMessage(),
                    [
                        'query'     => $request->getQueryParams(),
                        'request'   => $request->getParsedBody()
                    ]
                );
    
                // Set not found body
                $body = new ResponseTemplate(
                    "ERROR",
                    [
                        'error' => $error
                    ],
                    Utilities::httpStatusCodeName($statusCode)
                );
                
                return $response
                    ->withStatus($statusCode)
                    ->withJson(
                        $body,
                        $statusCode
                    );
            };
        };
    
        // Logger
        // ------------------------------------------------------------
        $container['logger'] = function ($c) {
            $logger = new Logger('api-logger');
            $logfile = YX_PATH.'/logs/api-logs.log';
            $stream = new StreamHandler($logfile, Logger::DEBUG);
            $fingersCrossed = new FingersCrossedHandler(
                $stream,
                Logger::INFO
            );
            $logger->pushHandler($fingersCrossed);
            return $logger;
        };
        
        // Errors : 404 (Not Found)
        // ------------------------------------------------------------
        $container['notFoundHandler'] = function ($c) {
            return function (Request $request, Response $response) use ($c) {
                $logger = $c['logger'];
                $logger->info(
                    'User @ '.$_SERVER['REMOTE_ADDR']. ' reached a 404',
                    [
                        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                        'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                        'DATETIME' => date('c')
                    ]
                );
                
                // Set error body
                $error = new ResponseError(
                    404,
                    'Not Found',
                    'The requested resource was not found on this server.',
                    [
                        'query'     => $request->getQueryParams(),
                        'request'   => $request->getParsedBody()
                    ]
                );
                
                // Set not found body
                $body = new ResponseTemplate(
                    "ERROR",
                    [
                        'error' => $error
                    ],
                    "Not Found"
                );
                
                // Send response
                return $response
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'application/json')
                    ->withJson(
                        $body,
                        404
                    );
            };
        };
    
        // Errors : 405 (Method Not Allowed)
        // ------------------------------------------------------------
        $container['notAllowedHandler'] = function ($c) {
            return function (
                Request $request,
                Response $response,
                array $methods
            ) use ($c) {
                // Implode allowed methods
                $methods = implode(', ', $methods);
    
                // Set error body
                $error = new ResponseError(
                    405,
                    'Not Allowed',
                    'Request method not allowed.'
                        .' Must be one of the following: '.$methods
                );
    
                // Set not found body
                $body = new ResponseTemplate(
                    "ERROR",
                    [
                        'error' => $error
                    ],
                    "Method Not Allowed"
                );
                
                return $response
                    ->withStatus(405)
                    ->withHeader('Allow', $methods)
                    ->withHeader('Content-Type', 'application/json')
                    ->withHeader('Access-Control-Allow-Methods', $methods)
                    ->withJson(
                        $body,
                        405
                    );
            };
        };
    }
}
