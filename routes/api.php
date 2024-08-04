<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserSignInController;
use App\Http\Controllers\Api\ComposerController;
use App\Http\Controllers\Api\UserCustomerController;
use App\Http\Controllers\Api\UserPosController;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\SalesPersonController;
use App\Http\Controllers\Api\ExtraDataController;
// Composer route
Route::get('/clear-all-cache', [ComposerController::class, 'clearAllCache']);

Route::post('/send-email-otp', [UserSignInController::class, 'sendEmailOtp']);
Route::post('/verify-Otp', [UserSignInController::class, 'verifyOtp']);
Route::post('/user-register', [UserSignInController::class, 'register']);
Route::post('/user-name-search', [UserSignInController::class, 'userNameSearch']);
Route::post('/password-add', [UserSignInController::class, 'passwordAdd']);
Route::post('/user-login', [UserSignInController::class, 'login']);


Route::get('/generate-barcode-api', [BarcodeController::class, 'generateApi']);


Route::middleware(['auth:api'])->group(function () {
    // Common routes accessible to all authenticated users
    Route::get('/user', [UserSignInController::class, 'getUser']);
    Route::get('/logout', [UserSignInController::class, 'logout']);
    Route::put('/update-profile', [UserSignInController::class, 'updateProfile']);
    Route::post('/user-profile-image-update', [UserSignInController::class, 'userProfileImageUpdate']);

    Route::post('/user-customers-add', [UserCustomerController::class, 'store']);
    Route::put('/user-customers-update/{id}', [UserCustomerController::class, 'update']);
    Route::delete('/user-customers/{id}', [UserCustomerController::class, 'destroy']);
    Route::get('/get-supplier-data', [UserCustomerController::class, 'getSupplierData']);
    Route::get('/get-customer-data', [UserCustomerController::class, 'getCustomerData']);

    Route::get('/get-all-sipplier', [ItemController::class, 'getAllSipplier']);
    Route::get('/get-sipplier-by-id/{supplierId}', [ItemController::class, 'getSipplierById']);
    Route::post('/store-item', [ItemController::class, 'storeItems']);
    Route::get('/search-supplier-customer', [ItemController::class, 'SearchSupplierCustomer']);
    Route::get('/get-all-items', [ItemController::class, 'getAllItems']);


    // storeGroup
    Route::post('/store-group', [GroupController::class, 'storeGroup']);
    Route::get('/get-group-data/{groupName?}', [GroupController::class, 'getGroup']);
    Route::put('/update-group/{groupId}', [GroupController::class, 'updateGroup']);
    Route::delete('/delete-group/{groupId}', [GroupController::class, 'deleteGroup']);

    //discount
    Route::get('/get-discount-value', [GroupController::class, 'getDiscount']);
    Route::post('/store-discount', [GroupController::class, 'storeDiscount']);
    Route::delete('/delete-discount/{id}', [GroupController::class, 'destroyDiscount']);
    Route::put('/update-discount/{id}', [GroupController::class, 'updateDiscount']);

    // storeGroupBrand
    Route::get('/group-brands/{groupId}/{brandName?}', [GroupController::class, 'getAllGroupBrands']);
    Route::post('/group-brand', [GroupController::class, 'createGroupBrand']);
    Route::put('/group-brand/{id}', [GroupController::class, 'updateGroupBrand']);
    Route::delete('/delete-group-brand/{id}', [GroupController::class, 'deleteGroupBrand']);

    // selce presion
    Route::get('/salespersons', [SalesPersonController::class, 'index']);
    Route::post('/store-salespersons', [SalesPersonController::class, 'store']);
    Route::post('/update-salespersons/{id}', [SalesPersonController::class, 'update']);
    Route::delete('/delete-salespersons/{id}', [SalesPersonController::class, 'destroy']);

    //expance
    Route::get('/expance-get', [ExtraDataController::class, 'getExpensean']);
    Route::post('/expance-store', [ExtraDataController::class, 'expancestore']);
    Route::delete('/expance-delete/{id}', [ExtraDataController::class, 'deleteExpensean']);



    //barckde
    Route::middleware(CheckUserRole::class . ':admin')->group(function () {
        Route::post('/user-update-email', [UserSignInController::class, 'updateEmail']);
        Route::post('/user-update-email-verify', [UserSignInController::class, 'verifyUpdateEmailOTP']);
        Route::post('/user-addbyadmin', [UserSignInController::class, 'useraddbyAdmin']);
        Route::put('/user/{userId}', [UserSignInController::class, 'updateUser']);
        Route::delete('/user-delete/{userId}', [UserSignInController::class, 'deleteUser']);
        Route::get('/get-admin-user', [UserSignInController::class, 'getAdminUser']);
    });



    // Routes accessible only to users with 'pos' role
    Route::middleware(CheckUserRole::class . ':pos')->group(function () {
        // Add 'pos' specific routes here
    });

    // Routes accessible only to users with 'userAccess' role
    Route::middleware(CheckUserRole::class . ':userAccess')->group(function () {
        // Add 'userAccess' specific routes here

    });
});


Route::fallback(function () {
    return response()->json(['status' => 0, 'message' => 'Route not found'], 404);
});
