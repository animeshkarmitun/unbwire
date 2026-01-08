<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriberNotificationController extends Controller
{
    protected UserNotificationService $notificationService;

    public function __construct(UserNotificationService $notificationService)
    {
        $this->middleware('auth')->except(['unsubscribe']);
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has unsubscribe token
        if (!$user->unsubscribe_token) {
            $user->unsubscribe_token = Str::random(64);
            $user->save();
        }

        $filter = $request->get('filter', 'all'); // all, unread, read

        $query = $user->notifications()->with('news.category', 'news.auther');

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = $user->unreadNotificationsCount();

        return view('frontend.notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    /**
     * View notification and redirect to news article
     */
    public function view($id)
    {
        $user = Auth::user();

        $notification = UserNotification::where('user_id', $user->id)
            ->findOrFail($id);

        // Mark as read
        if (!$notification->is_read) {
            $this->notificationService->markAsRead($notification);
        }

        // Get news
        $news = $notification->news;

        // Handle deleted/unpublished news
        if (!$news || $news->status != 1 || $news->is_approved != 1) {
            return redirect()->route('notifications.index')
                ->with('error', 'This news article is no longer available.');
        }

        // Redirect to news article
        return redirect()->route('news-details', $news->slug);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $notification = UserNotification::where('user_id', $user->id)
            ->findOrFail($id);

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'success' => true, 
            'message' => 'Notification marked as read',
            'unreadCount' => $user->unreadNotificationsCount()
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'All notifications marked as read',
            'unreadCount' => 0
        ]);
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['unreadCount' => 0]);
        }

        $user = Auth::user();
        $count = $user->unreadNotificationsCount();

        return response()->json(['unreadCount' => $count]);
    }

    /**
     * Unsubscribe from email notifications
     */
    public function unsubscribe(Request $request)
    {
        $token = $request->query('token');
        $user = User::where('unsubscribe_token', $token)->first();

        if (!$user) {
            return redirect()->route('home')
                ->with('error', 'Invalid unsubscribe link.');
        }

        // If form is submitted, update preference
        if ($request->isMethod('post')) {
            $user->email_notifications_enabled = false;
            $user->save();
            return view('frontend.notifications.unsubscribed', ['status' => 'success', 'message' => 'You have been successfully unsubscribed from email notifications.']);
        }

        return view('frontend.notifications.unsubscribe', compact('user'));
    }
}
