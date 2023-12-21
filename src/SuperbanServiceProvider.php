<?php


namespace Davematics\Superban;

use Illuminate\Support\ServiceProvider;
use Davematics\Superban\Http\Middleware\SuperbanMiddleware;
class SuperbanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/superban.php' => config_path('superban.php'),
        ], 'config');

        $this->registerMiddleware();
    }

    protected function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('superban', SuperbanMiddleware::class);
    }
}
