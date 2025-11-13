<?php

use Illuminate\Support\Facades\Route;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/forgot-password', function () { //Esto no funciona aun mirar documentación de bootstrap https://laravel.com/docs/11.x/passwords y  https://laravel.com/docs/11.x/requests#configuring-trusted-hosts
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');
Route::post('/reset-password', 'UsersController@updatePassword')->name('password.update');
