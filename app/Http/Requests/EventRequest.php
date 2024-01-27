<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'required|image|max:2048',
        ];

        // Apply the 'nullable' rule to 'image' when updating a record
        if ($this->isMethod('put')) {
            $rules['image'] = 'nullable|image|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The Title is required.',
            'description.required' => 'The Description is required.',

            'start_date.required' => 'The Start date is required.',
            'start_date.date' => 'The Start date must be in valid format.',

            'end_date.required' => 'The End date is required.',
            'end_date.date' => 'The End date must be in valid format.',

            'image.required' => 'The Image is required',
            'image.max' => 'The Image size should not exceed than 2MB',
        ];
    }
}
