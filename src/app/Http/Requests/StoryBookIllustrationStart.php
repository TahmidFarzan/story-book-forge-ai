<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookIllustrationStart extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ai_brain_illustration_id' => [
                'required',
                'integer',
                'exists:ai_brains,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ai_brain_illustration_id.required' => 'Please select an illustration AI brain.',
            'ai_brain_illustration_id.exists'   => 'Selected illustration AI brain does not exist.',
        ];
    }
}
