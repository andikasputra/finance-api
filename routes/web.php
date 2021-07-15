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

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});

$router->group(['prefix' => 'finance', 'middleware' => 'auth'], function () use ($router) {
    $router->get('/', 'FinanceController@index');
    $router->post('/', 'FinanceController@store');
    $router->get('/{id}', 'FinanceController@show');
    $router->put('/{id}', 'FinanceController@update');
    $router->post('/{id}/images', 'FinanceController@uploadImage');
    $router->delete('/{id}', 'FinanceController@destroy');
});
