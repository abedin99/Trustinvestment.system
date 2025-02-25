<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rules\Password;

class ProfilePasswordController extends Controller
{
    /**
     * Display the admin's profile password.
     */
    public function edit(Request $request): View
    {
        activity()
            ->performedOn(new Admin())
            ->causedBy(Auth::user())
            ->event('visited')
            ->log('Change Password page visited.');

        $admin = Auth::guard('admin')->user();
        return view('profile.password', compact('admin'));
    }

    /**
     * Update the admin's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $admin = Admin::find(Auth::guard('admin')->id());
        $admin->password = Hash::make($validated['password']);
        $admin->save();

        Alert::success('Success!', 'Your data updated successfully.')->hideCloseButton()->autoClose(3000);

        return back();
    }
}