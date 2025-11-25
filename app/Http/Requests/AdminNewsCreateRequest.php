<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNewsCreateRequest extends FormRequest
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
            'category' => ['required', 'integer', 'exists:categories,id'],
            'image' => ['required'], // Can be file upload or path from media library
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'in:0,1'],
            'is_breaking_news' => ['nullable', 'in:0,1'],
            'show_at_slider' => ['nullable', 'in:0,1'],
            'show_at_popular' => ['nullable', 'in:0,1'],
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->sometimes('image', 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', function ($input) {
            return $input->hasFile('image');
        });
        
        $validator->sometimes('image', 'string|max:500', function ($input) {
            return !$input->hasFile('image');
        });
    }
}

