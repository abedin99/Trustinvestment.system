<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Auth;

class Permission
{
    static function permit($name):bool
    {
        $names = is_array($name)?$name:array($name);
        $role = Auth::guard('admin')->user()->roles()->first();

        if($role){
            $permissions = $role->hasAnyPermission($names);

            if ($permissions) {
                return true;
            }
        }
        return false;
    }

    static function access($name):bool
    {
        $names = is_array($name)?$name:array($name);
        $role = Auth::user()->roles()->first();

        if($role){
                
            $permissions = $role->hasAnyPermission($names);

            if ($permissions) {
                return true;
            }
        }
        return false;
    }
}