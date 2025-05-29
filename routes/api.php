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
Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

Route::middleware('auth:api')->group(function () {
Route::post('logout', 'Api\AuthController@logout');
// Protected routes for projects and tasks
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
});


