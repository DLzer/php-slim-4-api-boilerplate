<?php

use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Factory\AppFactory;

return [
    // Settings
    Configuration::class => function () {
        return new Configuration(require __DIR__ . '/settings.php');
    },

    // Database connection
    Connection::class => function (ContainerInterface $container) {
        $factory = new ConnectionFactory(new IlluminateContainer());

        $connection = $factory->make($container->get(Configuration::class)->getArray('db'));

        // Disable the query log to prevent memory issues
        $connection->disableQueryLog();

        return $connection;
    },

    // Initialize PDO
    PDO::class => function (ContainerInterface $container) {
        return $container->get(Connection::class)->getPdo();
    },

    // DI Container to App
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // We'll only set this if we plan on running the app in a sub-directory
        // The public directory must not be part of the base path
        //$app->setBasePath('/slim4app');

        return $app;
    },

];