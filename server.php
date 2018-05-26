<?php

$http = new swoole_http_server("127.0.0.1", 9501);

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$http->on('request', function ($request, $response) use($app, $kernel) {
    $_SERVER = [];
    $_SERVER['argv'] = [];
    $_GET = [];
    $_POST = [];

    if (isset($request->server)) {
        foreach ($request->server as $k => $v) {
            $_SERVER[$k] = $v;
        }
    }

    if (isset($request->header)) {
        foreach ($request->header as $k => $v) {
            $_SERVER[$k] = $v;
        }
    }

    if (isset($request->get)) {
        foreach ($request->get as $k => $v) {
            $_GET[$k] = $v;
        }
    }

    if (isset($request->post)) {
        foreach ($request->post as $k => $v) {
            $_POST[$k] = $v;
        }
    }

    ob_start();

    $response_laravel = $kernel->handle(
       $request_laravel = Illuminate\Http\Request::capture()
   );

    $response_laravel->send();

    $kernel->terminate($request_laravel, $response_laravel);

    $result = ob_get_contents();

    ob_clean();

    $response->end($result);

});

$http->start();