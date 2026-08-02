<?php

namespace App\Http\Controllers\Provedores\Repositories;

use App\Models\Provedores\provedor as ProvedorModel;
use Illuminate\Support\Facades\Auth;

class ProvedoresRepository
{
    public function obtenerProvedoresCliente(int $empresaId)
    {
        return ProvedorModel::query()
            ->where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function crearProvedorCliente(array $data): ProvedorModel
    {
        return ProvedorModel::create([
            'empresa_id' => $data['empresa_id'],
            'tipo_documento_id' => $data['tipo_documento_id'],
            'numero_documento' => $data['numero_documento'],
            'codigo_verificacion' => $data['codigo_verificacion'] ?? null,
            'razon_social' => $data['razon_social'],
            'nombre_comercial' => $data['nombre_comercial'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'] ?? null,
            'email' => $data['email'] ?? null,
            'sitio_web' => $data['sitio_web'] ?? null,
            'pais_id' => $data['pais_id'],
            'departamento_id' => $data['departamento_id'],
            'ciudad_id' => $data['ciudad_id'],
            'codigo_postal' => $data['codigo_postal'] ?? null,
            'cupo_credito' => $data['cupo_credito'] ?? 0,
            'estado_id' => $data['estado_id'],
            'dias_credito' => $data['dias_credito'] ?? 0,
            'observaciones' => $data['observaciones'] ?? null,
            'creado_por' => Auth::id(),
            'actualizado_por' => Auth::id(),
        ]);
    }

    public function actualizarProvedorCliente(array $data): ProvedorModel
    {
        $provedor = ProvedorModel::query()
            ->where('empresa_id', $data['empresa_id'])
            ->where('id', $data['id'])
            ->firstOrFail();

        $provedor->update([
            'tipo_documento_id' => $data['tipo_documento_id'],
            'numero_documento' => $data['numero_documento'],
            'codigo_verificacion' => $data['codigo_verificacion'] ?? null,
            'razon_social' => $data['razon_social'],
            'nombre_comercial' => $data['nombre_comercial'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'] ?? null,
            'email' => $data['email'] ?? null,
            'sitio_web' => $data['sitio_web'] ?? null,
            'pais_id' => $data['pais_id'],
            'departamento_id' => $data['departamento_id'],
            'ciudad_id' => $data['ciudad_id'],
            'codigo_postal' => $data['codigo_postal'] ?? null,
            'cupo_credito' => $data['cupo_credito'] ?? 0,
            'estado_id' => $data['estado_id'],
            'dias_credito' => $data['dias_credito'] ?? 0,
            'observaciones' => $data['observaciones'] ?? null,
            'actualizado_por' => Auth::id(),
        ]);

        return $provedor;
    }

    public function cambiarEstadoProvedorCliente(int $provedorId, int $estadoId, int $empresaId): ProvedorModel
    {
        $provedor = ProvedorModel::query()
            ->where('empresa_id', $empresaId)
            ->where('id', $provedorId)
            ->firstOrFail();

        $provedor->estado_id = $estadoId;
        $provedor->actualizado_por = Auth::id();
        $provedor->save();

        return $provedor;
    }
}
