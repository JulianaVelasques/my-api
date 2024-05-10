<?php
    use Slim\App;
    use App\controllers\AccountController;

    return function (App $app) {
        $app->get('/reset', [AccountController::class, 'reset']);
    };

?>