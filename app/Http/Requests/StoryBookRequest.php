<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_18_plus'             => [
                'nullable',
                'boolean',
            ],

            'enable_mature_content'  => [
                'nullable',
                'boolean',
            ],

            'additional_information' => [
                'nullable',
                'string',
            ],

            'novel_continuity'       => [
                'required',
                'string',
            ],
            'language_id'            => [
                'required',
                'string',
            ],

            'genre_ids'              => [
                'required',
                'array',
            ],

            'genre_ids.*'            => [
                'integer',
                'exists:genres,id',
            ],

            'novel_type_id'         => [
                'required',
                'integer',
                'exists:novel_types,id',
            ],

            'audience_id'           => [
                'required',
                'integer',
                'exists:audiences,id',
            ],

            'ai_brain_id'            => [
                'required',
                'integer',
                'exists:ai_brains,id',
            ],
        ];
    }

    public function messages()
    {
        return [


            'novel_continuity.required'      => 'Please select a novel continuity.',
            'language_id.required'           => 'Please select a language.',

            'genre_ids.required'             => 'Genres must be required.',
            'genre_ids.array'                => 'Genres must be selected as an array.',

            'genre_ids.*.exists'             => 'Selected genre does not exist.',

            'novel_type_id.required'        => 'Novel types must be required.',
            'novel_type_id.exists'        => 'Selected novel type does not exist.',

            'audience_id.exists'          => 'Selected audience does not exist.',

            'ai_brain_id.required'           => 'Please select a ai brain.',
            'ai_brain_id.exists'             => 'Selected ai brain does not exist.',
        ];
    }

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {
            $aVData = $validator->getData();

            if (isset($aVData['enable_mature_content']) && ($aVData['enable_mature_content'] == true)) {

                if (! isset($aVData['is_18_plus']) || (isset($aVData['is_18_plus']) && ($aVData['is_18_plus'] == false))) {

                    $validator->errors()->add(
                        'enable_mature_content',
                        'Enable mature content can not true as is 18+ is false'
                    );
                }
            }
        });
    }
}
