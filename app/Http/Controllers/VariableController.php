<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Variable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VariableController extends Controller
{
    public function variableIndex(){
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.variables.datatable');
        }
    }

    public function variablesTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $variables = DB::table('variables')
                ->where(function ($query) use ($search) {
                    $query->where("name", "LIKE", "%" . $search . "%");
                })
                ->select(
                    'variables.*',
                )->orderBy('name', 'asc')->get();

            return DataTables::of($variables)
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
            'name' => ['required','unique:variables', 'min:3'],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => Str::title($request->name),
        );

        Variable::create($form_data);

        return response()->json(['success' => 'Variable data added successfully.']);
    }

    public function editVariable($id)
    {
        if (request()->ajax()) {
            $data = Variable::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function updateVariable(Request $request)
    {
        $id = $request->hidden_id;
        $rules = array(
            'name' => [
                'required', 
                'min:3',
                Rule::unique('variables')->ignore($id),
            ],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => Str::title($request->name)
        );

        Variable::whereId($request->hidden_id)->update($form_data);

        return response()->json(['update_success' => 'Variable data updated successfully.']);
    }

    public function destroyVariable($id)
    {
        $data = Variable::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Variable data deleted.']);
    }
}
