<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoryBookCharactersRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'character_additional_information' => [
                'nullable',
                'string',
            ],

            'plot' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'plot.required'  => 'Plot is required.',
            'character_additional_information.string'  => 'Character additional information must be a string.',
        ];
    }
}
