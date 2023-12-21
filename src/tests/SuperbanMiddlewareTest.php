<?php

namespace Davematics\Superban\Src\Test;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiter;
use Mockery;
use Davematics\Superban\Http\Middleware\SuperbanMiddleware;


class SuperbanMiddlewareTest extends TestCase
{
    
    protected function getPackageProviders($app)
    {
        return ['Davematics\Superban\SuperbanServiceProvider'];
    }




    public function testBannedUserGetsForbiddenResponse()
    {

        Cache::shouldReceive('put')->once();
        Cache::shouldReceive('get')->andReturn(true);



        Route::post('/testroute', function () {
            return response()->json(['message' => 'This route should not be accessible.']);
        })->middleware('superban:1,1,1');


        $request = Request::create('/testroute', 'POST');


        $response = $this->app->handle($request);

        
        $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertEquals('You are banned.', json_decode($response->getContent())->message);
    }

   public function testUserMiddlewareLiftsBanAfterBanTime()
    {
        Carbon::setTestNow(Carbon::create(2023, 1, 1, 0, 0, 0));

        $routeClosure = function () {
            return response()->json(['message' => 'This route is accessible.']);
        };

        $this->app['router']->post('/testroute', $routeClosure)->middleware('superban:2,2,2');


        $request = Request::create('/testroute', 'POST');

        $response = $this->app->handle($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());


        Carbon::setTestNow(Carbon::create(2023, 1, 1, 0, 11, 0));

        $response =  $response = $this->app->handle($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

}
