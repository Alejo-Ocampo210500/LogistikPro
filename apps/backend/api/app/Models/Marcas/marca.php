<?php

namespace App\Models\Marcas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Marca extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'estado_id',
        'empresa_id'
    ];
}
