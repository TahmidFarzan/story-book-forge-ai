<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookGenerate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'language_id'           => [
                'required',
                'integer',
                'exists:languages,id',
            ],

            'genre_ids'             => [
                'required',
                'array',
            ],

            'genre_ids.*'           => [
                'integer',
                'exists:genres,id',
            ],

            'story_book_type_id'    => [
                'required',
                'integer',
                'exists:story_book_types,id',
            ],

            'audience_id'           => [
                'required',
                'integer',
                'exists:audiences,id',
            ],

            'illustration_type_id'  => [
                'required',
                'integer',
                'exists:illustration_types,id',
            ],

            'ai_brain_text_id'      => [
                'required',
                'integer',
                'exists:ai_brains,id',
            ],

            'additional_information' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'language_id.required'              => 'Please select a language.',
            'language_id.exists'                => 'Selected language does not exist.',

            'genre_ids.required'                => 'Please select at least one genre.',
            'genre_ids.array'                   => 'Genres must be selected as an array.',
            'genre_ids.*.exists'                => 'Selected genre does not exist.',

            'story_book_type_id.required'       => 'Please select a story book type.',
            'story_book_type_id.exists'         => 'Selected story book type does not exist.',

            'audience_id.required'              => 'Please select an audience.',
            'audience_id.exists'                => 'Selected audience does not exist.',

            'illustration_type_id.required'     => 'Please select an illustration type.',
            'illustration_type_id.exists'       => 'Selected illustration type does not exist.',

            'ai_brain_text_id.required'         => 'Please select a text AI brain.',
            'ai_brain_text_id.exists'           => 'Selected text AI brain does not exist.',
        ];
    }
}
