<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Spatie\Activitylog\Models\Activity;
use RealRashid\SweetAlert\Facades\Alert;
use App\DataTables\Admin\AdminUsersDataTable;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminUsersController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('HasPermit:admin_index', only: ['index']),
            new Middleware('HasPermit:admin_create', only: ['create', 'store']),
            new Middleware('HasPermit:admin_edit', only: ['edit', 'update']),
            new Middleware('HasPermit:admin_show', only: ['show']),
            new Middleware('HasPermit:admin_delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AdminUsersDataTable $dataTable)
    {
        return $dataTable->render('admins.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name', 'asc')->get();
        return view('admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'regex:/\w*$/', 'max:255', 'unique:' . Admin::class],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:' . Admin::class],
            'role' => ['required', 'string', 'exists:' . Role::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        DB::beginTransaction();

        try {
            $role = Role::where('slug', $request->role)->first();

            $admin = Admin::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $admin->assignRole($role);

            DB::commit();

            Alert::success('Success!', 'Admin created successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.admins.index');
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            Alert::error('Error!', 'Something went wrong please try again later.' . $e->getMessage())->hideCloseButton()->persistent('Dismiss');;
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
    public function show(Request $request, string $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);
            $admin = Admin::find($decrypted);
        } catch (DecryptException $e) {
            return abort(404);
        }

        $page_title = $admin->name . ' activity logs';

        $events = Activity::selectRaw('event as name, MAX(id) as max_id')
            ->whereCauserType('App\Models\Admin')
            ->whereCauserId($admin->id)
            ->groupBy('name')
            ->orderByDesc('max_id') // Order by max_id (aggregated column)
            ->get()
            ->pluck('name');

        $subjects = Activity::selectRaw('subject_type as subject, MAX(id) as max_id')
            ->whereCauserType('App\Models\Admin')
            ->whereCauserId($admin->id)
            ->groupBy('subject')
            ->orderByDesc('max_id')
            ->get()
            ->pluck('subject');

        // Base query for activity logs
        $activity_logs = Activity::whereCauserType('App\Models\Admin')
            ->whereCauserId($admin->id);

        // Log the activity for viewing activity logs
        $adminUser = Admin::find(Auth::guard('admin')->id());
        
        activity()
            ->performedOn(new Activity())
            ->causedBy($adminUser)
            ->event('details')
            ->log('Admin user activity logs page details');

        // Date range filter
        if ($request->has('sdate') && $request->sdate != null && $request->has('edate') && $request->edate != null) {
            $to = Carbon::parse($request->edate)->format('Y-m-d');
            $from = Carbon::parse($request->sdate)->format('Y-m-d');
            $activity_logs = $activity_logs->whereDate('created_at', '<=', $to)->whereDate('created_at', '>=', $from);
        }

        // Subject filter
        if ($request->has('subject') && $request->subject != null) {
            if ($request->subject != 'All') {
                $activity_logs = $activity_logs->where('subject_type', 'App\Models\\' . $request->subject);
            }
        }

        // Event filter
        if ($request->has('event') && $request->event != null) {
            if ($request->event != 'All') {
                $activity_logs = $activity_logs->where('event', $request->event);
            }
        }

        // Order and paginate activity logs
        $activity_logs = $activity_logs->orderByDesc('id')->paginate(15);

        return view('admins.activity_logs', compact('activity_logs', 'page_title', 'events', 'subjects'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);
            $admin = Admin::findOrFail($decrypted);
            $roles = Role::where('guard_name', 'admin')->orderBy('name', 'asc')->get();
            return view('admins.edit', compact('roles', 'admin'));
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
        $admin = Admin::findOrFail($decrypted);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'regex:/\w*$/', 'max:255', 'unique:' . Admin::class . ',username,' . $admin->id . ',id'],
            'role' => ['required', 'string', 'exists:' . Role::class . ',slug'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:' . Admin::class . ',email,' . $admin->id . ',id'],
            'banned_at' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        }

        DB::beginTransaction();

        try {
            $role = Role::where('slug', $request->role)->first();

            $admin->name = $request->name;
            $admin->username = $request->username;
            $admin->email = $request->email;
            $admin->banned_at = $request->banned_at ? date('Y-m-d H:i:s', strtotime($request->banned_at)) : null;
            $admin->disabled_at = $request->disabled_at ? date('Y-m-d H:i:s', strtotime(now())) : null;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }
            $admin->save();

            $admin->assignRole($role);

            DB::commit();

            Alert::success('Success!', 'Admin updated successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.admins.index');
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

            $data = Admin::where('id', $decrypted)->firstOrFail();

            if ($data->delete()) {
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
