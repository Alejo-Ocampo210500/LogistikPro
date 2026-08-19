<?php

namespace App\Models\Notificaciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionSistemaLectura extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_sistema_lecturas';

    protected $fillable = [
        'notificacion_id',
        'user_id',
        'leida_at',
    ];

    protected function casts(): array
    {
        return [
            'leida_at' => 'datetime',
        ];
    }

    public function notificacion()
    {
        return $this->belongsTo(NotificacionSistema::class, 'notificacion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
