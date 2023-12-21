# Superban
The package offers a middleware named "superban," allowing you to block users from your application. This middleware assesses whether a user is banned and, if so, triggers a UserBannedException. You can catch this exception in your app/Exceptions/Handler.php file and redirect the user to a designated page.
# Installation
You can install the package via Composer:

```bash
composer require davematics/superban
```

Manually add the service provider to the providers array in config/app.php:

```bash
'Superban' => Davematics\Superban\Facades\Superban::class,
```

After installation, publish the configuration file:

```bash
 php artisan vendor:publish --provider="Davematics\Superban\SuperbanServiceProvider"
```
This will publish a superban.php file in your config directory.
<br>
Here you can Configure different cache drivers. - Redis, Database, etc.

# USAGE
```bash
Route::middleware(['superban:300,5,2880'])->group(function () {

 Route::post('/add-interest', [App\Http\Controllers\SomeController::class, 'index']);

});
```
The middleware utilizes Laravel's RateLimiter class to track the number of attempts a user makes to access a resource within a specific time frame. <br>
If the user surpasses the limit, the middleware generates a key based on the user's email, ID, or IP address and stores it in the cache for the designated time period. If the key is found in the cache, the middleware raises a UserBannedException.
<br><br>
#Example
The "superban" middleware accepts three parameters: <br>

The first parameter is the number of attempts a user can make before being banned. <br>
The second parameter is number of minutes during which the user can attempt the route before facing a ban. <br>
The last parameter is the number of minutes the user is banned. <br>

On the route you can chnage the parameters 300, 5, 2880 based to fit in your specification.

#Tests
To run the package tests, use the following bash command:

```bash
./vendor/bin/phpunit
```
# Security
If you find any security-related concerns, kindly reach out via email to: davgwuche@gmail.com.
