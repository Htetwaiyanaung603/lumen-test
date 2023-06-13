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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->get('/', [
    'as' => 'test', 'uses' => 'TestController@index'
]);

$router->post('/add/image', [ 'as' => 'imageAdd', 'uses' => 'TestController@addImage']);
$router->get('/get/photo', [ 'as' => 'imageGet', 'uses' => 'TestController@getImage']);

$router->get('/showVideo/{id}', [ 'as' => 'showVideo', 'uses' => 'VideoController@getVideo']);
$router->post('/storeVideo', [ 'as' => 'storeVideo', 'uses' => 'VideoController@videoStore']);

$router->get('/getStudentList', [ 'as' => 'getAPI', 'uses' => 'HttpController@index']);
$router->post('/storeStudent', [ 'as' => 'store.student', 'uses' => 'HttpController@store']);
$router->get('/editStudent/{id}', [ 'as' => 'store.edit', 'uses' => 'HttpController@edit']);
$router->put('/updateStudent', [ 'as' => 'store.update', 'uses' => 'HttpController@update']);
$router->delete('/deleteStudent/{id}', [ 'as' => 'store.delete', 'uses' => 'HttpController@delete']);

$router->post('/add/video', [ 'as' => 'addVideo', 'uses' => 'VideoController@addVideo']);
$router->post('/edit/video', [ 'as' => 'editVideo', 'uses' => 'VideoController@editVideo']);
$router->delete('/delete/video/{id}', [ 'as' => 'deleteVideo', 'uses' => 'VideoController@deleteVideo']);
$router->get('/show/video/{id}', [ 'as' => 'showVideo', 'uses' => 'VideoController@showVideo']);

$router->get('/indexVideo', [ 'as' => 'indexVideo', 'uses' => 'VideoController@indexVideo']);
$router->post('/vueUpload', ['as' => 'vueUpload', 'uses' => 'VideoController@uploadVue']);
$router->get('/showVideos/{id}', [ 'as' => 'showVideo', 'uses' => 'VideoController@showVueVideo']);
$router->get('/showVideoFile', [ 'as' => 'showVideoFile', 'uses' => 'VideoController@showVideos']);
$router->put('/editVideoData/{id}', [ 'as' => 'updateVideoData', 'uses' => 'VideoController@updateVideoData']);
$router->post('/editVideo', [ 'as' => 'updateVideo', 'uses' => 'VideoController@updateVideo']);

