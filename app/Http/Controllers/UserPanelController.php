<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserPanelController extends Controller
{
    public function userPanel()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('admin')) {
            return view('admin.index');
        }
    }
}
