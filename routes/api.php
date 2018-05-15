<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['visitors'])->group(function () {
    /* Login Routes */
    Route::post('/login', 'Login\\LoginController@postLogin')->name('postLogin');

    /* Activation by email Routes */
    Route::get('/activation/{email}/{activationcode}', 'Login\\ActivationController@activateUser');

    /* Forgot Password Routes */
    Route::get('/forgotpassword', 'Login\\ForgetPasswordController@forgotPasword')->name('forgotpassword');
    Route::post('/forgotpassword', 'Login\\ForgetPasswordController@postForgotPassword')->name('postForgotpassword');

    /* Reset Routes */
    Route::get('/reset/{email}/{code}', 'Login\\ForgetPasswordController@resetPassword')->name('resetPassword');
    Route::post('/reset/{email}/{code}', 'Login\\ForgetPasswordController@postResetPassword')->name('postResetPassword');

});

Route::middleware(['auth:api'])->group(function () {
    Route::resource('departments', 'Api\\DepartmentsController', ['except' => ['create', 'edit']]);
    Route::resource('designations', 'Api\\DesignationsController', ['except' => ['create', 'edit']]);
    Route::resource('company', 'Api\\CompanyController', ['except' => ['create', 'edit']]);
    Route::resource('leave-type', 'Api\\LeaveTypeController', ['except' => ['create', 'edit']]);
    Route::resource('holiday', 'Api\\HolidayController', ['except' => ['create', 'edit']]);
    Route::resource('salary-setting', 'Api\\SalarySettingController', ['except' => ['create', 'edit']]);
    Route::resource('employee', 'Api\\EmployeeController', ['except' => ['create', 'edit']]);
    Route::resource('leave-request', 'Api\\LeaveRequestController', ['except' => ['create', 'edit']]);

    Route::get('/user/username-availability/{username}', 'AdminUserController@userNamecheck');
    Route::get('/user/email-availability/{email}', 'AdminUserController@userEmailcheck');
    Route::get('/user/employee-number-availability/{number}', 'AdminUserController@employeeNumberCheck');
});