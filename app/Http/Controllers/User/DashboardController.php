<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use App\Models\User;
use App\Http\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->event('visited')
            ->log('Dashboard page visited.');

        return view('dashboard');
    }

    /**
     * Display a listing of the resource.
     */
    public function shareWithMe(Request $request)
    {
        $posts = Post::statusPublished()->with('categories')->whereHas('departments', function ($query) {
            $query->whereIn('department_id', Auth::guard('web')->user()->departments);
        })->paginate(15);

        activity()
            ->performedOn($request->user())
            ->causedBy($request->user())
            ->event('visited')
            ->log('Share with me page visited.');

        return view('dashboard', compact('posts'));
    }

    /**
     * Display the specified resource.
     */
    public function single($slug)
    {
        $user = Auth::guard('web')->user();
        $post = Post::where('slug', '=', $slug)
            ->with(['categories', 'status'])
            ->whereHas('departments', function ($query) use($user) {
                $query->whereIn('department_id', $user->departments);
            })->whereHas('status', function ($query) {
                $query->where('type', 'published');
            })
            ->firstOrFail();

        if (!Helper::postHasDepartmentPermission($post)) {
            return abort(404);
        }

        Helper::increasePostView($post);

        activity()
            ->performedOn(new User())
            ->causedBy($user)
            ->event('visited')
            ->log($post->title .' post details page visited.');

        return view('single', compact('post'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeExportActivityLog(Request $request)
    {
        if (Auth::guard('web')->check() == false) {
            return response()->json([
                'message' => 'Unauthorized',
                'success' => false,
                'status' => 401,
            ]);
        }

        if (!$request->type || !$request->model) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Something went wrong. Your session has been destroyed!',
                'success' => false,
                'status' => 401,
            ]);
        }

        $user = User::find(Auth::guard('web')->id());
        $referer = $request->headers->get('referer');
        $modelClass = '\\App\\Models\\' . $request->model;
        $model = new $modelClass;
        $message = 'Datatables export action ' . $request->type . ' executed in ' . $request->model . '.' .
            (($referer) ? Helper::convertUrlToLink('url: ' . $referer) : null);

        activity()
            ->performedOn($model)
            ->causedBy($user)
            ->event('export')
            ->log($message);

        return response()->json([
            'message' => $message,
            'success' => true,
            'status' => 201,
        ]);
    }
}
