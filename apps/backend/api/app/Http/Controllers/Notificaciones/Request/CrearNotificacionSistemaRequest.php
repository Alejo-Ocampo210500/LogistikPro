<?php

namespace App\Http\Controllers\Notificaciones\Request;

use Illuminate\Foundation\Http\FormRequest;

class CrearNotificacionSistemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evento' => ['required', 'string', 'max:80'],
            'tipo' => ['nullable', 'string', 'max:40'],
            'severidad' => ['nullable', 'string', 'max:30'],
            'titulo' => ['required', 'string', 'max:160'],
            'mensaje' => ['required', 'string'],
            'icono' => ['nullable', 'string', 'max:80'],
            'empresa_id' => ['nullable', 'exists:empresas,id'],
            'usuario_actor_id' => ['nullable', 'exists:users,id'],
            'destino_modulo' => ['nullable', 'string', 'max:120'],
            'destino_id' => ['nullable', 'string', 'max:120'],
            'destino_payload' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
            'hash_evento' => ['nullable', 'string', 'max:191'],
        ];
    }
}
