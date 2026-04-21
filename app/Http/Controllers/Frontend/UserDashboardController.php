<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use ZipArchive;

class UserDashboardController extends Controller
{
    /**
     * Dashboard Overview with Statistics
     */
    public function index()
    {
        $user = Auth::user();
        
        // Export Statistics from ActivityLog
        $baseQuery = ActivityLog::where('user_id', $user->id)
            ->where('user_type', 'user')
            ->where('action', 'exported')
            ->where('model_type', News::class);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'last_30_days' => (clone $baseQuery)->where('created_at', '>=', now()->subDays(30))->count(),
            'last_7_days' => (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', Carbon::today())->count(),
        ];

        return view('frontend.dashboard.index', compact('stats'));
    }

    /**
     * Searchable News List
     */
    public function newsList(Request $request)
    {
        $user = Auth::user();
        $query = News::query();

        // Default to Today if no search
        if (!$request->filled('from_date') && !$request->filled('to_date')) {
            $query->whereDate('created_at', Carbon::today());
        } else {
            // Validation for 10-day gap
            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);
            
            if ($fromDate->diffInDays($toDate) > 10) {
                return redirect()->back()->with('error', 'The date range window cannot exceed 10 days.');
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
        }

        // Apply same filtering as main news page
        $package = $user->currentPackage();
        $subscriptionTier = $package ? $package->slug : 'free';
        
        $news = $query->activeEntries()
            ->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->orderBy('created_at', 'DESC')
            ->paginate(20)
            ->withQueryString();

        return view('frontend.dashboard.news-list', compact('news'));
    }

    /**
     * Export all news in range as ZIP
     */
    public function exportZip(Request $request)
    {
        $user = Auth::user();
        
        // Date range validation
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        
        if ($fromDate->diffInDays($toDate) > 10) {
            return redirect()->back()->with('error', 'Maximum allowed window for bulk export is 10 days.');
        }

        // Fetch news
        $package = $user->currentPackage();
        $subscriptionTier = $package ? $package->slug : 'free';

        $newsItems = News::whereDate('created_at', '>=', $request->from_date)
            ->whereDate('created_at', '<=', $request->to_date)
            ->activeEntries()
            ->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->get();

        if ($newsItems->isEmpty()) {
            return redirect()->back()->with('error', 'No news articles found for the selected date range.');
        }

        // Generate ZIP
        $zipName = 'unb-news-export-' . $fromDate->format('Y-m-d') . '-to-' . $toDate->format('Y-m-d') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Failed to create ZIP archive.');
        }

        foreach ($newsItems as $item) {
            $folderName = Str::slug($item->title) . '-' . $item->created_at->format('Y-m-d');
            
            // Content
            $content = "Title: " . $item->title . "\n";
            $content .= "Date: " . $item->created_at->format('M d, Y') . "\n";
            $content .= "Category: " . ($item->category->name ?? 'N/A') . "\n";
            $content .= "--------------------------------------------------\n\n";
            $content .= strip_tags($item->content);
            
            $zip->addFromString($folderName . '/content.txt', $content);

            // Image
            if ($item->image && File::exists(public_path($item->image))) {
                $imagePath = public_path($item->image);
                $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
                $zip->addFile($imagePath, $folderName . '/main-image.' . $ext);
            }
            
            // Log activity for each (optional, but since we are showing stats, maybe we just log the zip export once?)
            // The user wants "News Export statistic", usually single exports are logged.
            // I'll log the ZIP export as a special bulk action.
            $item->logActivity('exported', null, ['zip_name' => $zipName, 'format' => 'zip']);
        }

        $zip->close();

        // Optionally log activity for the batch
        // Since the requirement asks for stats (Total, etc.), we should probably treat industrial exports 
        // as credits? I'll stick to logging the batch.

        return Response::download($zipPath)->deleteFileAfterSend(true);
    }
}
