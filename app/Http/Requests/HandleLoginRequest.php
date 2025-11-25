<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HandleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $remember = $this->boolean('remember');

        // First, check if admin exists and is active
        $admin = \App\Models\Admin::where('email', $this->email)->first();

        if (!$admin) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Check if admin is active (status = 1)
        if (!$admin->status) {
            throw ValidationException::withMessages([
                'email' => __('admin.Your account is inactive. Please contact administrator.'),
            ]);
        }

        // Attempt authentication
        if (! Auth::guard('admin')->attempt($this->only('email', 'password'), $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->session()->regenerate();
    }
}


