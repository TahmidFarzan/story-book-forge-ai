<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookStep9StoryStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'additional_information' => [
                'nullable',
                'string',
            ],

            'ai_brain_id' => [
                'required',
                'exists:ai_brains,id',
            ],
        ];
    }

    public function messages()
    {
        return [
            'additional_information.string' => 'Additional information must be a string.',
            'ai_brain_id.required' => 'Please select an ai brain.',
            'ai_brain_id.exists' => 'Selected ai brain does not exist.',
        ];
    }
}
