<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For example, you may allow only admin users to store new questions
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string|max:255',
            'category_id' => 'required|exists:question_categories,id',
            'tip_0' => 'nullable|string',
            'tip_1' => 'nullable|string',
            'tip_2' => 'nullable|string',
            'tip_3' => 'nullable|string',
            'tip_4' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validation errors (optional).
     */
    public function messages()
    {
        return [
            'text.required' => 'The question text is required.',
            'category_id.required' => 'Please select a category for the question.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
