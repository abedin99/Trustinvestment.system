<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use RealRashid\SweetAlert\Facades\Alert;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Geting the task for clearing a new resource.
     */
    public function clear(Request $request)
    {
        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        if ($request->server('HTTP_REFERER')) {
            Alert::success('Success!', 'Application optimized successfully!')->hideCloseButton()->autoClose(3000);
            return redirect()->back();
        }

        toast("Application optimized successfully!", "success")->timerProgressBar();
        return redirect()->route('index');
    }

    /**
     * Migrate the task for all fresh new resource.
     */
    public function migrateFresh(Request $request)
    {
        if (!App::environment(['local', 'staging', 'test', 'testing'])) {
            toast("You cannot run this command. App is running in production mode.", "error")->timerProgressBar();
            return redirect()->route('index');
        }

        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        $msg = "Migrate fresh command run successfully.";

        if ($request->server('HTTP_REFERER')) {
            Alert::success('Success!', $msg)->hideCloseButton()->autoClose(3000);
            return redirect()->back();
        }

        toast($msg, "success")->timerProgressBar();
        return redirect()->route('index');
    }
}
