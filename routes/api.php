<?php

use App\Http\Controllers\api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Customer's routes
Route::get('/customers', [UserController::class, 'getAllCustomers']);
Route::get('/customers/{id}', [UserController::class, 'getCustomerById']);
Route::post('/customers', [UserController::class, 'createNewCustomer']);
Route::post('/customers/{id}', [UserController::class, 'updateCustomer']);
Route::delete('/customers/{id}', [UserController::class, 'hardDeleteCustomer']);
Route::get('/customers-search', [UserController::class, 'searchPaginationCustomers']);

// Provider's routes
Route::get('/providers', [UserController::class, 'getAllProviders']);
Route::get('/providers/{id}', [UserController::class, 'getProviderById']);
Route::post('/providers', [UserController::class, 'createNewProvider']);
Route::put('/providers/{id}', [UserController::class, 'updateProvider']);
Route::delete('/providers/{id}', [UserController::class, 'hardDeleteProvider']);

// Category's routes
Route::get('/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/categories/{id}', [CategoryController::class, 'getCategoriesById']);
Route::post('/categories', [CategoryController::class, 'createNewCategories']);
Route::put('/categories/{id}', [CategoryController::class, 'updateCategories']);
Route::delete('/categories/{id}', [CategoryController::class, 'hardDeleteCategories']);

// Post's routes
Route::get('/posts', [PostController::class, 'getAllPosts']);
Route::get('/posts/{id}', [PostController::class, 'getPostsById']);
Route::post('/posts', [PostController::class, 'createNewPosts']);
Route::put('/posts/{id}', [PostController::class, 'updatePosts']);
Route::delete('/posts/{id}', [PostController::class, 'hardDeletePosts']);

// Banner's routes
Route::post('/banners', [BannerController::class, 'createMultipleBanners']);



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
