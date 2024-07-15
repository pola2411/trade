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
    Route::get('countries',[AuthController::class,'countries']);
    Route::get('banks',[AuthController::class,'banks']);
    Route::post('/upload/file',[ApisController::class,'upload_file']);

   ///this routes must be login
    Route::middleware('jwt.verify')->group(function(){
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'sendOtp']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [ApisController::class, 'getUser']);
        Route::post('/user/update', [AuthController::class, 'updateUser']);
        Route::post('/create/account',[ApisController::class,'create_account']);
        Route::middleware('check_type_account')->group(function(){
            Route::post('/save/payment/request',[ApisController::class,'save_payment_request']);
            Route::post('/withdrawn',[ApisController::class,'withdrawn']);
            Route::get('/trasactions',[ApisController::class,'trasactions']);
        });
        Route::get('/currancies', [ApisController::class, 'currancies']);

        Route::post('/bank/accounts/store',[ApisController::class,'bank_accounts_store']);

        Route::get('/get/bank/accounts',[ApisController::class,'bank_accounts_get']);

        Route::get('/get/verifications/country',[ApisController::class,'get_verifications_country']);
        Route::post('/store/verifications/customer',[ApisController::class,'store_verifications_customer']);

        Route::get('/get/all/accounts',[ApisController::class,'get_all_accounts']);
    });




});
