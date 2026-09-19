<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookStep14_2IllustrationPlanningRequest extends FormRequest
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

            'illustration_type_id' => [
                'required',
                'exists:illustration_types,id',
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
            'illustration_type_id.required' => 'Please select an illustration type.',
            'illustration_type_id.exists' => 'Selected illustration type does not exist.',
            'ai_brain_id.required' => 'Please select an ai brain.',
            'ai_brain_id.exists' => 'Selected ai brain does not exist.',
        ];
    }
}