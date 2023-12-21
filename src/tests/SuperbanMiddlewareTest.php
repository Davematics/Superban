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
    
    protected function getPackageProviders($app)
    {
        return ['Davematics\Superban\SuperbanServiceProvider'];
    }

    protected function setUp(): void
    {
        parent::setUp();

       
    }

    public function testBannedUserGetsForbiddenResponse()
    {
        
        Cache::shouldReceive('put')->once();
        Cache::shouldReceive('get')->andReturn(true);


       
        Route::post('/testroute', function () {
            return response()->json(['message' => 'This route should not be accessible.']);
        })->middleware('superban:1,5,1');

      
        $request = Request::create('/testroute', 'POST');

        
        $response = $this->app->handle($request);

       
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertEquals('You are banned.', json_decode($response->getContent())->message);
    }



}
