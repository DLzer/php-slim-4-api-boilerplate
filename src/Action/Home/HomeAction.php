<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as ServerResponse;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use App\Factory\LoggerFactory;

final class HomeAction
{

    /**
     * Logger Factory
     *
     * @var 
     */
    private $logger;

    public function __construct(LoggerFactory $logger) {
        $this->logger = $logger
        ->addFileHandler('app.log')
        ->createInstance('app_access');
    }

    public function __invoke(ServerRequest $request, ServerResponse $response): ServerResponse
    {
        $this->logger->info(sprintf('User Accessed Home At: %s', $_SERVER['HTTP_X_FORWARDED_FOR']));
        return $response->withJson(['response' => 'success', 'data' => 'Welcome Home...']);
    }
}