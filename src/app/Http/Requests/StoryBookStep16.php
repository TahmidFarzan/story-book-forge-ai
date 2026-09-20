<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookStep16 extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_no' => [
                'required',
                'integer',
                'min:1',
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
            'page_no.required' => 'Page number is required.',
            'page_no.integer' => 'Page number must be a number.',
            'page_no.min' => 'Page number must be at least 1.',
            'ai_brain_id.required' => 'Please select an ai brain.',
            'ai_brain_id.exists' => 'Selected ai brain does not exist.',
        ];
    }
}