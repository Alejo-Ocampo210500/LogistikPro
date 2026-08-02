<?php

namespace App\Models\Planes;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Suscripcion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Plan extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'planes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion_meses',
        'estado_id',
        'descuento',
    ];

    protected function casts(): array
    {
        return [
            'duracion_meses' => 'integer',
        ];
    }

    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class);
    }
}
