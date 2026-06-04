<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartInterviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handled by standard auth middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'total_questions' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'Selected category is invalid.',
            'difficulty.in' => 'Difficulty level must be Easy, Medium, or Hard.',
            'total_questions.max' => 'You can request up to 100 questions at a time.',
        ];
    }
}
