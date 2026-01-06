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
// Route::redirect('/', '/api/documentation');
Auth::routes(['verify' => true]);
// Route::get('login', 'Auth\LoginController@login');
Route::get('/subscribe', 'SubscriptionController@showForm')->name('subscribe');
Route::post('/subscribe-submit', 'SubscriptionController@subscribe')->name('subscribe.post');

Route::group(['middleware' => ['auth', 'verified', 'subscribed']], function () {

 
Route::get('/view-profile/{id}','UserController@view');
Route::get('/my-profile','UserController@viewProfile');
Route::get('/', 'HomeController@index')->name('Dashboard');
Route::get('/dashboard', 'HomeController@index')->name('Dashboard');
Route::get('/home', 'HomeController@index')->name('Dashboard');
Route::post('new-project', 'ProjectController@store')->name('New Project');
Route::get('/view-project/{id}','ProjectController@view')->name('View Project');
Route::post('project-member/{id}','ProjectController@teamMember')->name('View Project');
Route::post('project-board/{id}','ProjectController@boardProject')->name('View Project');
Route::post('project/edit-board', 'ProjectController@editBoard')->name('Edit Project Board');

Route::get('/projects','ProjectController@index')->name('Projects');
Route::post('/project/complete/{id}', 'ProjectController@markComplete')->name('Projects');
Route::post('/project/delete/{id}', 'ProjectController@delete')->name('Projects');
Route::post('/project/edit/{id}','ProjectController@updateTitle')->name('projects');


Route::get('/api-keys','ApiKeyController@index')->name('API Keys');
Route::get('/tasks','TaskController@index')->name('Tasks');
Route::post('/tasks/transfer/{id}','TaskController@transfer')->name('Tasks');
Route::post('new-task/{project_id}', 'TaskController@store')->name('New Task');
Route::post('new-task-home', 'TaskController@storeNew')->name('New Task');
Route::get('/view-task/{id}', 'TaskController@view')->name('View Task');
Route::post('task-comment/{id}', 'TaskController@comment')->name('Task Comment');
Route::post('task-attachment/{id}', 'TaskController@attachment')->name('Task Attachment');    
Route::post('task-activity/{id}','TaskController@activity')->name('Activity');
Route::post('activity','TaskController@Newactivity')->name('Activity');
Route::post('task-activity/api/{id}','TaskController@storeActivityApi')->name('Activity');
Route::delete('activity/destroy/{id}', 'TaskController@destroyApi')->name('Delete Activity');
Route::post('task-activity/api/edit/{id}', 'TaskController@updateApi')->name('Edit Activity');
Route::get('/activities/by-date/{date}', 'TaskController@getByDate')->name('activities.byDate');
Route::post('/activities/{id}', 'TaskController@updateActivityAPI')->name('activities.update');
Route::delete('/activities/{id}', 'TaskController@destroyActivityAPI')->name('activities.destroy');

Route::delete('/statuses/{id}', 'ProjectController@destroy')->name('statuses.destroy');

Route::post('update-task-column','TaskController@changeStatus');
Route::post('/update-column-order', 'TaskController@updateOrder')->name('columns.updateOrder');
Route::post('task/complete/{id}','TaskController@complete');
Route::post('tasks/update-board/{id}','TaskController@changeStatusManual');
Route::post('tasks/update-board/api/{id}','TaskController@updateBoard');
Route::post('/update-user-password','UserController@updatePassword');
Route::post('/activity/destroy/{id}','TaskController@destroy')->name('Delete Activity');
Route::post('tasks/{id}/archive','TaskController@archive')->name('Archive Task');
Route::post('/tasks/update-priority/{id}', 'TaskController@updatePriority');
Route::post('task-comment/api/{id}', 'TaskController@commentPost')->name('Task Comment');
Route::post('tasks/{id}/update-title','TaskController@updateTitle')->name('Update Title');
Route::post('tasks/{id}/update-description','TaskController@updateDescription')->name(' Update Description');
Route::post('edit-task-description/{id}','TaskController@updateDescription');


//Task API
Route::post('/tasks/{id}/update-due-date','TaskController@updateDueDate')->name('Update Due Date');

Route::get('my-tasks','TaskController@myTasks');


Route::get('/view-project/view-task/{id}', 'TaskController@view')->name('View Task');   

Route::get('timekeeping','TimekeepingController@index')->name('Timekeeping');
Route::get('my-timekeeping','TimekeepingController@myTimekeeping')->name('My Timekeeping');
Route::get('payslips','PayrollController@index')->name('Payslip');


Route::get('users','UserController@index')->name('users');
Route::post('new-user','UserController@store')->name('users');
Route::post('/edit-user/{id}','UserController@editUser')->name('edit-user');
Route::post('/change-avatar/{id}','UserController@avatar');

Route::post('task-member/{id}','TaskController@changeMember')->name('Change Password');

Route::get('/invoices', 'InvoiceController@index')->name('invoices.index');
Route::get('/invoice/create', 'InvoiceController@createInvoice')->name('invoices.create');
Route::get('/invoice/pay/{id}', 'InvoiceController@pay')->name('invoices.pay');
Route::post('/invoice/pay/{id}', 'InvoiceController@processPayment')->name('invoices.processPayment');

// Route::post('/subscribe', 'BillingController@subscribe')->name('billing.subscribe');

Route::get('/reports','TaskController@TaskReport')->name('task.reports');

Route::get('/users/search', 'UserController@search');

Route::get('/dashboard-admin', 'HomeController@adminDashboard')->name('admin.dashboard');

});

// Route::get('/api/documentation', function () {
//     return view('l5-swagger::index');
// });

Route::get('send-emails','TaskController@sendDailyTaskSummary');

Route::get('auth/google', 'GoogleController@redirectToGoogle');
Route::get('auth/google/callback','GoogleController@handleGoogleCallback');

