<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:subscribers index,admin'])->only(['index']);
        $this->middleware(['permission:subscribers delete,admin'])->only(['destroy']);
        $this->middleware(['permission:subscribers update,admin'])->only(['toggleFullNewsEmail']);
    }

    /**
     * Display a listing of users (subscribers)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by email
        if ($request->has('search') && $request->search) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Filter by email notifications enabled
        if ($request->has('email_notifications') && $request->email_notifications !== '') {
            $query->where('email_notifications_enabled', $request->email_notifications);
        }

        // Filter by active subscription
        if ($request->has('has_subscription') && $request->has_subscription !== '') {
            if ($request->has_subscription == '1') {
                $query->whereHas('activeSubscription');
            } else {
                $query->whereDoesntHave('activeSubscription');
            }
        }

        $users = $query->with('activeSubscription.package')->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'total' => User::count(),
            'with_notifications' => User::where('email_notifications_enabled', true)->count(),
            'with_subscription' => User::whereHas('activeSubscription')->count(),
            'without_subscription' => User::whereDoesntHave('activeSubscription')->count(),
        ];

        return view('admin.subscriber.index', compact('users', 'stats'));
    }

    /**
     * Toggle email notifications for a user
     */
    public function toggleEmailNotifications(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->email_notifications_enabled = $request->boolean('status', !$user->email_notifications_enabled);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Email notifications ' . ($user->email_notifications_enabled ? 'enabled' : 'disabled'),
                'enabled' => $user->email_notifications_enabled
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle send full news email for a user
     */
    public function toggleFullNewsEmail(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->send_full_news_email = $request->boolean('status', !($user->send_full_news_email ?? true));
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Send full news email ' . ($user->send_full_news_email ? 'enabled' : 'disabled'),
                'enabled' => $user->send_full_news_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user (subscriber)
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('admin.Deleted Successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users list
     */
    public function export()
    {
        $users = User::select('email', 'email_notifications_enabled', 'language_preference', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'subscribers_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Email Notifications', 'Language Preference', 'Registered Date']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->email,
                    $user->email_notifications_enabled ? 'Yes' : 'No',
                    $user->language_preference ?? 'All',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
