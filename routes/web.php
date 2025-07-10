<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::redirect('/', '/api/documentation');
Auth::routes();
// Route::get('login', 'Auth\LoginController@login');
Route::group(['middleware' => 'auth'], function () {
    
Route::get('/', 'HomeController@index')->name('Dashboard');
Route::get('/home', 'HomeController@index')->name('Dashboard');
Route::post('new-project', 'ProjectController@store')->name('New Project');
Route::get('/view-project/{id}','ProjectController@view')->name('View Project');
Route::post('project-member/{id}','ProjectController@teamMember')->name('View Project');
Route::post('project-board/{id}','ProjectController@boardProject')->name('View Project');
Route::post('project/edit-board', 'ProjectController@editBoard')->name('Edit Project Board');

Route::get('/projects','ProjectController@index')->name('Projects');


Route::get('/api-keys','ApiKeyController@index')->name('API Keys');
Route::get('/tasks','TaskController@index')->name('Tasks');
Route::post('new-task/{project_id}', 'TaskController@store')->name('New Task');
Route::get('/view-task/{id}', 'TaskController@view')->name('View Task');
Route::post('task-comment/{id}', 'TaskController@comment')->name('Task Comment');
Route::post('task-attachment/{id}', 'TaskController@attachment')->name('Task Attachment');    
Route::post('task-activity/{id}','TaskController@activity')->name('Activity');

Route::get('/view-project/view-task/{id}', 'TaskController@view')->name('View Task');   

Route::get('timekeeping','TimekeepingController@index')->name('Timekeeping');
Route::get('my-timekeeping','TimekeepingController@myTimekeeping')->name('My Timekeeping');
Route::get('payslips','PayrollController@index')->name('Payslip');


Route::get('users','UserController@index')->name('users');
Route::post('new-user','UserController@store')->name('users');
Route::post('/edit-user/{id}','UserController@editUser')->name('edit-user');
Route::post('/change-avatar/{id}','UserController@avatar');

});

Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});

