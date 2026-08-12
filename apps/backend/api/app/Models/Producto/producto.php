<?php

namespace App\Models\Producto;

use App\Models\Empresas\Empresa;
use App\Models\Estados\Estado;
use App\Models\InventarioCodigoBarras\InventarioCodigoBarra;
use App\Models\UnidadMedida\UnidadMedida;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'imagen',
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

    protected $appends = [
        'imagen_url',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categorias\categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(\App\Models\Marcas\marca::class, 'marca_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function impuesto()
    {
        return $this->belongsTo(\App\Models\Impuesto\impuesto::class, 'impuesto_id');
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

    public function inventarioCodigosBarras()
    {
        return $this->hasMany(InventarioCodigoBarra::class, 'producto_id');
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) {
            return null;
        }

        if (str_starts_with($this->imagen, 'http://') || str_starts_with($this->imagen, 'https://')) {
            return $this->imagen;
        }

        return url(Storage::url($this->imagen));
    }
}
