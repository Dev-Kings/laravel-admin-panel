<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminIndexController extends Controller
{
    public function index(){
        return view('super-admin.index');
    }
}
