<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    /**
     * Display the user profile page
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;
        $currentPackage = $user->currentPackage();
        $packages = SubscriptionPackage::active()
            ->orderBy('sort_order')
            ->get();

        return view('frontend.profile.index', compact('user', 'activeSubscription', 'currentPackage', 'packages'));
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Change subscription package
     */
    public function changePackage(Request $request)
    {
        $request->validate([
            'package_id' => ['required', 'exists:subscription_packages,id'],
        ]);

        $user = Auth::user();
        $newPackage = SubscriptionPackage::active()->findOrFail($request->package_id);
        $activeSubscription = $user->activeSubscription;

        // If user has an active subscription
        if ($activeSubscription && $activeSubscription->isActive()) {
            // Check if it's the same package
            if ($activeSubscription->subscription_package_id == $newPackage->id) {
                return redirect()->back()->with('error', 'You are already subscribed to this package.');
            }

            // Create a new subscription that starts after current one expires
            $newSubscription = new UserSubscription();
            $newSubscription->user_id = $user->id;
            $newSubscription->subscription_package_id = $newPackage->id;
            $newSubscription->starts_at = $activeSubscription->expires_at;
            
            // Calculate expiration based on new package's billing period (default to monthly)
            $billingPeriod = $newPackage->billing_period ?? 'monthly';
            $newSubscription->expires_at = $billingPeriod === 'yearly' 
                ? $newSubscription->starts_at->copy()->addYear() 
                : $newSubscription->starts_at->copy()->addMonth();
            
            $newSubscription->status = 'pending'; // Requires admin approval
            $newSubscription->payment_method = $activeSubscription->payment_method;
            $newSubscription->auto_renew = $activeSubscription->auto_renew;
            $newSubscription->notes = 'Package change scheduled for next billing cycle';
            $newSubscription->save();

            return redirect()->back()->with('success', 'Package change scheduled. Your new package will be activated after your current subscription expires on ' . $activeSubscription->expires_at->format('M d, Y') . '.');
        } else {
            // No active subscription - change immediately
            // Cancel any pending subscriptions
            $user->subscriptions()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            // Create new subscription starting now
            $newSubscription = new UserSubscription();
            $newSubscription->user_id = $user->id;
            $newSubscription->subscription_package_id = $newPackage->id;
            $newSubscription->starts_at = now();
            
            // Calculate expiration based on new package's billing period (default to monthly)
            $billingPeriod = $newPackage->billing_period ?? 'monthly';
            $newSubscription->expires_at = $billingPeriod === 'yearly' 
                ? now()->addYear() 
                : now()->addMonth();
            $newSubscription->status = 'pending'; // Requires admin approval
            $newSubscription->payment_method = 'manual';
            $newSubscription->auto_renew = false;
            $newSubscription->notes = 'New subscription - immediate activation';
            $newSubscription->save();

            return redirect()->back()->with('success', 'Subscription request submitted. Your subscription will be activated after admin approval.');
        }
    }
}

