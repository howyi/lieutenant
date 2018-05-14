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

// BROWSER
$router->get('/', function () use ($router) {
    return $router->app->version();
});


// API
$router->group([
    // 'middleware' => ['api'],
    'prefix'     => 'auth'
], function ($router) {
    $router->get('{service}/redirect', 'AuthController@redirect');
    $router->get('{service}/callback', 'AuthController@callback');

    $router->group(['middleware' => ['jwt.auth']], function ($router) {
        $router->post('refresh', 'AuthController@refresh');
        $router->post('logout', 'AuthController@logout');
        $router->get('me', 'AuthController@me');
    });
});