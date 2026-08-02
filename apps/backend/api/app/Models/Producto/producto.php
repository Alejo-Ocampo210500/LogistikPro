<?php

namespace App\Models\Producto;

use App\Models\Categorias\categoria;
use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\Impuesto\impuesto;
use App\Models\Marcas\marca;
use App\Models\UnidadMedida\UnidadMedida;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class producto extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'productos';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'codigo_barras',
        'nombre',
        'descripcion',
        'categoria_id',
        'marca_id',
        'unidad_medida_id',
        'impuesto_id',
        'costo',
        'precio_venta',
        'stock',
        'stock_minimo',
        'stock_maximo',
        'maneja_inventario',
        'permite_descuento',
        'es_servicio',
        'venta_libre',
        'estado_id',
        'creado_por',
        'actualizado_por'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function categoria()
    {
        return $this->belongsTo(categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(marca::class, 'marca_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function impuesto()
    {
        return $this->belongsTo(impuesto::class, 'impuesto_id');
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
}
