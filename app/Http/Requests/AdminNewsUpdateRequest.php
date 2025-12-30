<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNewsUpdateRequest extends FormRequest
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
            'category' => ['required_without:subcategory', 'nullable', 'integer', 'exists:categories,id'],
            'subcategory' => ['nullable', 'integer', 'exists:categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:authors,id'],
            'image' => ['nullable'], // Can be file upload or path from media library
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
            
            // If image field is provided but not a file, it should be a valid string path
            // Empty is allowed (keeping existing image)
            if (!$hasFile && $hasPath) {
                // Path from media library - just ensure it's a valid string
                // No need to validate file existence as it might be from media library DB
            }
        });
    }
}

