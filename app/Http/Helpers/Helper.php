<?php

namespace App\Http\Helpers;

use Carbon\Carbon;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class Helper
{
    static function getAvatar($url)
    {
        if ($url != null) {
            return asset($url);
        }

        return asset('assets/img/avatar.png');
    }

    static function showBalance($amount)
    {
        return "$".$amount;
    }
}
