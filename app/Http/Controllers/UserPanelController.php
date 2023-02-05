<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserPanelController extends Controller
{
    public function index(){
        return view('super-admin.index');
    }
    
    public function userPanel()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('admin')) {
            return view('admin.index');
        }
    }
}
