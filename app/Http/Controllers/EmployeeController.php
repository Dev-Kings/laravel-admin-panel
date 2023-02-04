<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;

class EmployeeController extends Controller
{
    public function employeeIndex()
    {
        $user = User::find(Auth::user()->id);
        $companies = DB::table('companies')->orderBy('name', 'asc')->get();
        $departments = DB::table('departments')->orderBy('name', 'asc')->get();
        $rates = DB::table('rates')->orderBy('rate', 'asc')->get();
        if ($user->hasRole('super-admin')) {
            return view('super-admin.employees.datatable', compact('companies', 'departments', 'rates'));
        }
        if ($user->hasRole('admin')) {
            return view('admin.employees.datatable');
        }
    }

    public function employeeTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $employees = DB::table('employees')->whereNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where("staff_no", "LIKE", "%" . $search . "%")
                        ->orWhere("employee_name", "LIKE", "%" . $search . "%");
                })
                ->join('companies','companies.id', '=', 'employees.company_id')
                ->join('departments','departments.id', '=', 'employees.department_id')
                ->join('rates','rates.id', '=', 'employees.rate_id')
                ->select(
                    'employees.*',
                    'companies.name as company_name',
                    'departments.name as department_name',
                    'rates.rate as rate',
                )->orderBy('employee_name', 'asc')->get();

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $button = '<button type="button" name="edit" id="' . $row->id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '  <button type="button" name="delete" id="' . $row->id . '" class="delete btn btn-outline-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->addColumn('checkbox', '<input type="checkbox" name="employees_checkbox[]" class="employees_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }
    }

    public function storeOne(Request $request)
    {
        $rules = array(
            'company_id' => 'required',
            'department_id' => 'required',
            'rate_id' => 'required',
            'staff_no' => "required|max:255|unique:employees",
            'employee_name' => ['required', 'string', 'max:255'],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
            'rate_id' => $request->rate_id,
            'staff_no' => strtoupper($request->staff_no),
            'employee_name' => strtoupper($request->employee_name),
        );

        Employee::create($form_data);

        return response()->json(['success' => 'Employee data added successfully.']);
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $data = Employee::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        $id = $request->hidden_id;
        $rules = array(
            'company_id' => 'required',
            'department_id' => 'required',
            'rate_id' => 'required',
            'staff_no' => [
                'required',
                'max:255',
                Rule::unique('employees')->ignore($id),
            ],
            'employee_name' => ['required', 'string', 'max:255'],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
            'rate_id' => $request->rate_id,
            'staff_no' => $request->staff_no,
            'employee_name' => strtoupper($request->employee_name),
        );

        Employee::whereId($request->hidden_id)->update($form_data);

        return response()->json(['update_success' => 'Employee data updated successfully.']);
    }

    public function destroy($id)
    {
        $data = Employee::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Employee data deleted.']);
    }

    public function deleteSelected(Request $request)
    {
        $employees_id_array = $request->input('id');
        $employee = Employee::whereIn('id', $employees_id_array);
        if ($employee->delete()) {
            return response()->json(['success' => 'Employee(s) data deleted.']);
        }
    }

    public function softDeleted()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.employees.frozen_employees');
        }
        if ($user->hasRole('admin')) {
            return view('admin.employees.frozen_employees');
        }
    }

    public function frozenEmployeesTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $frozen = DB::table('employees')->whereNotNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where("staff_no", "LIKE", "%" . $search . "%")
                        ->orWhere("employee_name", "LIKE", "%" . $search . "%");
                })
                ->select(
                    'employees.*',
                )->orderBy('employee_name', 'asc')->get();

            return DataTables::of($frozen)
                ->addIndexColumn()
                ->addColumn('action', function ($employee) {
                    $button = '<button type="button" name="restore" id="' . $employee->id . '" class="restore btn btn-outline-primary btn-sm">Restore</button>';
                    $button .= '  <button type="button" name="delete" id="' . $employee->id . '" class="delete btn btn-outline-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->addColumn('checkbox', '<input type="checkbox" name="employees_checkbox[]" class="employees_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }
    }

    public function restore($id)
    {
        if (request()->ajax()) {

            $frozen_employee = Employee::onlyTrashed()
                ->where('id', $id)
                ->first();

            if ($frozen_employee->restore()) {
                return response()->json(['success' => 'Employee\'s data restored successfully.']);
            }
        }
    }

    public function unFreezeEmployees(Request $request)
    {
        $employees_id_array = $request->input('id');
        $employee = Employee::whereIn('id', $employees_id_array);

        if ($employee->restore()) {
            return response()->json(['success' => 'Employee(s) restored successfully.']);
        }
    }

    public function delete($id)
    {
        $data = Employee::onlyTrashed()
            ->where('id', $id)
            ->first();

        $data->presents()->delete();

        $data->forceDelete();

        return response()->json(['success' => 'Employee data permanently deleted.']);
    }

    public function deleteForever(Request $request)
    {
        $employees_id_array = $request->input('id');
        
        Employee::onlyTrashed()->whereIn('id', $employees_id_array)->forceDelete();

        return response()->json(['success' => 'Employee data deleted permanently.']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'employee_import_file' => 'required|mimes:csv,txt',
            ]
        );

        if($validator->fails()){
            return back()->with('error-message', 'Kindly upload a .csv file only.');
        }
        try {
            Excel::import(new EmployeeImport(), $request->file('employee_import_file'));

            return back()->with('sql-success', 'Successfully added employees');
        } catch (NoTypeDetectedException) {
            return back()->with('general-error-message', 'Error importing data');
        } catch (QueryException $e) {
            $error = $e->getBindings();
            return back()->with('sql-message', json_encode($error));
        }
    }
}
