<?php
    use Slim\Factory\AppFactory;

    // Autoload the needed dependencies from vendor
    require __DIR__ . '/vendor/autoload.php';

    // Start session so we can persist the accounts values between requests
    session_start();

    // Get routes
    $routes = require __DIR__ . '/src/routes/routes.php';

    // Create slim app instance
    $app = AppFactory::create();

    // Config the routes
    $routes($app);

    // Run the app
    $app->run();
?>