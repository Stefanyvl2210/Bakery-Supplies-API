<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

/*
 * Public endpoints
 */

//Users
Route::post( '/register', [UserController::class, 'store'] );
Route::post( '/login', [AuthController::class, 'login'] );
//Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
//Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

/*
 * Products
 */
Route::prefix( '/product' )->group( function () {
    Route::get( '/', [ProductController::class, 'show_products'] );
    Route::get( '/{product_id}', [ProductController::class, 'get_product'] );
} );

/*
 * Categories
 */
Route::prefix( '/category' )->group( function () {
    Route::get( '/', [ProductController::class, 'show_categories'] );
    Route::get( '/{category_id}', [ProductController::class, 'get_category'] );
} );

//Protected Routes
Route::group( ['middleware' => ['auth:sanctum']], function () {
    //Users
    Route::get( '/user', [UserController::class, 'index'] );
    Route::put( '/user/{id}', [UserController::class, 'update'] );
    Route::delete( '/user/{id}', [UserController::class, 'destroy'] );
    Route::post( '/logout', [AuthController::class, 'logout'] );
    Route::post( '/verify-email', [AuthController::class, 'verify_email'] );

    /*
     * User endpoints
     */
    Route::group( ['middleware' => ['role:user']], function () {
        /*
         * Address
         */
        Route::prefix( '/address' )->group( function () {
            Route::post( '/', [AddressController::class, 'create'] );
            Route::get( '/', [ProductController::class, 'get_all'] );
            Route::get( '/{address_id}', [ProductController::class, 'get'] );
            Route::patch( '/{address_id}', [AddressController::class, 'update'] );
            Route::delete( '/{address_id}', [AddressController::class, 'delete'] );
        } );

        /*
         * Orders
         */
        Route::prefix( '/orders' )->group( function () {
            Route::post( '/', [OrderController::class, 'create'] );
            Route::get( '/users/{user_id}', [OrderController::class, 'get_all_by_user'] );
        } );
    } );

    /*
     * @ Admin Endpoints
     */
    Route::group( ['middleware' => ['role:admin']], function () {

        /*
         * Products
         */
        Route::prefix( '/product' )->group( function () {
            Route::post( '/', [ProductController::class, 'store'] );
            Route::patch( '/{product_id}', [ProductController::class, 'update_product'] );
            Route::delete( '/{product_id}', [ProductController::class, 'delete_product'] );
        } );

        /*
         * Categories
         */
        Route::prefix( '/category' )->group( function () {
            Route::post( '/', [ProductController::class, 'create_category'] );
            Route::patch( '/{category_id}', [ProductController::class, 'update_category'] );
            Route::delete( '/{category_id}', [ProductController::class, 'delete_category'] );
        } );

        /*
         * Orders
         */
        Route::prefix( '/orders' )->group( function () {
            Route::update( '/{order_id}', [OrderController::class, 'update'] );
            Route::get( '/', [OrderController::class, 'get_all'] );
            Route::patch( '/{order_id}', [OrderController::class, 'update'] );
            Route::delete( '/{order_id}', [OrderController::class, 'delete'] );
        } );
    } );

} );
