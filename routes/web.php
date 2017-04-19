<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return 'Welcome to wechat auth system!';
});

$app->group( ['middleware' => App\Http\Middleware\SignMiddleware::class], function() use ( $app ) {
    // 验证入口
    $app->get( '/auth', 'IndexController@auth' );
} );

// 微信授权回调
$app->get( '/notify', 'IndexController@notify' );