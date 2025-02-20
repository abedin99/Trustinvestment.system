<?php

namespace App\Http\Helpers;

use App\Models\Setting as SettingModel;

class Setting
{
    static function get($option, $arr = false)
    {
        $setting = SettingModel::whereOption($option)->first();
        if ($setting) {
            if ($arr == true) {
                return $setting;
            }
            return $setting->value;
        }
    }

    static function getBySlug($slug, $arr = false)
    {
        $setting = SettingModel::whereSlug($slug)->first();
        if ($setting) {
            if ($arr == true) {
                return $setting;
            }
            return $setting->value;
        }
    }

    static function getById($id, $arr = false)
    {
        $setting = SettingModel::find($id);
        if ($setting) {
            if ($arr == true) {
                return $setting;
            }
            return $setting->value;
        }
    }
}
