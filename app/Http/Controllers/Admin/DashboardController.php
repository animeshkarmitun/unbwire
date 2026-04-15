<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use App\Models\SocialLink;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index() : View
    {
        $adminId = Auth::guard('admin')->id();
        $todayStart = Carbon::today();
        $last7DaysStart = Carbon::today()->subDays(6);
        $last30DaysStart = Carbon::today()->subDays(29);

        $userNewsBaseQuery = News::query()->where(function ($query) use ($adminId) {
            $query->where('auther_id', $adminId)
                ->orWhere(function ($subQuery) use ($adminId) {
                    $subQuery->where('created_by', $adminId)
                        ->where('created_by_type', 'admin');
                });
        });

        $userTotalNews = (clone $userNewsBaseQuery)->count();
        $userTotalNewsLast30Days = (clone $userNewsBaseQuery)->whereDate('created_at', '>=', $last30DaysStart)->count();
        $userTotalNewsLast7Days = (clone $userNewsBaseQuery)->whereDate('created_at', '>=', $last7DaysStart)->count();
        $userTotalNewsToday = (clone $userNewsBaseQuery)->whereDate('created_at', '>=', $todayStart)->count();

        $overallNewsLast30Days = News::whereDate('created_at', '>=', $last30DaysStart)->count();
        $overallNewsLast7Days = News::whereDate('created_at', '>=', $last7DaysStart)->count();
        $overallNewsToday = News::whereDate('created_at', '>=', $todayStart)->count();
        $overallTotalNews = News::count();

        $publishedNews = 0;
        $pendingNews = 0;
        $Categories = 0;
        $languages = 0;
        $roles = 0;
        $permissions = 0;
        $socials = 0;

        if (canAccess(['access management index'])) {
            $publishedNews = News::where(['status' => 1, 'is_approved' => 1])->count();
            $pendingNews = News::where(['status' => 1, 'is_approved' => 0])->count();
            $Categories = Category::count();
            $languages = Language::count();
            $roles = Role::count();
            $permissions = Permission::count();
            $socials = SocialLink::count();
        }

        return view('admin.dashboard.index', compact(
            'publishedNews',
            'pendingNews',
            'Categories',
            'languages',
            'roles',
            'permissions',
            'socials',
            'userTotalNews',
            'userTotalNewsLast30Days',
            'userTotalNewsLast7Days',
            'userTotalNewsToday',
            'overallNewsLast30Days',
            'overallNewsLast7Days',
            'overallNewsToday',
            'overallTotalNews'
        ));
    }
}
