<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AudienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:200', Rule::unique('audiences')->ignore($this->route('slug'), 'slug')],
            'brief' => ['nullable'],
            'prompt_instruction' => ['nullable', 'string'],
            'genre_ids' => ['nullable', 'array'],
            'genre_ids.*' => ['integer', 'distinct', Rule::exists('genres', 'id')],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 200 characters.',
        ];
    }
}
