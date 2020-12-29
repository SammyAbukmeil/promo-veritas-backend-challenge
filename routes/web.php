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

$router->group(['prefix' => 'api'], function () use ($router) {
    // Promotion routes
    $router->get('promotions', ['uses' => 'PromotionController@getAllPromotions']);

    $router->get('promotion/{client}', ['uses' => 'PromotionController@getPromotionByClient']);

    $router->post('promotion/{client}', ['uses' => 'PromotionController@create']);

    $router->put('promotion/{id}', ['uses' => 'PromotionController@update']);

    $router->delete('promotion/{id}', ['uses' => 'PromotionController@delete']);    

    // Entrant routes
    $router->post('entrant/winning-moment', ['uses' => 'EntrantController@checkWinningMomentWinner']);

    $router->post('entrant/chance', ['uses' => 'EntrantController@checkChanceWinner']);
  });