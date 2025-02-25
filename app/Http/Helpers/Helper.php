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

        return asset('dist/img/avatar.png');
    }

    static function activeMenu($uri = '')
    {
        $active = false;
        if (Request::is(Request::segment(2) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri)) {
            $active = true;
        }
        return $active;
    }

    static function loadAttachmentPreview($path)
    {
        if(!$path){
            return false;
        }
        
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

        // for image only
        if(in_array($fileExtension, ['png', 'jpeg', 'jpg', 'GIF', 'TIFF', 'svg', 'webp'])){
            if (file_exists($path)) {
                return asset($path);
            }
            // no image
            return asset('/dist/img/icons/no-image-icon.png');
        }

        // for pdf
        if(in_array($fileExtension, ['pdf'])){
            return asset('/dist/img/icons/pdf-icon.png');
        }

        return asset('/dist/img/icons/file-icon.png');
    }

    static function increasePostView($post)
    {
        if (!$post) {
            return false;
        }

        $auth = Auth::guard('web')->user();

        if(Cache::has('post-view-' . $post->id . '-user-'. $auth->id)){
            return true;
        }
        
        $post->views()->create([
            'user_id' => $auth->id,
            'post_id' => $post->id,
        ]);

        $expiresAt = Carbon::now()->addMinutes(5);
        Cache::put('post-view-' . $post->id . '-user-'. $auth->id, true, $expiresAt); 

        return true;
    }

    static function postHasDepartmentPermission($post)
    {
        if (!$post) {
            return false;
        }
        $postDepartments = $post->departments()->get()->pluck('id');
        $permittedDepartments = auth()->user()->departments;
        $exists = $postDepartments->intersect($permittedDepartments)->isNotEmpty();
        
        return $exists;
    }

    static function getDepartmentHierarchy()
    {
        $user_departments = Auth::guard('web')->user()->departments;
        $parent_departments = Department::whereIn('id', $user_departments)->whereNotNull('parent_id')->get()->pluck('parent_id');

        // Convert user_departments array to collection
        $user_departments_collection = collect($user_departments);

        // Merge parent_departments collection with user_departments collection
        $merged_departments = $user_departments_collection->merge($parent_departments);

        // Remove duplicate department IDs
        $merged_departments = $merged_departments->unique();

        // Retrieve the departments using the merged collection
        $departments = Department::whereIn('id', $merged_departments)->get();

        // Initialize an empty array to hold the hierarchical data
        $departmentHierarchy = [];

        // Get parent departments
        $parentDepartments = $departments->whereNull('parent_id');

        // Loop through each parent department
        foreach ($parentDepartments as $parent) {
            // Add the parent department to the hierarchy
            $departmentHierarchy[$parent->id] = [
                'parent' => $parent,
                'children' => []
            ];

            // Retrieve children of the parent department
            $children = $departments->where('parent_id', $parent->id);

            // Add each child to the respective parent in the hierarchy
            foreach ($children as $child) {
                $departmentHierarchy[$parent->id]['children'][] = $child;
            }
        }

        return $departmentHierarchy;
    }

    /**
     * Check if a department exists in the hierarchical data.
     *
     * @param array $departments Hierarchical department data.
     * @param int $departmentId The ID of the department to check.
     * @return bool True if the department exists, false otherwise.
     */
    static function departmentPermissionExists($departmentId)
    {
        $departments = self::getDepartmentHierarchy();

        foreach ($departments as $department) {
            // Check parent department
            if ($department['parent']->id == $departmentId) {
                return true;
            }

            // Check children departments
            foreach ($department['children'] as $child) {
                if ($child->id == $departmentId) {
                    return true;
                }
            }
        }

        return false;
    }
    
    static function convertUrlToLink($text) {
        $pattern = '/(https?:\/\/[^\s]+)/';
        $replacement = '<a href="$1" target="_blank">$1</a>';
        return preg_replace($pattern, $replacement, e($text));
    }

}
