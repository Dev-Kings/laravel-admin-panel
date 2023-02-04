<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Present;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeDataController extends Controller
{
    public function dataIndex()
    {
        $user = User::find(Auth::user()->id);
        $companies = DB::table('companies')->orderBy('name', 'asc')->get();
        $departments = DB::table('departments')->orderBy('name', 'asc')->get();
        $rates = DB::table('rates')->orderBy('rate', 'asc')->get();
        if ($user->hasRole('super-admin')) {
            return view('super-admin.company-data.datatable', compact('companies', 'departments', 'rates'));
        }
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $employees = DB::table('employees')->whereNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where("staff_no", "LIKE", "%" . $search . "%")
                        ->orWhere("employee_name", "LIKE", "%" . $search . "%");
                })
                ->join('companies', 'companies.id', '=', 'employees.company_id')
                ->join('departments', 'departments.id', '=', 'employees.department_id')
                ->join('rates', 'rates.id', '=', 'employees.rate_id')
                ->select(
                    'employees.*',
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

    public function markPresent(Request $request)
    {
        $date = $request->input('date');
        $present = Present::where('present', true)->first();
        $employees_id_array = $request->input('id');
        $month_no = date('m', strtotime($date));
        foreach ($employees_id_array as $employee) {
            try {
                $employee = Employee::findOrFail($employee);
                $count = DB::table('employee_present')->where('employee_id', $employee->id)
                    ->whereMonth('date', $month_no)->get()->count();
                $rate = $employee->rate->rate;
                $total = 0;
                $days_present = 0;
                $check_presence_on_date = DB::table('employee_present')->where('employee_id', $employee->id)
                    ->where('date', $date)->get()->count();
                if ($check_presence_on_date == 0) {
                    if ($count != 0) {
                        $days_present = ($count + 1);
                        $total = $rate * ($count + 1);
                    }
                    if ($count == 0) {
                        $days_present = 1;
                        $total = (float)$rate;
                    }
                }
                if ($check_presence_on_date == 1) {
                    continue;
                }
            } catch (Exception $e) {
                return response()->json(['marking_error' => 'User(s) data not deleted. Kindly contact software developer for help.']);
            }
            $employee->presents()->attach($present, ['days_present' => $days_present, 'total' => $total, 'date' => $date]);
        }

        return response()->json(['success' => 'Employee(s) marked present.']);
    }

    public function paymentsIndex()
    {
        $user = User::find(Auth::user()->id);
        $companies = DB::table('companies')->orderBy('name', 'asc')->get();
        $departments = DB::table('departments')->orderBy('name', 'asc')->get();
        $rates = DB::table('rates')->orderBy('rate', 'asc')->get();

        $years = [
            date('Y'), date('Y') - 1, date('Y') - 2, date('Y') - 3,
        ];

        if ($user->hasRole('super-admin')) {
            return view('super-admin.payments.datatable', compact('years', 'companies', 'departments', 'rates'));
        }
    }

    public function monthlyPayments(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');
            $year = $request->input('year_request');
            $month = $request->input('month_request');

            // DB::statement("SET SQL_MODE=''");

            // $latestPresent = DB::table('employee_present')
            //     ->orderBy('days_present', 'desc')
            //     ->groupBy('employee_id')
            //     ->select(DB::raw('max(total)'));

            // // dd($latestPresent);

            // $employees = DB::table('employee_present')
            //     ->whereYear('date', $year)
            //     ->whereMonth('date', $month)
            //     ->join('employees', 'employees.id', '=', 'employee_id')
            //     ->where(function ($query) use ($search) {
            //         $query->where("staff_no", "LIKE", "%" . $search . "%")
            //             ->orWhere("employee_name", "LIKE", "%" . $search . "%");
            //     })
            //     ->join('rates', function ($join) {
            //         $join->on(function ($query) {
            //             $query->on('rates.id', '=', 'employees.rate_id');
            //         });
            //     })
            //     ->whereIn('employee_present.total', $latestPresent)
            //     ->select(
            //         'employees.id as id',
            //         'employees.staff_no as staff_no',
            //         'employees.employee_name as employee_name',
            //         'employee_present.days_present as days_present',
            //         'rates.rate as rate',
            //         'employee_present.total as total',
            //         'employee_present.date as date',
            //     )
            //     ->orderBy('days_present', 'desc')->get();

            DB::statement("SET SQL_MODE=''");

            // $latestPresent = DB::table('employee_present')
            //     ->orderBy('days_present', 'desc')
            //     ->groupBy('employee_id')
            //     ->select(DB::raw('max(total)'));

            // dd($latestPresent);

            $employees = DB::table('employee_present')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->join('employees', 'employees.id', '=', 'employee_id')
                ->where(function ($query) use ($search) {
                    $query->where("staff_no", "LIKE", "%" . $search . "%")
                        ->orWhere("employee_name", "LIKE", "%" . $search . "%");
                })
                ->join('rates', function ($join) {
                    $join->on(function ($query) {
                        $query->on('rates.id', '=', 'employees.rate_id');
                    });
                })
                ->select(
                    'employees.id as id',
                    'employees.staff_no as staff_no',
                    'employees.employee_name as employee_name',
                    DB::raw('MAX(employee_present.days_present) as days_present'),
                    'rates.rate as rate',
                    DB::raw('MAX(employee_present.total) as total'),
                    DB::raw('MAX(employee_present.date) as date'),
                )
                ->orderBy('days_present', 'desc')->groupBy('id')->get();

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" name="employees_checkbox[]" class="employees_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox'])
                ->make(true);
        }
    }

    public function dailyPaymentsView()
    {
        $user = User::find(Auth::user()->id);
        $companies = DB::table('companies')->orderBy('name', 'asc')->get();
        $departments = DB::table('departments')->orderBy('name', 'asc')->get();
        $rates = DB::table('rates')->orderBy('rate', 'asc')->get();

        $years = [
            date('Y'), date('Y') - 1, date('Y') - 2, date('Y') - 3,
        ];

        if ($user->hasRole('super-admin')) {
            return view('super-admin.payments.daily-datatable', compact('years', 'companies', 'departments', 'rates'));
        }
    }

    public function dailyPayments(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');
            $date_request = $request->input('date_request');

            $date = '';
            if ($date_request == null) {
                $date = date('Y-m-d');
            }
            if ($date_request != null) {
                $date = $date_request;
            }

            $employees = DB::table('employee_present')
                ->where('date', $date)
                ->join('employees', 'employees.id', '=', 'employee_id')
                ->where(function ($query) use ($search) {
                    $query->where("staff_no", "LIKE", "%" . $search . "%")
                        ->orWhere("employee_name", "LIKE", "%" . $search . "%");
                })
                ->join('rates', function ($join) {
                    $join->on(function ($query) {
                        $query->on('rates.id', '=', 'employees.rate_id');
                    });
                })
                ->select(
                    'employees.id as id',
                    'employees.staff_no as staff_no',
                    'employees.employee_name as employee_name',
                    'employee_present.days_present as days_present',
                    'rates.rate as rate',
                    'employee_present.total as total',
                    'employee_present.date as date',
                )->orderBy('employee_name', 'asc')->get();

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" name="employees_checkbox[]" class="employees_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox'])
                ->make(true);
        }
    }

    public function markAbsent(Request $request)
    {
        $date = $request->input('date');
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $employees_id_array = $request->input('id');
        foreach ($employees_id_array as $employee) {
            $employee = Employee::findOrFail((int)$employee);

            $records = DB::table('employee_present')->where('employee_id', $employee->id)
                ->whereYear('date', $year)->whereMonth('date', $month)->orderBy('days_present', 'desc')
                ->get();

            if ($records->count() > 0) {
                foreach ($records as $record) {
                    $query_builder = DB::table('employee_present')->where('employee_id', $employee->id)
                        ->where('date', $date)->first();

                    $count = $record->days_present;
                    $rate = $employee->rate->rate;

                    if ($query_builder->days_present < $count) {
                        $total = 0;
                        $days_present = 0;

                        if ($count === 1) {
                            continue;
                        }
                        if ($count > 1) {
                            $days_present = ($count - 1);
                            $total = $rate * ($count - 1);

                            $query = DB::table('employee_present')->where('employee_id', $employee->id)
                                ->whereYear('date', $year)->whereMonth('date', $month)
                                ->where('days_present', $record->days_present)
                                ->where('total', $record->total)->limit(1);

                            $query->update(['days_present' => $days_present, 'total' => $total]);
                        }
                    }
                    if ($query_builder->days_present >= $count) {
                        continue;
                    }
                }
            }
            if ($records->count() == 0) {
                continue;
            }
            DB::table('employee_present')->where('employee_id', $employee->id)
                ->where('date', $date)->delete();
        }
        return response()->json(['success' =>
        'Employee(s) marked absent. Any subsequent payments record related to the employee has been updated successfully.']);
    }

    public function monthlyChart()
    {
        $user = User::find(Auth::user()->id);

        if ($user->hasRole('super-admin')) {
            return view('super-admin.charts.monthly-chart');
        }
    }

    public function yearlyChart()
    {
        $user = User::find(Auth::user()->id);

        if($user->hasRole('super-admin')) {
            return view('super-admin.charts.yearly-chart');
        }
    }
}
