<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:subscribers index,admin'])->only(['index', 'pending']);
    }

    /**
     * Display email sending report
     */
    public function index(Request $request)
    {
        $query = UserNotification::with(['user', 'news'])
            ->whereNotNull('news_id');

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by email status
        if ($request->has('email_status') && $request->email_status !== '') {
            if ($request->email_status == 'sent') {
                $query->where('email_sent', true);
            } elseif ($request->email_status == 'pending') {
                $query->where('email_sent', false);
            }
        }

        // Filter by news
        if ($request->has('news_id') && $request->news_id) {
            $query->where('news_id', $request->news_id);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => UserNotification::whereNotNull('news_id')->count(),
            'sent' => UserNotification::whereNotNull('news_id')->where('email_sent', true)->count(),
            'pending' => UserNotification::whereNotNull('news_id')->where('email_sent', false)->count(),
            'today_sent' => UserNotification::whereNotNull('news_id')
                ->where('email_sent', true)
                ->whereDate('email_sent_at', today())
                ->count(),
            'today_pending' => UserNotification::whereNotNull('news_id')
                ->where('email_sent', false)
                ->whereDate('created_at', today())
                ->count(),
        ];

        // Get recent news for filter
        $recentNews = News::select('id', 'title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.email-report.index', compact('notifications', 'stats', 'recentNews'));
    }

    /**
     * Display pending email report
     */
    public function pending(Request $request)
    {
        $query = UserNotification::with(['user', 'news'])
            ->whereNotNull('news_id')
            ->where('email_sent', false);

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by news
        if ($request->has('news_id') && $request->news_id) {
            $query->where('news_id', $request->news_id);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_pending' => UserNotification::whereNotNull('news_id')
                ->where('email_sent', false)
                ->count(),
            'today_pending' => UserNotification::whereNotNull('news_id')
                ->where('email_sent', false)
                ->whereDate('created_at', today())
                ->count(),
            'oldest_pending' => UserNotification::whereNotNull('news_id')
                ->where('email_sent', false)
                ->orderBy('created_at', 'asc')
                ->first(),
        ];

        // Get recent news for filter
        $recentNews = News::select('id', 'title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.email-report.pending', compact('notifications', 'stats', 'recentNews'));
    }
}
