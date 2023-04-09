<?php

use App\Http\Controllers\api\AppointmentController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BannerController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\FeedbackController;
use App\Http\Controllers\api\LocationController;
use App\Http\Controllers\api\PackageController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\api\ServiceController;
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
Route::post('/providers/{id}', [UserController::class, 'updateProvider']);
Route::delete('/providers/{id}', [UserController::class, 'hardDeleteProvider']);

// Category's routes
Route::get('/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/categories/{id}', [CategoryController::class, 'getCategoryById']);
Route::post('/categories', [CategoryController::class, 'createNewCategory']);
Route::post('/categories/{id}', [CategoryController::class, 'updateCategory']);
Route::post('/hard-delete-category/{id}', [CategoryController::class, 'hardDeleteCategory']);

// Post's routes
Route::get('/posts', [PostController::class, 'getAllPosts']);
Route::get('/posts/{id}', [PostController::class, 'getPostById']);
Route::get('/posts-by-author/{author_id}', [PostController::class, 'getAllPostsByAuthorId']);
Route::post('/posts', [PostController::class, 'createNewPost']);
Route::post('/posts/{id}', [PostController::class, 'updatePost']);
Route::delete('/posts/{id}', [PostController::class, 'hardDeletePost']);

// Location's routes
Route::get('/locations', [LocationController::class, 'getAllLocations']);
Route::get('/locations/{id}', [LocationController::class, 'getLocationById']);
Route::get('/locations-by-user/{user_id}', [LocationController::class, 'getAllLocationsByUserId']);
Route::post('/locations', [LocationController::class, 'createNewLocation']);
Route::post('/locations/{id}', [LocationController::class, 'updateLocation']);
Route::delete('/locations/{id}', [LocationController::class, 'hardDeleteLocation']);

// Service's routes
Route::get('/services', [ServiceController::class, 'getAllServices']);
Route::get('/services/{id}', [ServiceController::class, 'getServiceById']);
Route::get('/services-by-provider/{provider_id}', [ServiceController::class, 'getAllServicesByProviderId']);
Route::get('/services-by-category/{category_id}', [ServiceController::class, 'getAllServicesByCategoryId']);
Route::post('/services', [ServiceController::class, 'createNewService']);
Route::post('/services/{id}', [ServiceController::class, 'updateService']);
Route::delete('/services/{id}', [ServiceController::class, 'hardDeleteService']);

// need test
// Package's routes
Route::get('/packages', [PackageController::class, 'getAllPackages']);
Route::get('/packages/{id}', [PackageController::class, 'getPackageById']);
Route::get('/packages-by-service/{provider_id}', [PackageController::class, 'getAllPackagesByServiceId']);
Route::post('/packages', [PackageController::class, 'createNewPackage']);
Route::post('/packages/{id}', [PackageController::class, 'updatePackage']);
Route::delete('/packages/{id}', [PackageController::class, 'hardDeletePackage']);

// Feedback's routes
Route::get('/feedbacks', [FeedbackController::class, 'getAllFeedbacks']);
Route::get('/feedbacks/{id}', [FeedbackController::class, 'getFeedbackById']);
Route::get('/feedbacks-by-appointment/{appointment_id}', [FeedbackController::class, 'getAllFeedbacksByAppointmentId']);
Route::post('/feedbacks', [FeedbackController::class, 'createNewFeedback']);
Route::post('/feedbacks/{id}', [FeedbackController::class, 'updateFeedback']);
Route::delete('/feedbacks/{id}', [FeedbackController::class, 'hardDeleteFeedback']);

// Appointment's routes
Route::get('/appointments', [AppointmentController::class, 'getAllAppointments']);
Route::get('/appointments/{id}', [AppointmentController::class, 'getAppointmentById']);
Route::get('/appointments-by-package/{package_id}', [AppointmentController::class, 'getAllAppointmentsByPackageId']);
Route::get('/appointments-by-customer/{customer_id}', [AppointmentController::class, 'getAllAppointmentsByCustomerId']);
Route::post('/appointments', [AppointmentController::class, 'createNewAppointment']);
Route::post('/appointments/{id}', [AppointmentController::class, 'updateAppointment']);
Route::delete('/appointments/{id}', [AppointmentController::class, 'hardDeleteAppointment']);






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
