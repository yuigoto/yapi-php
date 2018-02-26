<?php
namespace API\Controllers;

use API\Core\Salt;
use API\Core\ResponseTemplate;
use API\Models\Entity\Users\UserToken;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Container;
use Slim\Http\Response;

/**
 * YAPI : API\Controllers\AuthController
 * ----------------------------------------------------------------------
 * Handles authentication and validation endpoints.
 * 
 * @package     API\Controllers
 * @author      Fabio Y. Goto <lab@yuiti.com.br>
 * @copyright   2018 Fabio Y. Goto
 * @since       0.0.2
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
     *      Slim application reference
     */
    public function __construct(App &$app) 
    {
        // Set container reference
        $this->container = $app->getContainer();

        // Keep a reference to this
        $ctrl = $this;

        // Define routes
        $app->group('/auth', function () use ($app, $ctrl) {
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
        // Get username/e-mail address and password
        $credentials = $this->userLoginCredentials($request);

        // Did the user provide e-mail/username?
        if (!isset($credentials['username']) && !isset($credentials['email'])) {
            throw new \Exception('No username/e-mail address provided.', 401);
        }

        // Get entity manager
        $em = $this->container->get('em');

        // Get user from repository
        $user = $em->getRepository("API\Models\Entity\Users\User");

        // Search args
        $args = [];
        if (isset($credentials['email'])) {
            $args['email'] = $credentials['email'];
        }
        if (isset($credentials['username'])) {
            $args['username'] = $credentials['username'];
        }

        // Fetch user
        $user = $user->findOneBy($args);

        // Invalid user
        if ($user === null || !$user) {
            throw new \Exception(
                'Invalid username/e-mail address.', 
                401
            );
        }

        // Retrieve password hash
        $pass = explode(".", $user->getPassword());

        // Invalid password
        if (
            !$credentials['password'] 
            || !\password_verify($credentials['password'], $pass[0]) 
            || $pass[1] !== Salt::get()
        ) {
            throw new \Exception(
                'Invalid password provided.', 
                401
            );
        }

        // Set any previous token as invalid
        $this->invalidateAllTokens($user->getId());

        // Set token values
        $token = [
            'payload' => $user->getTokenPayload(), 
            'created' => time(), 
            'expires' => time() + (60 * 60 * 24 * 7)
        ];

        // Sign token
        $jwt = JWT::encode($token, Salt::get());

        // Save the token in the database
        $save = (new UserToken())
            ->setExpires($token['expires'])
            ->setIsValid(true)
            ->setToken($jwt)
            ->setUser($user);
        $em->persist($save);
        $em->flush();

        // Return response
        $return = new ResponseTemplate(
            200, 
            [
                "token" => $jwt
            ], 
            false
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($return, 200);
    }

    /**
     * Validates a user token, returning the payload information.
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
        // Fetch token
        $token = ($request->getHeader('Authorization')[0]);

        // Is token not empty?
        if ($token === null || $token === false || $token === "") {
            throw new \Exception('No user token provided.', 401);
        }

        // Decode
        try {
            $jwt = JWT::decode($token, Salt::get(), ['HS256']);
        } catch (\Exception $e) {
            throw new \Exception('Invalid user token provided.', 401);
        }

        // Return the payload, if valid
        $return = new ResponseTemplate(
            200, 
            (array) $jwt, 
            false
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($return);
    }

    // Private Methods
    // ------------------------------------------------------------------

    /**
     * Invalidates all previous tokens for the user ID provided.
     *
     * @param integer $id
     * @return void
     */
    protected function invalidateAllTokens(int $id) 
    {
        // Get entity manager + query builder
        $em = $this->container->get('em');
        $qb = $em->createQueryBuilder();

        // Set all tokens as invalid
        $qb->update("API\Models\Entity\Users\UserToken", 't')
            ->set('t.is_valid', 'false')
            ->where("t.user = {$id}")
            ->andWhere('t.is_valid = true')
            ->getQuery()
            ->execute();
    }

    /**
     * Returns credentials used to login.
     *
     * @param Request $request
     *      Server request object
     * @return array
     */
    protected function userLoginCredentials(Request $request): array 
    {
        // Fetch params
        $params = ($request->getMethod() === 'GET') 
            ? $request->getQueryParams() : $request->getParsedBody();
        
        // Will hold user data
        $user = [];

        // Get username or e-mail address
        if (isset($params['email'])) {
            // Extract e-mail address
            $user['email'] = trim($params['email']);
        } elseif (isset($params['user']) || isset($params['username'])) {
            // Extract value
            $curr = (isset($params['user'])) 
                ? trim($params['user']) : trim($params['username']);
            
            // Checks if it's an e-mail or not
            if (filter_var($curr, FILTER_VALIDATE_EMAIL)) {
                $user['email'] = $curr;
            } else {
                $user['username'] = $curr;
            }
        }

        // Only fetch password if username is set
        if (isset($user['username']) || isset($user['email'])) {
            if (isset($params['pass']) || isset($params['password'])) {
                $user['password'] = (isset($params['pass'])) 
                    ? trim($params['pass']) : trim($params['password']);
            }
        }

        return $user;
    }
}
