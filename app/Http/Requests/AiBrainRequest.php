<?php
namespace App\Http\Requests;

use App\Models\AiBrain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiBrainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aiBrain = $this->route('slug') ? AiBrain::where('slug', $this->route('slug'))->first() : null;

        return [
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique('ai_brains', 'name')->ignore($aiBrain?->id),
            ],
            'model'             => [
                'required',
                'string',
                'max:500',
                Rule::unique('ai_brains', 'model')->ignore($aiBrain?->id)
            ],
            'api_url'           => ['required', 'string', 'max:500'],
            'api_key'           => ['required', 'string', 'max:500'],
            'brief'             => ['nullable', 'string'],
            'focus'             => ['nullable', 'string'],
            'context_window'    => ['nullable', 'integer', 'min:1'],
            'average_latency'   => ['required', 'numeric', 'min:0'],
            'minimum_wait_time' => ['required', 'integer', 'min:0'],
            'timeout_seconds'   => ['required', 'integer', 'min:1'],
            'max_output_tokens' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'The name field is required.',
            'name.string'                => 'The name must be a string.',
            'name.max'                   => 'The name may not be greater than 255 characters.',
            'name.unique'                => 'The name has already been taken.',
            'model.required'             => 'The model field is required.',
            'model.string'               => 'The model must be a string.',
            'api_url.required'           => 'The API URL field is required.',
            'api_url.string'             => 'The API URL must be a string.',
            'api_key.required'           => 'The API key field is required.',
            'api_key.string'             => 'The API key must be a string.',
            'context_window.integer'     => 'The context window must be an integer.',
            'context_window.min'         => 'The context window must be at least 1.',
            'average_latency.required'   => 'The average latency field is required.',
            'average_latency.numeric'    => 'The average latency must be a number.',
            'average_latency.min'        => 'The average latency must be at least 0.',
            'minimum_wait_time.required' => 'The minimum wait time field is required.',
            'minimum_wait_time.integer'  => 'The minimum wait time must be an integer.',
            'minimum_wait_time.min'      => 'The minimum wait time must be at least 0.',
            'timeout_seconds.required'   => 'The timeout seconds field is required.',
            'timeout_seconds.integer'    => 'The timeout seconds must be an integer.',
            'timeout_seconds.min'        => 'The timeout seconds must be at least 1.',
            'max_output_tokens.integer'  => 'The max output tokens must be an integer.',
            'max_output_tokens.min'      => 'The max output tokens must be at least 1.',
        ];
    }
}
