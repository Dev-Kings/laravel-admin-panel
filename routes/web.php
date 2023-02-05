<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserPanelController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\SuperAdminRoleController;
use App\Http\Controllers\SuperAdmin\SuperAdminPermissionController;

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


Route::get('/clear', function(){
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared successfully";
});

Route::get('/', function () {
    return view('welcome');
});

//dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//super-admin 
Route::middleware(['auth', 'role:super-admin'])->name('super-admin.')->prefix('super-admin')->group(function() {
    Route::get('/', [UserPanelController::class, 'index'])->name('index');
    
    //roles
    Route::resource('/roles', SuperAdminRoleController::class);
    Route::post('/roles/{role}/permissions', [SuperAdminRoleController::class, 'givePermission'])->name('roles.permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [SuperAdminRoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    
    //permissions
    Route::resource('/permissions', SuperAdminPermissionController::class);
    Route::post('/permissions/{permission}/roles', [SuperAdminPermissionController::class, 'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}', [SuperAdminPermissionController::class, 'removeRole'])->name('permissions.roles.remove');
    
    //users    
    Route::get('/users/{user}/role', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');

    //users - data
    Route::post('/users/save', [UserController::class, 'store'])->name('users.store');
    Route::get('/user/{id}/edit/', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/users/edit', [UserController::class, 'update'])->name('user.update');
    Route::get('/users-data', [UserController::class, 'usersIndex'])->name('users.data');
    Route::post('users-table', [UserController::class, 'usersTable'])->name('all.users');
    Route::get('/user/destroy/{id}/', [UserController::class, 'destroy']);
    Route::get('/delete-users', [UserController::class, 'deleteSelectedUsers'])->name('delete.users');

    //admins
    Route::get('/admins', [UserController::class, 'adminsIndex'])->name('admins.index');
    Route::get('/assign-admin', [UserController::class, 'assignAdmin'])->name('admins.assign');
    Route::delete('/users/{user}/admin-roles', [UserController::class, 'removeAdminRole'])->name('admins.roles.remove');
    Route::post('/users/{user}/assign-role', [UserController::class, 'assignAdminRole'])->name('users.role');
});

//admin 
Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function() {
    Route::get('/', [UserPanelController::class, 'userPanel'])->name('index');
    
    //users
    Route::post('/users/save', [UserController::class, 'store'])->name('users.store');
    Route::get('/user/{id}/edit/', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/users/edit', [UserController::class, 'update'])->name('user.update');
    Route::get('/users-data', [UserController::class, 'usersIndex'])->name('users.index');
    Route::post('users-table', [UserController::class, 'usersTable'])->name('all.users');
    Route::get('/user/destroy/{id}/', [UserController::class, 'destroy']);
    Route::get('/delete-users', [UserController::class, 'deleteSelectedUsers'])->name('delete.users');
});

require __DIR__.'/auth.php';
