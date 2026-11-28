<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AcademyController;
use App\Http\Controllers\Api\CourseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->apiResource('academies', AcademyController::class);

Route::middleware('auth:sanctum')->apiResource('courses', CourseController::class);

Route::get('/academy/{academy}/courses', [AcademyController::class, 'courses']);

Route::middleware('auth:sanctum')->get('/course/{course}/students', [CourseController::class, 'students']);

Route::middleware('auth:sanctum')->post(
    '/courses/{course}/enroll/{student}',
    [CourseController::class, 'enroll']
);

Route::middleware('auth:sanctum')->delete(
    '/courses/{course}/unenroll/{student}',
    [CourseController::class, 'unenroll']
);