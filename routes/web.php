<?php

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

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['visitors'])->group(function () {
    /* Register Routes */

//    Route::get('/register', 'RegistrationController@register')->name('register');
//    Route::post('/register', 'RegistrationController@postRegister')->name('postRegister');

    /* Login Routes */

    Route::get('/login', 'Login\LoginController@login')->name('login');
    Route::post('/login', 'Login\LoginController@postLogin')->name('postLogin');


    /* Activation by email Routes */

    Route::get('/activation/{email}/{activationcode}', 'Login\ActivationController@activateUser');

    /* Forgot Password Routes */

    Route::get('/forgotpassword', 'Login\ForgetPasswordController@forgotPasword')->name('forgotpassword');
    Route::post('/forgotpassword', 'Login\ForgetPasswordController@postForgotPassword')->name('postForgotpassword');

    /* Reset Routes */

    Route::get('/reset/{email}/{code}', 'Login\ForgetPasswordController@resetPassword')->name('resetPassword');
    Route::post('/reset/{email}/{code}', 'Login\ForgetPasswordController@postResetPassword')->name('postResetPassword');

});

//
//Route::middleware(['authcheck'])->group(function () {
//
//
//});