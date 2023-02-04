<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class SuperAdminPermissionController extends Controller
{
     //permissions view
    public function index(){
        $permissions = Permission::all();

        return view('super-admin.permissions.index', compact('permissions'));
    }

    //create view
    public function create(){
        return view('super-admin.permissions.create');
    }

    //store permissions in db
    public function store(Request $request){
        $validated = $request->validate(['name' => ['required', 'min:3']]);
        Permission::create($validated);

        return to_route('super-admin.permissions.index')->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission){
        $roles = Role::all();
        return view('super-admin.permissions.edit', compact('permission', 'roles'));
    }

    public function update(Request $request, Permission $permission){
        $validated = $request->validate(['name' => 'required']);
        $permission->update($validated);

        return to_route('super-admin.permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission){
        $permission->delete();

        return back()->with('success', 'Permission deleted');
    }
    
    public function assignRole(Request $request, Permission $permission){
        if($permission->hasRole($request->role)){
            return back()->with('error-message', 'Role exists');
        }

        $permission->assignRole($request->role);
        return back()->with('success', 'Role assigned permission');
    }

    public function removeRole(Permission $permission, Role $role){
        if($permission->hasRole($role)){
            $permission->removeRole($role);
            
            return back()->with('success', 'Role denied permission');
        }
        return back()->with('error-message', 'Role doesn\'t exist');
    }
}
