<?php
namespace API;

use API\Core\ClientInformation;
use API\Core\ResponseError;
use API\Core\ResponseTemplate;
use API\Core\Salt;
use API\Core\Utilities;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Logger;
use Psr7Middlewares\Middleware\TrailingSlash;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Container;
use Slim\Http\Response;
use Slim\Middleware\JwtAuthentication;

/**
 * YAPI : API\Api
 * ----------------------------------------------------------------------
 * Application handler, starts a runnable `Slim\App` object.
 * 
 * @package     API
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
 */
class Api 
{
    // Constants (mostly information only)
    // ------------------------------------------------------------------

    /**
     * API system name.
     * 
     * @var string
     */
    const API_NAME = 'YAPI (Yuiti\'s API)';

    /**
     * API author.
     * 
     * @var string
     */
    const API_AUTHOR = 'Fabio Y. Goto <lab@yuiti.com.br>';

    /**
     * API version number.
     * 
     * @var string
     */
    const API_VERSION = '0.0.2';

    /**
     * API license.
     * 
     * @var string
     */
    const API_LICENSE = 'MIT';

    /**
     * API version number.
     * 
     * @var string
     */
    const API_RIGHTS = '©2018 Fabio Y. Goto';

    // Properties
    // ------------------------------------------------------------------

    /**
     * Slim application handle.
     *
     * @var App
     */
    protected $app;

    /**
     * Slim container handle.
     * 
     * Use it to inject dependencied into the application.
     *
     * @var Container
     */
    protected $container;

    // Constructor
    // ------------------------------------------------------------------

    /**
     * Api constructor.
     */
    public function __construct()
    {
        // Load environment variables
        $dotenv = new Dotenv(API_ROOT);
        $dotenv->load();

        // Set container configuration
        $config = [
            'settings' => [
                'displayErrorDetails' => true, 
                'debug' => true
            ]
        ];

        // Set container and inject dependencies
        try {
            $this->container = new Container($config);
            $this->dependencies($this->container);
        } catch (Exception $e) {
            $this->errorHandleOnStart($e, 'Dependency container error.');
        }

        // Fire Slim
        $this->app = new App($this->container);

        // Fire application route handler
        new RouteHandler($this->app);

        // Add trailing slash middleware
        $this->app->add(new TrailingSlash(false));

        // Add authentication middleware
        $this->authentication();
    }

    // Public methods
    // ------------------------------------------------------------------

    /**
     * Returns the application instance.
     *
     * @return App
     */
    public function getApp(): App 
    {
        return $this->app;
    }

    /**
     * Returns the container instance.
     *
     * @return Container
     */
    public function getContainer(): Container 
    {
        return $this->container;
    }

    // Private methods
    // ------------------------------------------------------------------

    /**
     * Applies the authentication middleware on the applicaton.
     *
     * @return void
     */
    protected function authentication() 
    {
        // Container alias
        $c = $this->app->getContainer();

        // Set JWT authentication for most routes
        $this->app->add(new JwtAuthentication([
            // So we can use it with HTTP
            "secure" => false, 
            // Security Salt
            "secret" => Salt::get(), 
            "path" => ["/api"], 
            "passthrough" => [
                "/api/auth", 
                "/api/healthcheck", 
                "/api/bootstrap"
            ], 
            "regexp" => "/(.*)/", 
            "header" => "X-Token", 
            "realm" => "Protected", 
            // Success callback
            "callback" => function (
                Request $request, 
                Response $response,
                $args
            ) use ($c) {
                // Get entity manager
                $em = $c->get('em');

                // Get token payload
                $token = ($request->getHeader('Authorization')[0]);
                
                // Find it in the DB
                $token = $em->getRepository("API\Models\Entity\Users\UserToken")
                    ->findBy(['token' => $token]);
                
                // If token is invalid, trigger error
                if (!$token[0]->getIsValid()) return false;

                // Set jwt token
                $c['jwt'] = $args['decoded'];
            }, 
            // Error callback
            "error" => function (
                Request $request, 
                Response $response, 
                $args
            ) {
                // Generate error
                $err = new ResponseError(
                    401, 
                    'Invalid Access Token',
                    'The token provided is invalids and/or is expired.', 
                    $args
                );

                // Response object
                $res = new ResponseTemplate(
                    401, 
                    $err, 
                    true
                );

                // Return data
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withJson($res, 401);
            }
        ]));
    }

