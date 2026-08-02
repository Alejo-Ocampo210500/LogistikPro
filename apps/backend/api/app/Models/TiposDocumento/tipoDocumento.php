<?php

namespace App\Models\TiposDocumento;

use App\Models\Estados\Estado;
use App\Models\Provedores\provedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class tipoDocumento extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'tipos_documentos';

    protected $fillable = [
        'nombre',
        'abreviatura',
        'descripcion',
        'estado_id',
        'creado_por',
        'actualizado_por',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function proveedores()
    {
        return $this->hasMany(provedor::class, 'tipo_documento_id');
    }
}
