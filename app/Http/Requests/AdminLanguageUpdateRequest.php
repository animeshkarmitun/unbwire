<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLanguageUpdateRequest extends FormRequest
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
        $availableLanguages = array_keys(config('language', []));
        $languageId = $this->route('language'); // Get the language ID from the route parameter

        return [
            'lang' => [
                'required',
                'string',
                Rule::in($availableLanguages),
                Rule::unique('languages', 'lang')->ignore($languageId)
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'default' => ['required', 'in:0,1'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}

