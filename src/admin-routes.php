<?php

use Illuminate\Support\Facades\Route;
use DevKings\LaravelAdminPanel\Controllers\UserController;

Route::group([
    'namespace' => 'DevKings\LaravelAdminPanel\Controllers',
    'prefix' => 'admin-panel',
], function () {
    Route::get('/', [UserController::class, 'helloWorld']);
});