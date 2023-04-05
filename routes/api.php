<?php

use App\Http\Controllers\api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Customer's routes
Route::get('/customers', [UserController::class, 'getAllCustomers']);
Route::get('/customers/{id}', [UserController::class, 'getCustomerById']);
Route::post('/customers', [UserController::class, 'createNewCustomer']);
Route::put('/customers/{id}', [UserController::class, 'updateCustomer']);
Route::delete('/customers/{id}', [UserController::class, 'hardDeleteCustomer']);
Route::get('/customers-search', [UserController::class, 'searchPaginationCustomers']);

// Provider's routes
// Route::get('/providers', [UserController::class, 'getAllProviders']);
// Route::get('/providers/{id}', [UserController::class, 'getProviderById']);
// Route::post('/providers', [UserController::class, 'createNewProvider']);
// Route::put('/providers/{id}', [UserController::class, 'updateProvider']);
// Route::delete('/providers/{id}', [UserController::class, 'hardDeleteProvider']);

// Category's routes
// Route::get('/categories', [UserController::class, 'getAllCategories']);
// Route::get('/categories/{id}', [UserController::class, 'getCategoriesById']);
// Route::post('/categories', [UserController::class, 'createNewCategories']);
// Route::put('/categories/{id}', [UserController::class, 'updateCategories']);
// Route::delete('/categories/{id}', [UserController::class, 'hardDeleteCategories']);

// Route::post('/register', [UserController::class, 'register']);

// Auth routes
Route::group([

	'middleware' => 'api',
	'prefix'     => 'auth',

], function ($router) {

	Route::post('login', [AuthController::class, 'login']);
	// Route::post('logout', 'AuthController@logout');
	// Route::post('refresh', 'AuthController@refresh');
	// Route::post('me', 'AuthController@me');
});
