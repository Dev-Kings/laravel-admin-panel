<?php

namespace DevKings\LaravelAdminPanel\Controllers;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function helloWorld()
    {
        return view('admin-panel::hello');
    }
}