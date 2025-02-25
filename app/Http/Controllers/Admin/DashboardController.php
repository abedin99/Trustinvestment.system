<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Admin;
use App\Models\PostShare;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('HasPermit:dashboard', only: ['index']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Admin::find(Auth::guard('admin')->id());

        activity()
            ->performedOn($admin)
            ->causedBy($admin)
            ->event('visited')
            ->log('Admin dashboard page visited.');

        return view('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function allActivityLogs(Request $request)
    {
        $page_title = 'All activity logs';
        $events = Activity::selectRaw('event as name')->whereCauserType('App\Models\Admin')->groupBy('name')->get()->pluck('name');
        $userIds = Activity::selectRaw('causer_id as userId')->whereCauserType('App\Models\Admin')->groupBy('userId')->get()->pluck('userId');
        $subjects = Activity::selectRaw('subject_type as subject')->whereCauserType('App\Models\Admin')->groupBy('subject')->get()->pluck('subject');
        $users = Admin::whereIn('id', $userIds)->get();
        $activity_logs = Activity::query()->whereCauserType('App\Models\Admin');

        $model = new Activity();

        activity()
            ->performedOn($model)
            ->causedBy(Auth::user())
            ->event('Visited')
            ->log('Activity logs page visited');

        // date to date report
        if ($request->has('sdate') && $request->sdate != null && $request->has('edate') && $request->edate != null) {
            $to = Carbon::parse($request->edate)->format('Y-m-d');
            $from = Carbon::parse($request->sdate)->format('Y-m-d');

            $activity_logs = $activity_logs->whereDate('created_at', '<=', $to)->whereDate('created_at', '>=', $from);
        }

        if ($request->has('event') && $request->event != null) {
            if ($request->event != 'All') {
                $activity_logs = $activity_logs->where('event', $request->event);
            }
        }

        if ($request->has('subject') && $request->subject != null) {
            if ($request->subject != 'All') {
                $activity_logs = $activity_logs->where('subject_type', 'App\Models\\'.$request->subject);
            }
        }

        if ($request->has('user') && $request->user != null) {
            if ($request->user != 'All') {
                $user = Admin::where('username', $request->user)->first();
                if ($user) {
                    $activity_logs = $activity_logs->where('causer_id', $user->id);
                }
            }
        }

        $activity_logs = $activity_logs->orderByDesc('id')->paginate(10);

        return view('activity_logs', compact('activity_logs', 'page_title', 'events', 'users', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeExportActivityLog(Request $request)
    {
        if (Auth::guard('admin')->check() == false) {
            return response()->json([
                'message' => 'Unauthorized',
                'success' => false,
                'status' => 401,
            ]);
        }

        if (!$request->type || !$request->model) {
            Auth::guard('admin')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Something went wrong. Your session has been destroyed!',
                'success' => false,
                'status' => 401,
            ]);
        }

        $admin = Admin::find(Auth::guard('admin')->id());
        $referer = $request->headers->get('referer');
        $modelClass = '\\App\\Models\\' . $request->model;
        $model = new $modelClass;
        $message = 'Datatables export action ' . $request->type . ' executed in ' . $request->model . '.' .
            (($referer) ? Helper::convertUrlToLink('url: ' . $referer) : null);

        activity()
            ->performedOn($model)
            ->causedBy($admin)
            ->event('export')
            ->log($message);

        return response()->json([
            'message' => $message,
            'success' => true,
            'status' => 201,
        ]);
    }
}
