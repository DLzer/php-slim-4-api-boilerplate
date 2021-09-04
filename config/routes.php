<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get('/', \App\Action\Home\HomeAction::class);


        // Users Route Group
    $app->group('/users', function(RouteCollectorProxy $app) {
        $app->get('', \App\Action\User\UserFindAction::class);
        $app->get('/{user_id}', \App\Action\User\UserReadAction::class);
    });
};