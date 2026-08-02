<?php

namespace App\Models\Pagos;

use Illuminate\Database\Eloquent\Model;

class EstadoPago extends Model
{
    protected $table = 'estados_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
