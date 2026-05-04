<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use App\Models\SocialLink;
use App\Models\ApPhoto;
use App\Models\ApPhotoCategory;
use App\Models\ApPhotoTag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index() : View
    {
        $adminId = Auth::guard('admin')->id();
        $todayStart = Carbon::today();
        $last7DaysStart = Carbon::today()->subDays(6);
        $last30DaysStart = Carbon::today()->subDays(29);
        $last90DaysStart = Carbon::today()->subDays(89);



        $stats = Cache::remember('admin_dashboard_stats', 600, function () use ($last30DaysStart, $last7DaysStart, $todayStart, $last90DaysStart) {
            $data = [];
            $data['overallNewsLast30Days'] = News::whereDate('created_at', '>=', $last30DaysStart)->count();
            $data['overallNewsLast7Days'] = News::whereDate('created_at', '>=', $last7DaysStart)->count();
            $data['overallNewsToday'] = News::whereDate('created_at', '>=', $todayStart)->count();
            $data['overallNewsLast90Days'] = News::whereDate('created_at', '>=', $last90DaysStart)->count();

            // AP Photo Statistics
            $data['apTotalPhotos'] = ApPhoto::count();
            $data['apTotalCategories'] = ApPhotoCategory::count();
            $data['apTotalTags'] = ApPhotoTag::count();
            $data['apPhotosToday'] = ApPhoto::whereDate('created_at', '>=', $todayStart)->count();
            $data['apPhotosLast7Days'] = ApPhoto::whereDate('created_at', '>=', $last7DaysStart)->count();
            $data['apPhotosLast30Days'] = ApPhoto::whereDate('created_at', '>=', $last30DaysStart)->count();
            $data['apPhotosLast90Days'] = ApPhoto::whereDate('created_at', '>=', $last90DaysStart)->count();

            if (canAccess(['access management index'])) {
                $data['pendingNews'] = News::where(['status' => 1, 'is_approved' => 0])->count();
                $data['Categories'] = Category::count();
                $data['languages'] = Language::count();
                $data['roles'] = Role::count();
                $data['permissions'] = Permission::count();
                $data['socials'] = SocialLink::count();
            } else {
                $data['pendingNews'] = 0;
                $data['Categories'] = 0;
                $data['languages'] = 0;
                $data['roles'] = 0;
                $data['permissions'] = 0;
                $data['socials'] = 0;
            }

            return $data;
        });

        return view('admin.dashboard.index', $stats);
    }
}
