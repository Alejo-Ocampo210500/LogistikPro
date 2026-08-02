<?php

namespace App\Models\Pagos;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

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
