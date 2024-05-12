<?php
    use Slim\App;
    use App\controllers\AccountController;

    return function (App $app) {
        $app->post('/reset', [AccountController::class, 'reset']);
        $app->get('/balance', [AccountController::class, 'getBalance']);
        $app->post('/event', [AccountController::class, 'resolveEvent']);
    };
?>