    /**
     * Injects dependencies into the application's container.
     *
     * @param Container $container 
     *      Application container alias, so we won't have to use 
     *      `$this->container` every single time we apply a dependecy.
     * @return void
     */
    protected function dependencies(&$container) 
    {
        // EntityManager Config
        $entity_config = Setup::createAnnotationMetadataConfiguration(
            [
                API_SOURCE.'\\api\\models\\entity\\'
            ], 
            API_DEV_MODE
        );

        // Database configuration for EntityManager
        try {
            // If no driver was declared, halt
            if (getenv('DATABASE_DRIVER') === false) {
                throw new \Exception('Database driver failed.', 500);
            }

            // Define connection
            if (getenv('DATABASE_DRIVER') === 'pdo_sqlite') {
                // Set sqlite database path as the same as salt
                $path = API_ROOT.'\\data\\';
                
                // SQLite uses `path` instead of `host`
                $connection = [
                    'driver' => getenv('DATABASE_DRIVER'), 
                    'path' => $path.getenv('DATABASE_HOSTNAME')
                ];
            } else {
                $connection = [
                    'driver' => getenv('DATABASE_DRIVER'), 
                    'host' => getenv('DATABASE_HOSTNAME'), 
                    'dbname' => getenv('DATABASE_DATABASE'), 
                    'user' => getenv('DATABASE_USERNAME'), 
                    'password' => getenv('DATABASE_PASSWORD'), 
                ];
            }
        } catch (Exception $e) {
            $this->errorHandleOnStart($e, 'Database Error');
        }

        // Entity Manager
        // --------------------------------------------------------------
        $em = EntityManager::create($connection, $entity_config);
        $container['em'] = $em;

        // Logger
        // --------------------------------------------------------------
        $container['logger'] = function ($c) {
            $logger = new Logger('api-logger');
            $logfile = API_ROOT.'\\logs\\api-logs.log';
            $stream = new StreamHandler($logfile, Logger::DEBUG);
            $fingersCrossed = new FingersCrossedHandler(
                $stream, 
                LOGGER::INFO
            );
            $logger->pushHandler($fingersCrossed);
            return $logger;
        };

        // Custom exception handler
        // --------------------------------------------------------------
        $container['errorHandler'] = function ($c) {
            return function (
                Request $request, 
                Response $response, 
                \Exception $exception
            ) use ($c) {
                // Set status code
                $code = ($exception->getCode()) ? $exception->getCode() : 500;

                // Set error body
                $err = new ResponseError(
                    $code, 
                    Utilities::httpStatusName($code), 
                    $exception->getMessage(), 
                    $exception->getTrace()
                );

                // Set response
                $res = new ResponseTemplate(
                    $code, 
                    $err,
                    true
                );

                // Log
                $logger = $c['logger'];
                $logger->info(
                    'User @ '.$_SERVER['REMOTE_ADDR'].' reached a '.$code, 
                    (new ClientInformation())->toArray()
                );
                $logger->info(
                    Utilities::httpStatusName($code)
                        .': '.$exception->getMessage()
                );

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($code)
                    ->withJson($res, $code);
            };
        };

        // Custom 404 error handler
        // --------------------------------------------------------------
        $container['notFoundHandler'] = function ($c) {
            return function (
                Request $request, 
                Response $response
            ) use ($c) {
                // Set error body
                $err = new ResponseError(
                    404, 
                    'Not Found', 
                    'The requested resource wasn\'t found or is inaccessible.'
                );

                // Set response
                $res = new ResponseTemplate(
                    404, 
                    $err, 
                    true
                );

                // Log
                $logger = $c['logger'];
                $logger->info(
                    'User @ '.$_SERVER['REMOTE_ADDR'].' reached a 404', 
                    (new ClientInformation())->toArray()
                );

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404)
                    ->withJson($res, 404);
            };
        };

        // Custom 405 error handler
        // --------------------------------------------------------------
        $container['notAllowedHandler'] = function ($c) {
            return function (
                Request $request, 
                Response $response, 
                array $methods
            ) use ($c) {
                // Implode allowed
                $methods = implode(', ', $methods);

                // Set error body
                $err = new ResponseError(
                    405, 
                    'Not Allowed', 
                    'Method not allowed. Must be one of: '.$methods.'.'
                );

                // Set response
                $res = new ResponseTemplate(
                    405, 
                    $err, 
                    true
                );

                // Log
                $logger = $c['logger'];
                $logger->info(
                    'User @ '.$_SERVER['REMOTE_ADDR'].' reached a 405', 
                    (new ClientInformation())->toArray()
                );

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withHeader('Allow', $methods)
                    ->withHeader('Access-Control-Allow-Methods', $methods)
                    ->withStatus(405)
                    ->withJson($res, 405);
            };
        };
    }

    /**
     * Returns a JSON response with an error message when the 
     * application's still starting.
     *
     * @param \Exception $e 
     *      The exception object 
     * @param string $title 
     *      Error title 
     * @return void
     */
    protected function errorHandleOnStart(\Exception $e, string $title) 
    {
        // Create error response
        $err = new ResponseError(
            $e->getCode(), 
            $title, 
            $e->getMessage(), 
            $e->getTrace()
        );

        // Set header and response
        header('Content-Type', 'application/json');

        // Return response
        echo json_encode(
            new ResponseTemplate(
                $e->getCode, 
                $err, 
                true
            )
        );
    }
}
