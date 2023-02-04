<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Rate;
use App\Models\User;
use App\Imports\RatesImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;

class RateController extends Controller
{
    public function ratesIndex()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('super-admin')) {
            return view('super-admin.rates.datatable');
        }
    }

    public function ratesTable(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search.value');

            $rates = DB::table('rates')
                ->where(function ($query) use ($search) {
                    $query->where("rate", "LIKE", "%" . $search . "%");
                })
                ->select(
                    'rates.*',
                )->orderBy('rate', 'asc')->get();

            return DataTables::of($rates)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $button = '<button type="button" name="edit" id="' . $row->id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '  <button type="button" name="delete" id="' . $row->id . '" class="delete btn btn-outline-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->addColumn('checkbox', '<input type="checkbox" name="rates_checkbox[]" class="rates_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        try {
            Excel::import(new RatesImport(), $request->file('rate_import_file'));

            return back()->with('import-success', 'Successfully added rates data');
        } catch (NoTypeDetectedException) {
            return back()->with('error-message', 'Error importing data. Ensure .csv file type is attached');
        }
    }

    public function storeOne(Request $request)
    {
        $rules = array(
            'rate' => ['required', 'numeric', 'unique:rates'],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'rate' => $request->rate,
        );

        try {
            Rate::create($form_data);
        } catch (QueryException $e) {
            return response()->json(['range_error' => 'Rate should be 3 digits followed by 2 decimal places.']);
        }

        return response()->json(['success' => 'Rate added successfully.']);
    }

    public function editRate($id)
    {
        if (request()->ajax()) {
            $data = Rate::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function updateRate(Request $request)
    {
        $id = $request->hidden_id;
        $rules = array(
            'rate' => [
                'required',
                'numeric',
                Rule::unique('rates')->ignore($id),
            ],
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'rate' => $request->rate,
        );

        try {
            Rate::whereId($request->hidden_id)->update($form_data);
        } catch (QueryException $e) {
            return response()->json(['range_error' => 'Rate should be 3 digits followed by 2 decimal places.']);
        }

        return response()->json(['update_success' => 'Rate data updated successfully.']);
    }

    public function destroyRate($id)
    {
        $data = Rate::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Rate deleted.']);
    }

    public function deleteRates(Request $request)
    {
        $rate_id_array = $request->input('id');

        Rate::whereIn('id', $rate_id_array)->delete();

        return response()->json(['success' => 'Rates deleted.']);
    }
}
