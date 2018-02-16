<?php
namespace API\Controllers;

use API\Models\Entity\Users\User;
use API\Models\Entity\Users\UserToken;
use Doctrine\ORM\Query;
use Firebase\JWT\JWT;
use Monolog\Handler\Curl\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Container;
use YAPI\Core\ResponseTemplate;
use YAPI\Core\Utilities;

/**
 * YAPI/SLIM : API\Controllers\AuthController
 * ----------------------------------------------------------------------
 * Handles the authentication endpoints.
 *
 * @package     API\Controllers
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.1
 */
class AuthController
{
    /**
     * Slim container handler.
     *
     * @var Container
     */
    protected $container;
    
    /**
     * AuthController constructor.
     *
     * @param App $app
     *      Slim application instance.
     */
    public function __construct(App &$app)
    {
        // Set container
        $this->container = $app->getContainer();
        
        // Create a reference to this controller
        $ctrl = $this;
        
        // Define healthcheck route
        $app->group('/auth', function() use ($app, $ctrl) {
            // Authentication handler
            $app->map(['GET', 'POST'], '', [$ctrl, 'authenticate']);
            
            // Token validation handler
            $app->any('/validate', [$ctrl, 'validate']);
        });
    }
    
    /**
     * Handles user authentication on POST and GET.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    public function authenticate(
        Request $request,
        Response $response,
        array $args
    ) {
        // Get username/email and password values
        $credentials = $this->userLoginCredentials($request);
        
        // Get entity manager and user repository
        $em     = $this->container->get('em');
    
        /**
         * @var User
         */
        $user   = $em->getRepository("API\Models\Entity\Users\User");
        
        // If no username/e-mail provided, throw error
        if (!isset($credentials['username']) && !isset($credentials['email'])) {
            throw new \Exception('No username or e-mail provided', 401);
        }
        
        // Define if search for username or e-mail
        if (isset($credentials['email'])) {
            $user = $user->findOneBy(['email' => $credentials['email']]);
        } else {
            $user = $user->findOneBy(['username' => $credentials['username']]);
        }
        
        // Invalid user/e-mail
        if ($user === null || !$user) {
            throw new \Exception(
                'Invalid username or e-mail address',
                401
            );
        }
        
        // Invalid password
        if (!$credentials['password'] || $credentials['password'] != $user->getPassword()) {
            throw new \Exception(
                'Invalid password provided',
                401
            );
        }
        
        // Set any previous token as not valid
        $this->invalidateAllTokens($user->getId());
        
        // Set token values
        $token = [
            'payload'   => $user->getTokenPayload(),
            'created'   => time(),
            'expires'   => time() + (60 * 60 * 24 * 7)
        ];
        
        // Fetch security salt
        $salt = Utilities::securitySaltRetrieve();
        
        // Sign token
        $jwt = JWT::encode($token, $salt);
    
        // Save the token for further verification
        $save = (new UserToken())
            ->setExpires($token['expires'])
            ->setIsValid(true)
            ->setToken($jwt)
            ->setUser($user);
        
        $em->persist($save);
        $em->flush();
        
        // Build response
        $return = new ResponseTemplate(
            "SUCCESS",
            [
                "token" => $jwt
            ]
        );
        return $response->withJson($return);
    }
    
    /**
     * Validates a user token and returns the payload information.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    public function validate(
        Request $request,
        Response $response,
        array $args
    ) {
        // Fetch token from headers
        $token = ($request->getHeader('Authorization')[0]);
        
        // Invalid token
        if ($token === null || $token === false || $token === "") {
            throw new \Exception('Invalid user token provided', 401);
        }
    
        // Fetch security salt
        $salt = Utilities::securitySaltRetrieve();
        
        // Decode
        try {
            $jwt = JWT::decode($token, $salt, array('HS256'));
        } catch (\Exception $e) {
            throw new \Exception('Invalid user token provided', 401);
        }
        
        // If decoding was ok, return payload
        $return = new ResponseTemplate(
            "SUCCESS",
            (array) $jwt
        );
        return $response->withJson($return);
    }
    
    /**
     * Returns the credentials used to login.
     *
     * @param Request $request
     *      Server request object
     * @return array
     */
    private function userLoginCredentials(Request $request)
    {
        // Check request method
        $method = $request->getMethod();
    
        // Getch params
        $params = ($method === 'GET')
            ? $request->getQueryParams() : $request->getParsedBody();
        
        // Holds user data
        $user = [];
        
        // Get username or e-mail address
        if (isset($params['user']) || isset($params['username'])) {
            $user['username'] = (isset($params['username']))
                ? trim($params['username']) : trim($params['user']);
        } elseif (isset($params['email'])) {
            $user['email'] = trim($params['email']);
        }
        
        // Only fetch password if username's set
        if (isset($user['username']) || isset($user['email'])) {
            if (isset($params['pass'])) {
                $user['password'] = trim($params['pass']);
            } elseif (isset($params['password'])) {
                $user['password'] = trim($params['password']);
            }
            $user['password'] = Utilities::passwordHash($user['password']);
        }
        
        return $user;
    }
    
    /**
     * Invalidates all previous tokens for the user ID provided.
     *
     * @param int $user_id
     */
    public function invalidateAllTokens(int $user_id)
    {
        // Get entity manager
        $em = $this->container->get('em');
        
        // Create query builder
        $qb = $em->createQueryBuilder();
        
        // Set all tokens from this user as invalid
        $qb->update('API\Models\Entity\Users\UserToken', 't')
           ->set('t.is_valid', 'false')
           ->where("t.user = {$user_id}")
           ->andWhere("t.is_valid = true")
           ->getQuery()
           ->execute();
    }
}
