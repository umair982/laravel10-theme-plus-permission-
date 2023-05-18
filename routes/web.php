<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCrudController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/admin',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.login');
Route::get('/admin/register',[RegisterController::class,'showAdminRegisterForm'])->name('admin.register-view');
Route::post('/admin/register',[RegisterController::class,'createAdmin'])->name('admin.register');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group([
    'middleware'=>'auth:admin',
    'prefix'        => 'admin',
],function(){
    Route::get('dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
    Route::get('profile', [AdminController::class,'edit'])->name('admin.profile');
    Route::post('update', [AdminController::class,'update'])->name('admin-update');
    Route::resource('setting',SettingController::class);
    Route::resource('users',UserController::class);
    Route::post('delete-selected-users', [UserController::class,'deleteSelectedUsers'])->name('delete-selected-users');
    Route::post('get-users', [UserController::class,'getUsers'])->name('admin.getUsers');
    Route::post('get-user', [UserController::class,'userDetail'])->name('admin.getUser');
    Route::get('user/delete/{id}', [UserController::class,'destroy'])->name('user-delete');
    Route::resource('roles', RoleController::class);
    Route::post('delete-selected-roles', [RoleController::class,'deleteSelectedUsers'])->name('delete-selected-roles');
    Route::post('get-roles', [RoleController::class,'getRoles'])->name('admin.getRoles');
    Route::post('get-role', [RoleController::class,'roleDetail'])->name('admin.getRole');
    Route::get('role/delete/{id}', [RoleController::class,'destroy'])->name('role-delete');
    Route::resource('admins',AdminCrudController::class);
    Route::post('delete-selected-admins', [AdminCrudController::class,'deleteSelectedAdmins'])->name('delete-selected-admins');
    Route::post('get-admins', [AdminCrudController::class,'getAdmins'])->name('admin.getAdmins');
    Route::post('get-admin', [AdminCrudController::class,'adminDetail'])->name('admin.getAdmin');
    Route::get('admin/delete/{id}', [AdminCrudController::class,'destroy'])->name('admin-delete');
});

