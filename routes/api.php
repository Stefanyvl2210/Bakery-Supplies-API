<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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

//Public Routes
//Users
Route::post( '/register', [UserController::class, 'store'] );
Route::post( '/login', [AuthController::class, 'login'] );
//Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
//Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

//Protected Routes
Route::group( ['middleware' => ['auth:sanctum']], function () {
    //Users
    Route::get( '/user', [UserController::class, 'index'] );
    Route::put( '/user/{id}', [UserController::class, 'update'] );
    Route::delete( '/user/{id}', [UserController::class, 'destroy'] );
    Route::post( '/logout', [AuthController::class, 'logout'] );
    Route::post( '/verify-email', [AuthController::class, 'verify_email'] );

    /*
     * @ Admin Endpoints
     */
    Route::group( ['middleware' => ['role:admin']], function () {

        /*
         * Products
         */
        Route::prefix( '/product' )->group( function () {
            Route::get( '/', [ProductController::class, 'show_products'] );
            Route::get('/{product_id}', [ProductController::class, 'get_product']);
            Route::post('/', [ProductController::class, 'store']);
            Route::patch('/{product_id}', [ProductController::class, 'update_product']);
            Route::delete('/{product_id}', [ProductController::class, 'delete_product']);
        } );

        /*
        * Categories
        */
        Route::prefix( '/category' )->group( function () {
            Route::get( '/', [ProductController::class, 'show_categories'] );
            Route::get('/{category_id}', [ProductController::class, 'get_category']);
            Route::post('/', [ProductController::class, 'create_category']);
            Route::patch('/{category_id}', [ProductController::class, 'update_category']);
            Route::delete('/{category_id}', [ProductController::class, 'delete_category']);
        } );
    } );

} );
