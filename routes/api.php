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

Route::apiResource('projects', 'Api\ProjectController');
Route::patch('projects/{id}/complete', 'Api\ProjectController@complete');
Route::post('projects/{id}/assign-users', 'Api\ProjectController@assignUsers');
Route::post('projects/{id}/assign-team', 'Api\ProjectController@assignTeam');

Route::apiResource('tasks', 'Api\TaskController');
Route::patch('tasks/{id}/complete', 'Api\TaskController@complete');
Route::patch('tasks/{id}/deadline', 'Api\TaskController@setDeadline');
Route::post('tasks/{id}/assign-users', 'Api\TaskController@assignUsers');
Route::post('tasks/{id}/comments', 'Api\TaskController@addComment');
Route::post('tasks/{id}/attachments', 'Api\TaskController@uploadAttachment');
Route::post('tasks/{id}/activity', 'Api\TaskController@logActivity');

