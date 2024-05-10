<?php
// use - import classes and interfaces from other namespaces
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Autoload the needed dependencies from vendor
require __DIR__ . '/../vendor/autoload.php';

// Create slim app instance
$app = AppFactory::create();

// Define the routes
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

// Run the app
$app->run();