<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('loginn', 'Api@authenticate');
Route::get('open', 'Api@open');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'Api@getAuthenticatedUser');
    Route::get('closed', 'Api@closed');
});