<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;


Route::post('/register', [UsersController::class, 'register']);
Route::post('/login', [UsersController::class, 'login']);
Route::get('/dashboard', [UsersController::class, 'dashboard']);
Route::post('/logout', [UsersController::class, 'logout']);
Route::apiResource('courses', CourseController::class);
Route::middleware('auth:api')->post('/enrollments', [EnrollmentController::class, 'enrollments']);
// Route::middleware('auth:api')->get('/users', [UserController::class, 'index']);
Route::get('/users', [UsersController::class, 'index']);

