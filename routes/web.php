<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\WorkshopController;
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

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/workshops', [WorkshopController::class, 'index']);
Route::get('/workshops/{slug}', [WorkshopController::class, 'show']);

Route::get('/instructors', [InstructorController::class, 'index']);
Route::get('/instructors/{slug}', [InstructorController::class, 'show']);

Route::get('/challenges', [ChallengeController::class, 'index']);
Route::get('/challenges/{slug}', [ChallengeController::class, 'show']);


require __DIR__ . '/auth.php';
