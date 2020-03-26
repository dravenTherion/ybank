<?php

use Illuminate\Http\Request;
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

/**
 * Account Routes
 */
Route::get('accounts/{id}', 'Accounts@get');

/**
 * Transaction Routes
 */
Route::get('accounts/{id}/transactions', 'Transactions@list');
Route::post('accounts/{id}/transactions', 'Transactions@send');

/**
 * Miscellaneous Routes
 */
Route::get('currencies', function () {
    $account = DB::table('currencies')
              ->get();

    return $account;
});
