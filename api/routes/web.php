<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([ 'prefix' => 'users' ], function () use ($router) {
    $router->post('', 'UserController@createItem');
    $router->get('', ['middleware' => 'token', 'uses' => 'UserController@getItems']);
    $router->get('{id}', [ 'middleware' => 'token', 'uses' => 'UserController@getItem']);
    $router->put('{id}', [ 'middleware' => 'token', 'uses' => 'UserController@updateItem']);
});

$router->group([ 'prefix' => 'tokens' ], function () use ($router) {
    $router->post('issue', 'TokenController@issue');
    $router->post('validate', 'TokenController@check');
});

$router->group([ 'prefix' => 'otp'], function () use ($router) {
    $router->post('', 'OTPController@createItem');
});