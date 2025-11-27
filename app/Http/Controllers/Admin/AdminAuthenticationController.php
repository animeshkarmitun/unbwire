<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminResetPasswordRequest;
use App\Http\Requests\HandleLoginRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Mail\AdminSendResetLinkMail;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminAuthenticationController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function handleLogin(HandleLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Logout any existing user session to prevent session conflicts
        // This ensures admin and user sessions don't interfere with each other
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Regenerate session to prevent session fixation attacks
        // This also regenerates the CSRF token automatically
        $request->session()->regenerate(true);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function forgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(SendResetLinkRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();
        
        if (!$admin) {
            return redirect()->back()->with('error', __('admin.Email not found!'));
        }

        $token = \Str::random(64);
        $admin->remember_token = $token;
        $admin->save();

        Mail::to($request->email)->send(new AdminSendResetLinkMail($token, $request->email));

        return redirect()->back()->with('success', __('admin.A mail has been sent to your email address please check!'));
    }

    public function resetPassword($token)
    {
        return view('admin.auth.reset-password', compact('token'));
    }

    public function handleResetPassword(AdminResetPasswordRequest $request)
    {
        $admin = Admin::where(['email' => $request->email, 'remember_token' => $request->token])->first();

        if(!$admin){
            return back()->with('error', __('admin.token is invalid'));
        }

        $admin->password = bcrypt($request->password);
        $admin->remember_token = null;
        $admin->save();

        return redirect()->route('admin.login')->with('success', __('admin.Password reset successfull'));

    }
}
