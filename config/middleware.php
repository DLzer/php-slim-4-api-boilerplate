<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Selective\BasePath\BasePathMiddleware;
// use Selective\Validation\Middleware\ValidationExceptionMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(function (Request $request, RequestHandlerInterface $handler): Response {
        $response = $handler->handle($request);
        $response = $response->withHeader('Content-Type', 'Content-Type: application/json');
        return $response;
    });
    // $app->add(ValidationExceptionMiddleware::class);
    $app->add(BasePathMiddleware::class);
    $app->add(ErrorMiddleware::class);
};