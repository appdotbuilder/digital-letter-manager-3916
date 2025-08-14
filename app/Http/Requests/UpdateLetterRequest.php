<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLetterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_address' => 'nullable|string',
            'template_id' => 'nullable|exists:letter_templates,id',
            'assigned_reviewer' => 'nullable|exists:users,id',
            'assigned_signer' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Letter title is required.',
            'title.max' => 'Letter title cannot exceed 255 characters.',
            'content.required' => 'Letter content is required.',
            'template_id.exists' => 'Selected template does not exist.',
            'assigned_reviewer.exists' => 'Selected reviewer does not exist.',
            'assigned_signer.exists' => 'Selected signer does not exist.',
        ];
    }
}