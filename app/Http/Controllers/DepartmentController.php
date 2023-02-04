<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function departmentIndex(){
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.departments.datatable');
        }
    }

    public function departmentTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $departments = DB::table('departments')
                ->where(function ($query) use ($search) {
                    $query->where("name", "LIKE", "%" . $search . "%");
                })
                ->select(
                    'departments.*',
                )->orderBy('name', 'asc')->get();

            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $button = '<button type="button" name="edit" id="' . $row->id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '  <button type="button" name="delete" id="' . $row->id . '" class="delete btn btn-outline-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function storeOne(Request $request)
    {
        $rules = array(
            'name' => ['required','unique:departments', 'min:3'],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => Str::title($request->name),
        );

        Department::create($form_data);

        return response()->json(['success' => 'Department data added successfully.']);
    }

    public function editDepartment($id)
    {
        if (request()->ajax()) {
            $data = Department::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function updateDepartment(Request $request)
    {
        $id = $request->hidden_id;
        $rules = array(
            'name' => [
                'required', 
                'min:3',
                Rule::unique('departments')->ignore($id),
            ],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => Str::title($request->name)
        );

        Department::whereId($request->hidden_id)->update($form_data);

        return response()->json(['update_success' => 'Department data updated successfully.']);
    }

    public function destroyDepartment($id)
    {
        $data = Department::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Department data deleted.']);
    }
}
