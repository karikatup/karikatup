<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

Route::bind('username', function($value){
    return \App\User::where('username', $value)->firstOrFail();
});

Route::bind('author', function($value){
    return \App\Author::where('slug', $value)->firstOrFail();
});

Route::bind('comic', function($value){
    return \App\Comic::where('slug', $value)->firstOrFail();
});

Route::bind('comment', function($value){
    return \App\Comment::findOrFail($value);
});

$api->version('v1', function(Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api){
        $api->post('register', 'App\Api\V1\Controllers\AuthController@register');
        $api->post('login', 'App\Api\V1\Controllers\AuthController@login');

        $api->post('send', 'App\Api\V1\Controllers\AuthController@send');
        $api->post('reset', 'App\Api\V1\Controllers\AuthController@reset');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api){
        $api->group(['prefix' => 'comic', 'middleware' => 'bindings'], function(Router $api){
            $api->post('create', 'App\Api\V1\Controllers\ComicController@store');
            $api->post('{comic}/comment', 'App\Api\V1\Controllers\ComicController@comment');
            $api->post('{comic}/comment/{comment}/delete', 'App\Api\V1\Controllers\ComicController@commentDestroy');
            $api->post('{comic}/like', 'App\Api\V1\Controllers\ComicController@like');
            $api->post('{comic}/unlike', 'App\Api\V1\Controllers\ComicController@unlike');
        });
    });

    $api->group(['prefix' => 'comic', 'middleware' => 'bindings'], function(Router $api){
        $api->get('/', 'App\Api\V1\Controllers\ComicController@index');
        $api->get('{comic}', 'App\Api\V1\Controllers\ComicController@show');
    });

    $api->group(['prefix' => 'user', 'middleware' => 'bindings'], function(Router $api){
        $api->get('{username}', 'App\Api\V1\Controllers\UserController@show');
    });

    $api->group(['prefix' => 'author', 'middleware' => 'bindings'], function(Router $api){
        $api->get('/', 'App\Api\V1\Controllers\AuthorController@index');
        $api->get('{author}', 'App\Api\V1\Controllers\AuthorController@show');
    });
});