<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLanguageStoreRequest extends FormRequest
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

        return [
            'lang' => [
                'required',
                'string',
                Rule::in($availableLanguages),
                'unique:languages,lang'
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'default' => ['required', 'in:0,1'],
            'status' => ['required', 'in:0,1'],
        ];
    }
}


