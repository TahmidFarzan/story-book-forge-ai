<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoryBookTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:200', Rule::unique('story_book_types')->ignore($this->route('slug'), 'slug')],
            'brief' => ['nullable'],
            'prompt_instruction' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 200 characters.',
            'prompt_instruction.required' => 'The prompt instruction field is required.',
        ];
    }
}
