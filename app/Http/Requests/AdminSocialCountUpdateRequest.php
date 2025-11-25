<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSocialCountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'language' => ['required', 'string'],
            'icon' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'fan_count' => ['required', 'string', 'max:50'],
            'fan_type' => ['required', 'string', 'max:50'],
            'button_text' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}

