<?php

namespace App\Models\Notificaciones;

use App\Models\Empresas\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionSistema extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_sistema';

    protected $fillable = [
        'evento',
        'tipo',
        'severidad',
        'titulo',
        'mensaje',
        'icono',
        'destino_modulo',
        'destino_id',
        'hash_evento',
        'empresa_id',
        'usuario_actor_id',
        'destino_payload',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'destino_payload' => 'array',
            'metadata' => 'array',
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'usuario_actor_id');
    }

    public function lecturas()
    {
        return $this->hasMany(NotificacionSistemaLectura::class, 'notificacion_id');
    }
}
