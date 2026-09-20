<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookStep15IllustrationPlanningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
            'illustration_type_id.required' => 'Please select an illustration type.',
            'illustration_type_id.exists' => 'Selected illustration type does not exist.',
            'ai_brain_id.required' => 'Please select an ai brain.',
            'ai_brain_id.exists' => 'Selected ai brain does not exist.',
        ];
    }
}