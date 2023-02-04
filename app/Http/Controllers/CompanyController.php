<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function companyIndex(){
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.companies.datatable');
        }
    }

    public function companyTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $companies = DB::table('companies')
                ->where(function ($query) use ($search) {
                    $query->where("name", "LIKE", "%" . $search . "%");
                })
                ->select(
                    'companies.*',
                )->orderBy('name', 'asc')->get();

            return DataTables::of($companies)
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
            'name' => ['required','unique:companies', 'min:3'],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => strtoupper($request->name),
        );

        Company::create($form_data);

        return response()->json(['success' => 'Company data added successfully.']);
    }

    public function editCompany($id)
    {
        if (request()->ajax()) {
            $data = Company::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function updateCompany(Request $request)
    {
        $id = $request->hidden_id;
        $rules = array(
            'name' => [
                'required', 
                'min:3',
                Rule::unique('companies')->ignore($id),
            ],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => strtoupper($request->name)
        );

        Company::whereId($request->hidden_id)->update($form_data);

        return response()->json(['update_success' => 'Company data updated successfully.']);
    }

    public function destroyCompany($id)
    {
        $data = Company::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Company data deleted.']);
    }
}
