<?php
use Illuminate\Contracts\Routing\ResponseFactory;
require_once __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/helpers.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

$app->configure('view');

$app->configure('filesystems');

// class_alias('Illuminate\Support\Facades\Storage', 'Storage');

//Response api use macro
$app->bind(ResponseFactory::class, function ($app) {
    return $app->make(\Laravel\Lumen\Http\ResponseFactory::class);
});

$app->make(ResponseFactory::class)->macro('success', function ($data = null, $message, $status) {
    return $this->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], $status);
});

$app->make(ResponseFactory::class)->macro('error', function ($message, $status = 400) {
    return $this->json([
        'success' => false,
        'message' => $message,
    ], $status);
});
//end respons api

$app->bind(
    Illuminate\Contracts\View\Factory::class,
    function ($app) {
        return $app->make('view');
    }
);

$app->view->addLocation(__DIR__.'/../resources/views');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');

// $app->configure('session');
// $app->alias('session', Illuminate\Session\SessionManager::class);
// $app->alias('session.store', Illuminate\Session\Store::class);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    // App\Http\Middleware\ExampleMiddleware::class
    App\Http\Middleware\CorsMiddleware::class
]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);



/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
$app->register(Illuminate\View\ViewServiceProvider::class);
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(Exula\Ceph\CephStorageServiceProvider::class); //support ceph for driver
$app->register(Pion\Laravel\ChunkUpload\Providers\ChunkUploadServiceProvider::class);
// $app->register(App\Providers\ResponseMacroServiceProvider::class);

// $app->register(\App\Providers\ChunkUploadServiceProvider::class);
// $app->register(Illuminate\Session\SessionServiceProvider::class);



/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
