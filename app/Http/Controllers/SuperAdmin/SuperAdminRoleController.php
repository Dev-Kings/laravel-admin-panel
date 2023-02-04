<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class SuperAdminRoleController extends Controller
{
    //roles view
    public function index(){
        $roles = Role::all();
        
        return view('super-admin.roles.index', compact('roles'));
    }

    //create page
    public function create(){
        return view('super-admin.roles.create');
    }

    //store role in db
    public function store(Request $request){
        $validated = $request->validate(['name' => ['required', 'min:3']]);
        Role::create($validated);

        return to_route('super-admin.roles.index')->with('success', 'Role created successfully');
    }

    public function edit(Role $role){
        $permissions = Permission::all();
        return view('super-admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role){
        $validated = $request->validate(['name' => 'required', 'min:3']);
        $role->update($validated);

        return to_route('super-admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role){
        $role->delete();

        return back()->with('success', 'Role deleted successfully');
    }
    
    public function givePermission(Request $request, Role $role){
        if($role->hasPermissionTo($request->permission)){
            return back()->with('error-message', 'Permission exists');
        }
        $role->givePermissionTo($request->permission);

        return back()->with('success', 'Permission granted');
    }

    public function revokePermission(Role $role, Permission $permission){
        if($role->hasPermissionTo($permission)){
            $role->revokePermissionTo($permission);
            return back()->with('success', 'Permission revoked');
        }
        return back()->with('error-message', 'Permission doesn\'t exist');
    }
}
