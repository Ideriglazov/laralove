<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ipController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route::get('/', [ipController::class, 'index']);
Route::get('/', [PostController::class, 'index']);//При открытии главной страницы сайта вызывается метод index из PostController

//Route::resource('/post',PostController::class);

Route::get('post/', [PostController::class, 'index'])->name('post.index');//Этот маршрут используется в качестве ссылки на главную страницу
Route::get('post/create', [PostController::class, 'create'])->name('post.create');//маршрут для вызова метода create, вызывается в файле layout.blade.php
Route::get('post/show/{id}', [PostController::class, 'show'])->name('post.show');//маршрут для вызова метода show, в него передается параметр id, и в зависимости от этого параметра будет открываться соответствующий пост
Route::get('post/edit/{id}', [PostController::class, 'edit'])->name('post.edit');//маршрут для вызова метода edit, в него передается параметр id
Route::post('post/', [PostController::class, 'store'])->name('post.store');//маршрут для вызова метода store, вызывается в файле create.blade.php
Route::patch('post/show/{id}', [PostController::class, 'update'])->name('post.update');//маршрут для вызова метода update. Здесь мы ипользуем patch, а не get потому что метод update вносит изменения в существующий в базе данных пост
Route::delete('post/{id}', [PostController::class, 'destroy'])->name('post.destroy');//маршрут для вызова метода destroy. Здесь мы ипользуем delete, потому что метод destroy удаляет пост
Route::get('post/show_ip', [PostController::class, 'show_ip'])->name('post.show_ip');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/cache-clear', function() {
    Artisan::call('cache:clear');
    return '<h1>Cache cleared</h1>';
    // Do whatever you want either print a message or exit
});

Route::get('/config-clear', function() {
    Artisan::call('config:clear');
    return '<h1>Configurations cleared</h1>';
    // Do whatever you want either a print a message or exit
});

Route::get('/serve', function() {
    Artisan::call('serve');
    // Do whatever you want either a print a message or exit
});
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});
/*Route::get('/migrate-fresh', function() {
    Artisan::call('migrate:fresh');
    // Do whatever you want either a print a message or exit
});*/
Route::get('/model-create', function() {
    Artisan::call('make:model test -m');
    return '<h1>model created</h1>';
    // Do whatever you want either a print a message or exit
});
/*Route::get('/db-seed', function() {
    Artisan::call('db:seed');
    return '<h1>seed</h1>';
    // Do whatever you want either a print a message or exit
});*/
Route::get('/migrate', function() {
    Artisan::call('migrate');
    return '<h1>migration</h1>';
    // Do whatever you want either a print a message or exit
});
Route::get('/controller', function() {
    Artisan::call('make:controller ipController');
    return '<h1>controller</h1>';
    // Do whatever you want either a print a message or exit
});
Route::get('/idehelper', function() {
    Artisan::call('ide-helper:generate');
    return '<h1>idehelper</h1>';
    // Do whatever you want either a print a message or exit
});