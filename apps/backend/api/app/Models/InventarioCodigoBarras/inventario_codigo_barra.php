<?php

namespace App\Models\InventarioCodigoBarras;

use App\Models\Empresas\Empresa;
use App\Models\Producto\producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class InventarioCodigoBarra extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'inventario_codigos_barras';

    protected $fillable = [
        'empresa_id',
        'producto_id',
        'codigo_barras',
        'tipo_codigo_barras',
        'es_principal',
        'estado',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function producto()
    {
        return $this->belongsTo(producto::class, 'producto_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
