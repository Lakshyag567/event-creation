<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExcelUploadController;

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

// Excel File
Route::post('event/bulk-upload', [ExcelUploadController::class, 'upload']);
// Event BREAD
Route::resource('event', EventController::class)->only(['index', 'store', 'show', 'update', 'destroy']);