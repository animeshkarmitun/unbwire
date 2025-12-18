<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display subscription plans page
     */
    public function plans()
    {
        $packages = SubscriptionPackage::active()
            ->orderBy('sort_order')
            ->get();
        
        $user = Auth::user();
        $userSubscription = $user ? $user->activeSubscription : null;
        $pendingSubscription = $user ? $user->pendingSubscription : null;

        return view('frontend.subscription.plans', compact('packages', 'userSubscription', 'pendingSubscription'));
    }

    /**
     * Show subscription details and checkout
     */
    public function checkout($packageId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to subscribe.');
        }

        $package = SubscriptionPackage::active()->findOrFail($packageId);
        $user = Auth::user();

        // Check if user already has a pending subscription
        $pendingSubscription = $user->pendingSubscription;
        if ($pendingSubscription) {
            return redirect()->route('subscription.plans')
                ->with('error', 'You already have a pending subscription request. Please wait for admin approval before applying for a new subscription.');
        }

        // Check if user already has an active subscription
        $activeSubscription = $user->activeSubscription;
        
        return view('frontend.subscription.checkout', compact('package', 'activeSubscription'));
    }

    /**
     * Process subscription (manual for now, can integrate payment gateway later)
     */
    public function subscribe(Request $request, $packageId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to subscribe.');
        }

        $request->validate([
            'payment_method' => ['required', 'string', 'in:manual,stripe,paypal'],
            'billing_period' => ['required', 'in:monthly,yearly'],
        ]);

        $package = SubscriptionPackage::active()->findOrFail($packageId);
        $user = Auth::user();

        // Check if user already has a pending subscription
        $pendingSubscription = $user->pendingSubscription;
        if ($pendingSubscription) {
            return redirect()->route('subscription.plans')
                ->with('error', 'You already have a pending subscription request. Please wait for admin approval before applying for a new subscription.');
        }

        // Cancel existing active subscription if upgrading
        $activeSubscription = $user->activeSubscription;
        if ($activeSubscription) {
            $activeSubscription->status = 'cancelled';
            $activeSubscription->save();
        }

        // Calculate expiration date
        $startsAt = now();
        $expiresAt = $request->billing_period === 'yearly' 
            ? $startsAt->copy()->addYear() 
            : $startsAt->copy()->addMonth();

        // Create new subscription - always set to pending for admin approval
        $subscription = new UserSubscription();
        $subscription->user_id = $user->id;
        $subscription->subscription_package_id = $package->id;
        $subscription->starts_at = $startsAt;
        $subscription->expires_at = $expiresAt;
        $subscription->status = 'pending'; // Always pending - requires admin approval
        $subscription->payment_method = $request->payment_method;
        $subscription->payment_transaction_id = $request->payment_transaction_id ?? null;
        $subscription->auto_renew = $request->has('auto_renew');
        $subscription->save();

        // Inform user that admin approval is required
        toast('Subscription request submitted successfully! Your subscription will be activated after admin approval.', 'success');
        return redirect()->route('subscription.my-subscription')
            ->with('info', 'Your subscription is pending admin approval. You will be notified once it is activated.');
    }

    /**
     * Show user's current subscription
     */
    public function mySubscription()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;
        $pendingSubscription = $user->pendingSubscription;
        $subscriptionHistory = $user->subscriptions()
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.subscription.my-subscription', compact('activeSubscription', 'pendingSubscription', 'subscriptionHistory'));
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        $activeSubscription->auto_renew = false;
        $activeSubscription->save();

        toast('Subscription auto-renewal cancelled. Your subscription will remain active until ' . $activeSubscription->expires_at->format('M d, Y'), 'success');
        
        return redirect()->back();
    }
}

