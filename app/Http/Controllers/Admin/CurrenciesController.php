<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CurrenciesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class CurrenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CurrenciesDataTable $dataTable)
    {
        return $dataTable->render('currencies.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $model = new Currency();

        activity()
            ->performedOn($model)
            ->causedBy(Auth::user())
            ->event('Page Visit')
            ->withProperties(['url' => URL::current()])
            ->log('Currency create page visited.');

        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $currency = new Currency;
            $currency->name         = $request->name;
            $currency->slug         = Str::random(11);
            $currency->symbol       = $request->symbol;
            $currency->currency     = $request->currency;
            $currency->save();

            DB::commit();

            Alert::success('Success!', 'Currency created successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.currencies.index');
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            Alert::error('Error!', 'Something went wrong please try again later.'. $e->getMessage())->hideCloseButton()->persistent('Dismiss');;
            return redirect()
                ->back()
                ->withInput($request->input());
        }

        Alert::error('Error!', 'Something went wrong please try again later.')->hideCloseButton()->persistent('Dismiss');;
        return redirect()
            ->back()
            ->withInput($request->input());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);
            $currency = Currency::findOrFail($decrypted);

            activity()
                ->performedOn($currency)
                ->causedBy(Auth::user())
                ->event('Page Visit')
                ->withProperties(['url' => URL::current()])
                ->log('Currency edit page visited.');

            return view('currencies.edit', compact('currency'));
        } catch (DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $decrypted = Crypt::decryptString($id);
        $currency = Currency::findOrFail($decrypted);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $currency->name         = $request->name;
            $currency->slug         = Str::random(11);
            $currency->symbol       = $request->symbol;
            $currency->currency     = $request->currency;
            $currency->save();

            DB::commit();

            Alert::success('Success!', 'Currency updated successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.currencies.index');
            
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            Alert::error('Error!', 'Something went wrong please try again later.')->hideCloseButton()->autoClose(3000);
            dd($e->getMessage());
            return redirect()
                ->back()
                ->withInput($request->input());
        }

        Alert::error('Error!', 'Something went wrong please try again later.')->hideCloseButton()->autoClose(3000);
        return redirect()
            ->back()
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);
            
            $data = Currency::where('id', $decrypted)->firstOrFail();

            if($data->delete())
            {
                return response()->json([
                    'success' => true,
                    'message' => 'Your data has been deleted.',
                    'status' => 200,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Something\'s gone wrong. please try again!',
                'status' => 201,
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
                'status' => 201,
            ]);
        }
    }
}