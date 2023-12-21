<?php

namespace Davematics\Superban\Src\Test;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Davematics\Superban\Http\Middleware\SuperbanMiddleware;


class SuperbanMiddlewareTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    protected function getPackageProviders($app)
    {
        return ['Davematics\Superban\SuperbanServiceProvider'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set up your testing environment
    }

    public function testBannedUserGetsForbiddenResponse()
    {
        // Arrange
        Cache::shouldReceive('put')->once();
        Cache::shouldReceive('get')->andReturn(true);


        // Create a dummy route
        Route::post('/testroute', function () {
            return response()->json(['message' => 'This route should not be accessible.']);
        })->middleware('superban:1,5,1');

        // Create a dummy request
        $request = Request::create('/testroute', 'POST');

        // Act
        $response = $this->app->handle($request);

        // Assert
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertEquals('You are banned.', json_decode($response->getContent())->message);
    }



}
