<?php

namespace App\Models\Empresas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Empresa extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'empresas';

    protected $fillable = [
        'numero_documento',
        'digito_verificacion',
        'nombre_comercial',
        'logo',
        'telefono',
        'email',
        'sitio_web',
        'direccion',
        'codigo_postal',
        'created_by',
        'updated_by'
    ];
}
