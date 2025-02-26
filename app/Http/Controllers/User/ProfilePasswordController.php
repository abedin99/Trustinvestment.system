<?php

namespace App\Http\Controllers\User;

use App\Models\User;
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
        $user = Auth::guard('web')->user();
        
        activity()
            ->performedOn($request->user())
            ->causedBy($request->user())
            ->event('visited')
            ->log('Change Password page visited.');

        return view('profile.password', compact('user'));
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

        $user = User::find(Auth::id());
        $user->password = Hash::make($validated['password']);
        $user->save();

        Alert::success('Success!', 'Your data updated successfully.')->hideCloseButton()->autoClose(3000);

        return back();
    }
}