<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class UserSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:subscription package index,admin'])->only(['index']);
        $this->middleware(['permission:subscription package update,admin'])->only(['update', 'approve', 'updateExpiryDate', 'edit']);
        $this->middleware(['permission:subscription package delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of user subscriptions.
     */
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'package']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by package
        if ($request->has('package_id') && $request->package_id !== '') {
            $query->where('subscription_package_id', $request->package_id);
        }

        // Order by: pending first, then by created date
        $subscriptions = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $packages = \App\Models\SubscriptionPackage::active()->get();

        return view('admin.user-subscription.index', compact('subscriptions', 'packages'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(string $id)
    {
        $subscription = UserSubscription::with('user')->findOrFail($id);
        $packages = \App\Models\SubscriptionPackage::active()->get();
        return view('admin.user-subscription.edit', compact('subscription', 'packages'));
    }

    /**
     * Update subscription status and details
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,expired,cancelled,pending'],
            'package_id' => ['required', 'exists:subscription_packages,id'],
            'expires_at' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscription = UserSubscription::with('user')->findOrFail($id);
        
        // Update User Profile
        if ($subscription->user) {
            $user = $subscription->user;
            
            // Check email uniqueness if email changed
            if ($user->email !== $request->email) {
                 $request->validate([
                     'email' => ['unique:users,email'],
                 ]);
            }
            
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Handle boolean toggles (unchecked = false, so use boolean())
            $user->email_notifications_enabled = $request->boolean('email_notifications_enabled');
            $user->send_full_news_email = $request->boolean('send_full_news_email');
            
            $user->save();
        }

        // Update Subscription
        $subscription->status = $request->status;
        $subscription->subscription_package_id = $request->package_id;
        $subscription->expires_at = $request->expires_at;
        $subscription->save();

        toast(__('Updated Successfully!'), 'success')->width('350');

        return redirect()->route('admin.user-subscription.index');
    }

    /**
     * Update subscription expiry date
     */
    public function updateExpiryDate(Request $request, $id)
    {
        $request->validate([
            'expires_at' => ['required', 'date', 'after:today'],
        ]);

        $subscription = UserSubscription::findOrFail($id);
        $subscription->expires_at = $request->expires_at;
        $subscription->save();

        toast(__('admin.Expiry date updated successfully!'), 'success')->width('350');

        return redirect()->back();
    }

    /**
     * Approve pending subscription
     */
    public function approve($id)
    {
        $subscription = UserSubscription::with('user')->findOrFail($id);
        
        if ($subscription->status !== 'pending') {
            toast(__('admin.Only pending subscriptions can be approved!'), 'error')->width('350');
            return redirect()->back();
        }

        $subscription->status = 'active';
        $subscription->save();

        // TODO: Send notification email to user about subscription activation
        // Mail::to($subscription->user->email)->send(new SubscriptionActivatedMail($subscription));

        toast(__('admin.Subscription approved successfully! The user account has been activated.'), 'success')->width('350');

        return redirect()->back();
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(string $id)
    {
        try {
            $subscription = UserSubscription::findOrFail($id);
            $subscription->delete();

            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }
}

