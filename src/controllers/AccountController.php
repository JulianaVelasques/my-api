<?php     
    namespace App\controllers;

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    class AccountController {
        public function reset(Request $request, Response $response, $args) {
            $response->getBody()->write("Hello world from reset!");
            return $response;
        }
    }
?>