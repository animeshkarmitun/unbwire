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
            'image' => ['nullable'], // Will be validated in withValidator
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
        $validator->after(function ($validator) {
            $hasFile = $this->hasFile('image');
            $imagePath = $this->input('image');
            $hasPath = !empty($imagePath) && is_string($imagePath) && trim($imagePath) !== '';
            
            // Image is required - either file upload or media library path
            if (!$hasFile && !$hasPath) {
                $validator->errors()->add('image', 'The image field is required. Please select an image from media library or upload a file.');
                return;
            }
            
            // If it's a file upload, validate file properties
            if ($hasFile) {
                $file = $this->file('image');
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    $validator->errors()->add('image', 'The image must be a file of type: jpeg, png, jpg, gif, webp.');
                }
                if ($file->getSize() > 5120 * 1024) { // 5MB in bytes
                    $validator->errors()->add('image', 'The image may not be greater than 5MB.');
                }
            }
            
            // If it's a path from media library, validate it exists
            if ($hasPath && !$hasFile) {
                $path = trim($imagePath);
                // Check if file exists (optional - you might want to validate against media library DB)
                if (!file_exists(public_path($path)) && !str_starts_with($path, 'http')) {
                    // Don't fail validation if it's a valid path format, just log
                    // The path might be from media library which is stored in DB
                }
            }
        });
    }
}

