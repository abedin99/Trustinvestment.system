<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Spatie\Activitylog\Models\Activity;
use App\DataTables\Admin\UsersDataTable;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Contracts\Encryption\DecryptException;

class UsersController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('HasPermit:user_index', only: ['index']),
            new Middleware('HasPermit:user_create', only: ['create', 'store']),
            new Middleware('HasPermit:user_edit', only: ['edit', 'update']),
            new Middleware('HasPermit:user_show', only: ['show']),
            new Middleware('HasPermit:user_delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $model = new User();

        activity()
            ->performedOn($model)
            ->causedBy(Auth::guard('admin')->user())
            ->event('Page Visit')
            ->withProperties(['url' => URL::current()])
            ->log('User create page visited.');

        $departments = Department::orderBy('name', 'asc')->whereNull('parent_id')->get();
        return view('users.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'regex:/\w*$/', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:' . User::class],
            'departments' => ['required'],
            'departments.*' => ['required', 'exists:' . Department::class . ',slug'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'disabled_at' => ['nullable', 'in:on,1,true'],
        ]);

        DB::beginTransaction();

        if ($request->departments) {
            $departments = [];
            foreach ($request->departments as $key => $department) {
                $departments[] = Department::whereSlug($department)->first()->id;
            }
        }

        try {
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'departments' => $departments,
                'password' => Hash::make($request->password),
                'disabled_at' => $request->disabled_at ? date('Y-m-d H:i:s', strtotime(now())) : null,
            ]);

            DB::commit();

            Alert::success('Success!', 'User created successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.users.index');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);
            $user = User::findOrFail($decrypted);
            
            activity()
                ->performedOn($user)
                ->causedBy(Auth::guard('admin')->user())
                ->event('Page Visit')
                ->withProperties(['url' => URL::current()])
                ->log('User edit page visited.');

            $departments = Department::orderBy('name', 'asc')->whereNull('parent_id')->get();
            return view('users.edit', compact('departments', 'user'));
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
        $user = User::findOrFail($decrypted);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'regex:/\w*$/', 'max:255', 'unique:' . User::class . ',username,' . $user->id . ',id'],

            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:' . User::class . ',email,' . $user->id . ',id'],
            'departments' => ['required'],
            'departments.*' => ['required', 'exists:' . Department::class . ',slug'],
            'banned_at' => ['nullable', 'date_format:Y-m-d'],
            'disabled_at' => ['nullable', 'in:on,1,true'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        }

        DB::beginTransaction();

        if ($request->departments) {
            $departments = [];
            foreach ($request->departments as $key => $department) {
                $departments[] = Department::whereSlug($department)->first()->id;
            }
        }

        try {
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->departments = $departments;
            $user->banned_at = $request->banned_at ? date('Y-m-d H:i:s', strtotime($request->banned_at)) : null;
            $user->disabled_at = $request->disabled_at ? date('Y-m-d H:i:s', strtotime(now())) : null;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            DB::commit();

            Alert::success('Success!', 'User updated successfully.')->hideCloseButton()->autoClose(3000);
            return redirect()->route('admin.users.index');
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

            $data = User::where('id', $decrypted)->firstOrFail();

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

    /**
     * Display the specified resource.
     */
    public function activityLogs(Request $request, $id)
    {
        try {
            $decrypted = Crypt::decryptString($id);
    
            $user = User::where('id', $decrypted)->firstOrFail();
    
            $page_title = $user->name . ' activity logs';
    
            $events = Activity::selectRaw('event as name, MAX(id) as max_id')
                ->whereCauserType('App\Models\User')
                ->whereCauserId($user->id)
                ->groupBy('name')
                ->orderByDesc('max_id')
                ->get()
                ->pluck('name');
    
            $subjects = Activity::selectRaw('subject_type as subject, MAX(id) as max_id')
                ->whereCauserType('App\Models\User')
                ->whereCauserId($user->id)
                ->groupBy('subject')
                ->orderByDesc('max_id')
                ->get()
                ->pluck('subject');
    
            $activity_logs = Activity::whereCauserType('App\Models\User')
                ->whereCauserId($user->id);
    
            activity()
                ->performedOn(new Activity())
                ->causedBy(Auth::user())
                ->event('details')
                ->log('User activity logs page details');
    
            if ($request->has('sdate') && $request->has('edate')) {
                $to = Carbon::parse($request->edate)->format('Y-m-d');
                $from = Carbon::parse($request->sdate)->format('Y-m-d');
    
                $activity_logs = $activity_logs->whereDate('created_at', '<=', $to)
                    ->whereDate('created_at', '>=', $from);
            }
    
            if ($request->has('subject') && $request->subject != 'All') {
                $activity_logs = $activity_logs->where('subject_type', 'App\Models\\' . $request->subject);
            }
    
            if ($request->has('event') && $request->event != 'All') {
                $activity_logs = $activity_logs->where('event', $request->event);
            }
    
            $activity_logs = $activity_logs->orderByDesc('id')->paginate(15);
    
            return view('users.activity-logs', compact('user', 'activity_logs', 'page_title', 'events', 'subjects'));
        } catch (DecryptException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
                'status' => 201,
            ]);
        }
    }
    
}
