<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApisController;
use App\Http\Controllers\API\AuthController;
use App\Models\Banks;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'API'], function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::prefix('forget/password')->group(function(){
        Route::post('/', [AuthController::class, 'forgetpassword']);

    });

    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [AuthController::class, 'resend'])->middleware('auth:api')->name('verification.resend');
    ///this routes must be login
    Route::middleware('jwt.verify')->group(function(){

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [ApisController::class, 'getUser']);
        Route::post('/user/update', [AuthController::class, 'updateUser']);


    });




});
