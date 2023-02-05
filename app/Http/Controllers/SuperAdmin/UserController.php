<?php

namespace App\Http\Controllers\SuperAdmin;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function usersIndex()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.users.datatable');
        }
        if ($user->hasRole('admin')) {
            return view('admin.employees.datatable');
        }
    }

    public function usersTable(Request $request){
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $users = DB::table('users')
                    ->where(function ($query) use ($search) {
                        $query->where("email", "LIKE", "%" . $search . "%")
                            ->orWhere("firstname", "LIKE", "%" . $search . "%")
                            ->orWhere("lastname", "LIKE", "%" . $search . "%");
                    })
                    ->select(
                        'users.*',
                        DB::raw("CONCAT(users.firstname, ' ', users.lastname) as username"),
                    )->orderBy('firstname', 'asc')->get();


            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $button = '<a href="/super-admin/users/' . $row->id . '/role" class="btn btn-outline-primary btn-sm">Role</a>';
                    $button .= ' <button type="button" name="edit" id="' . $row->id . '" class="edit btn btn-outline-info btn-sm">Edit</button>';
                    $button .= '  <button type="button" name="delete" id="' . $row->id . '" class="delete btn btn-outline-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->addColumn('checkbox', '<input type="checkbox" name="users_checkbox[]" class="users_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if ($user->hasRole('super-admin')) {

            $rules = array(
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            );

            $valid = Validator::make($request->all(), $rules);

            if($valid->fails()){
                return response()->json(['errors' => $valid->errors()->all()]);
            }

            User::create([
                'firstname' => Str::title($request->firstname),
                'lastname' => Str::title($request->lastname),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['success' => 'User data added successfully.']);
        }
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $data = User::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        $id = $request->hidden_id;
            $rules = array(
                'firstname' => ['sometimes', 'required', 'string', 'max:255'],
                'lastname' => ['sometimes', 'required', 'string', 'max:255'],
                'email' => [
                    'sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($id),
                ],
            );

            $error = Validator::make($request->all(), $rules);

            if ($error->fails()) {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $form_data = array(
                'firstname' => Str::title($request->firstname),
                'lastname' => Str::title($request->lastname),
                'email' => $request->email,
            );

            User::whereId($request->hidden_id)->update($form_data);

            return response()->json(['update_success' => 'User details updated successfully.']);        
    }

    public function genericIndex()
    {
        $users = User::latest()->filter(request([
            'search'
        ]))->paginate(50);

        return view('users.index', compact('users'));
    }

    public function rolePlayers()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.employees.datatable');
        }
        if ($user->hasRole('admin')) {
            return view('admin.employees.datatable');
        }
    }

    public function deleteSelectedUsers(Request $request)
    {
        $users_id_array = $request->input('id');
        foreach ($users_id_array as $user) {
            try {
                $user = User::findOrFail($user);
                $user->delete();                
            } catch (Exception $e) {
                return response()->json(['deletion_error' => 'User(s) data not deleted. Kindly contact IT dept. for help.']);
            }            
        }
        return response()->json(['success' => 'User(s) data deleted.']);                             
    }

    public function adminsIndex()
    {
        $roles = Role::all();
        $admins = Role::where('name', 'admin')
            ->first()->users;

        return view('super-admin.admins.index', compact('admins', 'roles'));
    }

    public function assignRoles()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.employees.datatable');
        }
        if ($user->hasRole('admin')) {
            return view('admin.employees.datatable');
        }
    }

    public function assignAdmin()
    {
        $users = User::whereDoesntHave('roles')
            ->latest()->filter(request([
                'search'
            ]))->paginate(10);

        return view('super-admin.admins.assign', compact('users'));
    }

    public function show(User $user)
    {
        $role_check = $user->hasAnyRole('super-admin', 'admin');
        if($role_check){
            $role_array = ['super-admin', 'admin'];
            $roles = DB::table('roles')->whereIn('name', $role_array)->get();
    
            return view('super-admin.users.assign', compact('user', 'roles'));
        }
        if(!$role_check){
            return view('super-admin.users.show', compact('user'));
        }        
    }

    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with('error-message', 'User has the role');
        }
        $user->assignRole($request->role);
        return back()->with('success', 'Role ' . $request->role . ' assigned to ' . $user->firstname);
    }

    public function assignAdminRole(User $user)
    {

        if ($user->hasRole('admin')) {
            return back()->with('error-message', 'User has the role');
        }
        $user->assignRole('admin');
        return back()->with('success', 'Role admin assigned to ' . $user->firstname .' '. $user->lastname);
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return back()->with('success', 'Role ' . $role->name . ' denied/removed from ' . $user->firstname);
        }

        return back()->with('error-message', 'Role doesn\'t exist');
    }

    public function removeAdminRole(User $user)
    {
        if ($user->hasRole('admin')) {
            $user->removeRole('admin');
            return back()->with('success', 'Role admin denied/removed from ' . $user->firstname);
        }
    }

    public function givePermission(Request $request, User $user)
    {
        if ($user->hasPermissionTo($request->permission)) {
            return back()->with('error-message', 'Permission exists');
        }
        $user->givePermissionTo($request->permission);

        return back()->with('success', 'Permission granted');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('success', 'Permission revoked');
        }
        return back()->with('error-message', 'Permission doesn\'t exist');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['success' => 'User data deleted.']);
        } catch (Exception $e) {
            return response()->json(['deletion_error' => $e.' User data not deleted. Contact developer for support.']);
        }
    }
}
