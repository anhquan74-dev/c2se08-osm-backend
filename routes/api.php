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
use App\Http\Controllers\api\AttachPhotoController;
use Illuminate\Support\Facades\Route;

// Customer's routes
Route::get('/customers', [UserController::class, 'getAllCustomers']);
Route::get('/customers/{id}', [UserController::class, 'getCustomerById']);
Route::post('/customers', [UserController::class, 'createNewCustomer']);
Route::post('/customers/{id}', [UserController::class, 'updateCustomer']);
Route::post('/hard-delete-customer/{id}', [UserController::class, 'hardDeleteCustomer']);
Route::get('/customers-search', [UserController::class, 'searchPaginationCustomers']);

// Provider's routes
Route::get('/providers', [UserController::class, 'getAllProviders']);
Route::get('/providers/{id}', [UserController::class, 'getProviderById']);
Route::post('/providers', [UserController::class, 'createNewProvider']);
Route::post('/providers/{id}', [UserController::class, 'updateProvider']);
Route::post('/hard-delete-provider/{id}', [UserController::class, 'hardDeleteProvider']);

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
Route::post('/hard-delete-post/{id}', [PostController::class, 'hardDeletePost']);

// Location's routes
Route::get('/locations', [LocationController::class, 'getAllLocations']);
Route::get('/locations/{id}', [LocationController::class, 'getLocationById']);
Route::get('/locations-by-user/{user_id}', [LocationController::class, 'getAllLocationsByUserId']);
Route::post('/locations', [LocationController::class, 'createNewLocation']);
Route::post('/locations/{id}', [LocationController::class, 'updateLocation']);
Route::post('/hard-delete-location/{id}', [LocationController::class, 'hardDeleteLocation']);

// Service's routes
Route::get('/services', [ServiceController::class, 'getAllServices']);
Route::get('/services/{id}', [ServiceController::class, 'getServiceById']);
Route::get('/services-by-provider/{provider_id}', [ServiceController::class, 'getAllServicesByProviderId']);
Route::get('/services-by-category/{category_id}', [ServiceController::class, 'getAllServicesByCategoryId']);
Route::post('/services', [ServiceController::class, 'createNewService']);
Route::post('/services/{id}', [ServiceController::class, 'updateService']);
Route::post('/hard-delete-service/{id}', [ServiceController::class, 'hardDeleteService']);

// Package's routes
Route::get('/packages', [PackageController::class, 'getAllPackages']);
Route::get('/packages/{id}', [PackageController::class, 'getPackageById']);
Route::get('/packages-by-service/{service_id}', [PackageController::class, 'getAllPackagesByServiceId']);
Route::post('/packages', [PackageController::class, 'createNewPackage']);
Route::post('/packages/{id}', [PackageController::class, 'updatePackage']);
Route::post('/hard-delete-package/{id}', [PackageController::class, 'hardDeletePackage']);

// Appointment's routes
Route::get('/appointments', [AppointmentController::class, 'getAllAppointments']);
Route::get('/appointments/{id}', [AppointmentController::class, 'getAppointmentById']);
Route::get('/appointments-by-customer/{customer_id}', [AppointmentController::class, 'getAllAppointmentsByCustomerId']);
Route::get('/appointments-by-package/{package_id}', [AppointmentController::class, 'getAllAppointmentsByPackageId']);
Route::post('/appointments', [AppointmentController::class, 'createNewAppointment']);
Route::post('/appointments/{id}', [AppointmentController::class, 'updateAppointment']);
Route::post('/hard-delete-appointment/{id}', [AppointmentController::class, 'hardDeleteAppointment']);

// Feedback's routes
Route::get('/feedbacks', [FeedbackController::class, 'getAllFeedbacks']);
Route::get('/feedbacks/{id}', [FeedbackController::class, 'getFeedbackById']);
Route::get('/feedbacks-by-appointment/{appointment_id}', [FeedbackController::class, 'getAllFeedbacksByAppointmentId']);
Route::post('/feedbacks', [FeedbackController::class, 'createNewFeedback']);
Route::post('/feedbacks/{id}', [FeedbackController::class, 'updateFeedback']);
Route::post('/hard-delete-feedback/{id}', [FeedbackController::class, 'hardDeleteFeedback']);

// Attach photo's routes
Route::post('/attach-photos', [AttachPhotoController::class, 'createAttachPhoto']);
Route::post('/hard-delete-attach-photo/{id}', [AttachPhotoController::class, 'hardDeleteAttachPhoto']);

// Banner's routes
Route::post('/banners', [BannerController::class, 'createBanner']);
Route::post('/hard-delete-banner/{id}', [BannerController::class, 'hardDeleteBanner']);

// need test
// Auth routes
// Route::post('/register', [UserController::class, 'register']);
Route::group([
	'middleware' => 'api',
	'prefix'     => 'auth',
], function ($router) {
	Route::post('login', [AuthController::class, 'login']);
	Route::post('logout', [AuthController::class, 'logout']);
	Route::post('refresh', [AuthController::class, 'refresh']);
	Route::post('me', [AuthController::class, 'me']);
});
