<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

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

Route::prefix('v1')->group(function () {


    Route::post('register', [AuthController::class, 'register']);

    Route::post('login', [AuthController::class, 'login']);

    Route::post('emailverify', [AuthController::class, 'emailverify']);

    Route::get('getEntreeCategorie', [UserController::class, 'getEntreeCategorie']);

    Route::get('getSortieCategorie', [UserController::class, 'getSortieCategorie']);

    Route::get('getTypeTransaction', [UserController::class, 'getTypeTransaction']);

    Route::get('getDevis', [UserController::class, 'getDevis']);




    Route::middleware(['auth:sanctum'])->group(function () {

        Route::put('user/update', [UserController::class, 'update']);

        Route::post('solde/put', [UserController::class, 'putSolde']);

        Route::put('logout', [AuthController::class, 'logout']);

        Route::post('transaction/create', [UserController::class, 'createTransaction']);

        Route::get('transaction/getRecent', [UserController::class, 'getRecentTransaction']);

        Route::get('transaction/soldeEntree', [UserController::class, 'getSoldeEntree']);

        Route::get('transaction/soldeSortie', [UserController::class, 'getSoldeSortie']);

        Route::get('transaction/getDetail', [UserController::class, 'getDetail']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });


    });
});
