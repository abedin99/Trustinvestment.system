<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\DataTables\Admin\RoleDataTable;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Contracts\Encryption\DecryptException;

class RoleController extends Controller implements HasMiddleware
{
    /**
    * Get the middleware that should be assigned to the controller.
    */
   public static function middleware(): array
   {
       return [
           new Middleware('HasPermit:role_index', only: ['index']),
           new Middleware('HasPermit:role_create', only: ['create', 'store']),
           new Middleware('HasPermit:role_edit', only: ['edit', 'update']),
           new Middleware('HasPermit:role_show', only: ['show']),
           new Middleware('HasPermit:role_delete', only: ['destroy']),
           new Middleware('HasPermit:role_permissions', only: ['permissions']),
       ];
   }

    /**
     * Display a listing of the resource.
     */
    public function index(RoleDataTable $dataTable)
    {
        $page_title = 'Manage Role';
        return $dataTable->render('roles.index', compact('page_title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $model = new Role();

        activity()
            ->performedOn($model)
            ->causedBy(Auth::guard('admin')->user())
            ->event('Page Visit')
            ->withProperties(['url' => URL::current()])
            ->log('Role create page visited.');

        $page_title = 'New Role';
        return view('roles.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:512', 'unique:'. Role::class],
            'status' => ['nullable', 'in:on,1,true'],
        ]);

        if ($validator->fails()) {
            Alert::error('Error!', 'Something\'s gone wrong. please try again!')->hideCloseButton()->autoClose(3000);

            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($request->input());
        }

        DB::beginTransaction();

        try {
            $role = new Role();
            $role->name       = $request->name;
            $role->slug       = Str::slug($request->name.' '.str::random(7));
            $role->guard_name = Auth::getDefaultDriver();
            $role->status     = ($request->status)?true:false;
            $role->save();

            DB::commit();

            Alert::success('Success!', 'Role Created successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.roles.index');
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            Alert::error('Error! => ' .$e->getMessage(), 'Something went wrong please try again later.')->hideCloseButton()->autoClose(false);
            return redirect()
                    ->back()
                    ->withInput($request->input());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $role = Role::where('slug', $slug)->firstOrFail();
        $permissions = Permission::where('guard_name', 'admin')->get();

        activity()
            ->performedOn($role)
            ->causedBy(Auth::guard('admin')->user())
            ->event('Page Visit')
            ->withProperties(['url' => URL::current()])
            ->log('Role permissions page visited.');

        return view('roles.permissions', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function permissions(Request $request, string $slug)
    {
        $request->validate([
            'permission' => 'required',
        ]);

        $role = Role::where('slug', $slug)->firstOrFail();
        $role->syncPermissions($request->permission);

        $role->updated_at     = now();
        $role->save();

        activity()
            ->performedOn($role)
            ->causedBy(Auth::guard('admin')->user())
            ->event('updated')
            ->log(Str::headline($role->name).' role permissions has been updated.');

        Alert::success('Success!', 'Permissions has been updated successfully.')->hideCloseButton()->autoClose(3000);
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $role = Role::where('slug', $slug)->firstOrFail();

        activity()
            ->performedOn($role)
            ->causedBy(Auth::guard('admin')->user())
            ->event('Page Visit')
            ->withProperties(['url' => URL::current()])
            ->log('Role edit page visited.');

        $page_title = 'New Role';
        return view('roles.edit', compact('page_title', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $role = Role::where('slug', $slug)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:512'],
            'status' => ['nullable', 'in:on,1,true'],
        ]);

        if ($validator->fails()) {
            Alert::error('Error!', 'Something\'s gone wrong. please try again!')->hideCloseButton()->autoClose(3000);

            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($request->input());
        }

        DB::beginTransaction();

        try {
            $role->name       = $request->name;
            $role->slug       = Str::slug($request->name.' '.str::random(7));
            $role->guard_name = Auth::getDefaultDriver();
            $role->status     = ($request->status)?true:false;
            $role->save();

            DB::commit();

            Alert::success('Success!', 'Role Updated successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.roles.index');
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            Alert::error('Error! => ' .$e->getCode(), 'Something went wrong please try again later.')->hideCloseButton()->autoClose(false);
            return redirect()
                    ->back()
                    ->withInput($request->input());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);

            $role = Role::where('id', $decrypted)->firstOrFail();

            if($role->delete())
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