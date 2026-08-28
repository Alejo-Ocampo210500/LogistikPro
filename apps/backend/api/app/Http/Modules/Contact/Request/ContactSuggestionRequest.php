<?php

namespace App\Http\Modules\Contact\Request;

use Illuminate\Foundation\Http\FormRequest;

class ContactSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'topic' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'acceptPolicy' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'acceptPolicy.accepted' => 'Debes aceptar el tratamiento de datos para continuar.',
        ];
    }
}
