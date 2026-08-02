<?php

namespace App\Models\Categorias;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Producto\producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class categoria extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'categorias';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'estado_id',
        'creado_por',
        'actualizado_por',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function productos()
    {
        return $this->hasMany(producto::class, 'categoria_id');
    }
}
