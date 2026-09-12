<?php
namespace App\Http\Requests;

use App\Models\AiPrompt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aiPrompt = $this->route('slug') ? AiPrompt::where('slug', $this->route('slug'))->first() : null;

        return [
            'prompt' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'The name field is required.',
            'name.string'    => 'The name must be a string.',
            'name.max'       => 'The name may not be greater than 255 characters.',
            'name.unique'    => 'The name has already been taken.',
            'prompt.required' => 'The prompt field is required.',
            'prompt.string'  => 'The prompt must be a string.',
        ];
    }
}
