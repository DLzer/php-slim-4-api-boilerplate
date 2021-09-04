<?php

namespace App\Test\TestCase\Action\Home;

use App\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * Test.
 */
class HomeActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @return void
     */
    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->app->handle($request);

        // Assert: Redirect
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * Test invalid link.
     *
     * @return void
     */
    public function testPageNotFound(): void
    {
        $request = $this->createRequest('GET', '/nada');
        $response = $this->app->handle($request);

        // Assert: Not found
        $this->assertSame(404, $response->getStatusCode());
    }
}