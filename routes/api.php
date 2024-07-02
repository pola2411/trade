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
        Route::post('/verifyOtp', [AuthController::class, 'verifyOtp_for_change_pass']);
        Route::post('/change/password', [AuthController::class, 'changepass']);
    });

    Route::get('/interest_calc',[ApisController::class,'interest_calc']);
    Route::get('/programs',[ApisController::class,'programs']);
    Route::get('/program/details/{id}',[ApisController::class,'program_details']);
    Route::get('/program/contracts/{id}',[ApisController::class,'program_contract']);
    Route::get('/contract/{id}',[ApisController::class,'contract']);
    Route::get('/banks',[ApisController::class,'Banks']);
    Route::post('/upload/file',[ApisController::class,'upload_file']);
    Route::post('/check/phone',[AuthController::class,'checphone']);
    Route::post('/verifyOtp/For/Register',[AuthController::class,'verifyOtpForRegister']);



    ///this routes must be login
    Route::middleware('jwt.verify')->group(function(){
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'sendOtp']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/store/order',[ApisController::class,'order_store']);
        Route::get('/order/status',[ApisController::class,'status_order']);
        Route::get('/program/types',[ApisController::class,'program_type']);
        Route::get('/filter',[ApisController::class,'filter']);
        Route::get('/user', [ApisController::class, 'getUser']);
        Route::post('/user/update', [AuthController::class, 'updateUser']);
        Route::post('/all/orders',[ApisController::class,'all_orders']);
        Route::get('/all/orders/last',[ApisController::class,'last_order']);

        Route::get('/notafication/count',[ApisController::class,'conter_nota']);
        Route::get('/notafication/all/{status?}',[ApisController::class,'all_nota']);
        Route::get('/seen/{id}',[ApisController::class,'seen']);
        Route::get('/order/compleated/{id}',[ApisController::class,'order_compleated']);

        Route::get('/order/installments',[ApisController::class,'orders_installments']);

        Route::get('/order/installments/{id}',[ApisController::class,'order_installments']);
        Route::get('/remove/order/{id}',[ApisController::class,'remove_order']);

    });




});
