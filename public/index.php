<?php
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable( YAPI_ROOT );
$dotenv->safeLoad();

var_dump($_ENV);
die;

$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->post('/', function (Request $req, Response $res) {
    $res->getBody()->write(json_encode([
        'date' => date("YmdHis"),
        'method' => 'POST'
    ]));
    return $res;
});

$app->get('/', function (Request $req, Response $res) {
    $res->getBody()->write(json_encode([
        'date' => date("YmdHis")
    ]));
    return $res;
});

$app->get('/hello/{name}', function (Request $req, Response $res, $args) {
    $name = $args['name'];
    $res->getBody()->write(json_encode([
        'name' => $name,
        'date' => date("YmdHis")
    ]));
    return $res;
});

// $container = new DI\Container();

// $app = \DI\Bridge\Slim\Bridge::create($container);

// $app->get('/', function (Request $request, Response $response) {
//     $response->getBody()->write(json_encode([
//         'name' => 'Fabio',
//         'surname' => 'Goto',
//         'age' => 35
//     ]));
//     return $response;
// });

// $app->run();

$app->run();