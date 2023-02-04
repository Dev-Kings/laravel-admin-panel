<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\RateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\UserPanelController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeDataController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\SuperAdminRoleController;
use App\Http\Controllers\SuperAdmin\SuperAdminIndexController;
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
    Route::get('/', [SuperAdminIndexController::class, 'index'])->name('index');
    
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

    //employees
    Route::post('/employees/create', [EmployeeController::class, 'store'])->name('employees.store');

    //employees - alpha
    Route::post('/employee-create', [EmployeeController::class, 'storeOne'])->name('employee.store');
    Route::get('/employee/{id}/edit/', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::post('/employees/edit', [EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/employees-data', [EmployeeController::class, 'employeeIndex'])->name('employees.alpha');
    Route::post('employees-table', [EmployeeController::class, 'employeeTable'])->name('employee.list');
    Route::get('/employee/destroy/{id}/', [EmployeeController::class, 'destroy']);
    Route::get('/delete-employees', [EmployeeController::class, 'deleteSelected'])->name('delete.employees');

    //employees - deleted
    Route::get('/frozen-employees', [EmployeeController::class, 'softDeleted'])->name('employees.deleted');
    Route::post('frozen-table', [EmployeeController::class, 'frozenEmployeesTable'])->name('employee.frozen');
    Route::get('/employee/{id}/restore/', [EmployeeController::class, 'restore'])->name('employee.restore');
    Route::get('/employee/delete/{id}/', [EmployeeController::class, 'delete']);
    Route::get('unfreeze-employees', [EmployeeController::class, 'unFreezeEmployees'])->name('unfreeze.employees');
    Route::get('delete-employees-forever', [EmployeeController::class, 'deleteForever'])->name('delete.employees.forever');

    //data
    Route::get('/company-data', [EmployeeDataController::class, 'dataIndex'])->name('data.index');
    Route::post('data-records', [EmployeeDataController::class, 'dataTable'])->name('company.data.list');
    Route::get('/mark-employees-present', [EmployeeDataController::class, 'markPresent'])->name('mark.employees.present');
    Route::get('/mark-employees-absent', [EmployeeDataController::class, 'markAbsent'])->name('mark.employees.absent');

    //payments
    Route::get('/payments-data', [EmployeeDataController::class, 'paymentsIndex'])->name('payments.index');
    Route::post('monthly-data', [EmployeeDataController::class, 'monthlyPayments'])->name('monthly.data');
    Route::get('/daily-payments', [EmployeeDataController::class, 'dailyPaymentsView'])->name('daily.payments.data');
    Route::post('daily-data', [EmployeeDataController::class, 'dailyPayments'])->name('daily.data');

    //companies
    Route::post('/create-company', [CompanyController::class, 'storeOne'])->name('company.store');
    Route::get('/company/{id}/edit/', [CompanyController::class, 'editCompany'])->name('company.edit');
    Route::post('/company/edit', [CompanyController::class, 'updateCompany'])->name('company.update');
    Route::get('/companies-data', [CompanyController::class, 'companyIndex'])->name('companies.index');
    Route::post('companies-table', [CompanyController::class, 'companyTable'])->name('company.list');
    Route::get('/company/destroy/{id}/', [CompanyController::class, 'destroyCompany']);

    //departments
    Route::post('/create-department', [DepartmentController::class, 'storeOne'])->name('department.store');
    Route::get('/department/{id}/edit/', [DepartmentController::class, 'editDepartment'])->name('department.edit');
    Route::post('/department/edit', [DepartmentController::class, 'updateDepartment'])->name('department.update');
    Route::get('/departments-data', [DepartmentController::class, 'departmentIndex'])->name('departments.index');
    Route::post('departments-table', [DepartmentController::class, 'departmentTable'])->name('department.list');
    Route::get('/department/destroy/{id}/', [DepartmentController::class, 'destroyDepartment']);

    //variables
    Route::post('/create-variable', [VariableController::class, 'storeOne'])->name('variable.store');
    Route::get('/variable/{id}/edit/', [VariableController::class, 'editVariable'])->name('variable.edit');
    Route::post('/variable/edit', [VariableController::class, 'updateVariable'])->name('variable.update');
    Route::get('/variables-data', [VariableController::class, 'variableIndex'])->name('variables.index');
    Route::post('variables-table', [VariableController::class, 'variablesTable'])->name('variables.list');
    Route::get('/variable/destroy/{id}/', [VariableController::class, 'destroyVariable']);

    Route::post('/rates/create', [RateController::class, 'store'])->name('rates.store');

    //rates
    Route::post('/create-rate', [RateController::class, 'storeOne'])->name('rate.store');
    Route::get('/rate/{id}/edit/', [RateController::class, 'editRate'])->name('rate.edit');
    Route::post('/rate/edit', [RateController::class, 'updateRate'])->name('rate.update');
    Route::get('/rates-data', [RateController::class, 'ratesIndex'])->name('rates.data');
    Route::post('rates-table', [RateController::class, 'ratesTable'])->name('rates.list');
    Route::get('/rate/destroy/{id}/', [RateController::class, 'destroyRate']);
    Route::get('delete-rates', [RateController::class, 'deleteRates'])->name('delete.rates');

    //payments chart
    Route::get('/monthly-payments-chart', [EmployeeDataController::class, 'monthlyChart'])->name('monthly.payments.chart');
    Route::get('/yearly-payments-chart',[EmployeeDataController::class, 'yearlyChart'])->name('yearly.payments.chart');
});

//admin 
Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function() {
    Route::get('/', [UserPanelController::class, 'userPanel'])->name('index');
    
    //users
    Route::get('/users', [UserController::class, 'genericIndex'])->name('users.index');

    //employees - alpha
    Route::post('/employees/create', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employee-create', [EmployeeController::class, 'storeOne'])->name('employee.store');
    Route::get('/employee/{id}/edit/', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::post('/employees/edit', [EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/employees-data', [EmployeeController::class, 'employeeIndex'])->name('employees.alpha');
    Route::post('employees-table', [EmployeeController::class, 'employeeTable'])->name('employee.list');
    Route::get('/employee/destroy/{id}/', [EmployeeController::class, 'destroy']);
    Route::get('delete-employees', [EmployeeController::class, 'deleteSelected'])->name('delete.employees');

    //employees - deleted
    Route::get('/frozen-employees', [EmployeeController::class, 'softDeleted'])->name('employees.deleted');
    Route::post('frozen-table', [EmployeeController::class, 'frozenEmployeesTable'])->name('employee.frozen');
    Route::get('/employee/{id}/restore/', [EmployeeController::class, 'restore'])->name('employee.restore');
    Route::get('/employee/delete/{id}/', [EmployeeController::class, 'delete']);
    Route::get('unfreeze-employees', [EmployeeController::class, 'unFreezeEmployees'])->name('unfreeze.employees');
    Route::get('delete-employees-forever', [EmployeeController::class, 'deleteForever'])->name('delete.employees.forever');
});

require __DIR__.'/auth.php';
