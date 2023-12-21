<?php

namespace Davematics\Superban\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

class SuperbanMiddleware
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $limit, $decayMinutes, $banDuration)
    {
        $key = $this->resolveRateLimiterKey($request);

        $decaySeconds = (int) ($decayMinutes * 60);

        if (
            $this->limiter->attempt($key, $limit, function () use ($decaySeconds) {
                return $decaySeconds;
            })
        ) {
            if ($this->limiter->tooManyAttempts($key, $limit)) {
                $this->banClient($request, $banDuration);

                // returning response
                return response()->json(['message' => 'You are banned.'], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }

    protected function resolveRateLimiterKey($request)
    {
        return md5($request->ip() . $request->userAgent());
    }

    protected function banClient($request, $banDuration)
    {
        //gettting the key
        $key = $this->resolveRateLimiterKey($request);

        // store a flag in cache indicating that the user is banned
        Cache::put("banned:$key", true, $banDuration);

        //clear the rate limiter attempts for this user
        $this->limiter->resetAttempts($key);
    }
}
