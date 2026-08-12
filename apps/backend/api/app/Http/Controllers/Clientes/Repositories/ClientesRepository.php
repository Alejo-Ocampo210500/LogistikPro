<?php

namespace App\Http\Controllers\Clientes\Repositories;

use App\Models\Clientes\Cliente;

class ClientesRepository
{
    public function obtenerClientesCliente(int $empresaId)
    {
        return Cliente::where('empresa_id', $empresaId)
            ->orderByDesc('id')
            ->get();
    }

    public function obtenerClienteClientePorId(int $clienteId, int $empresaId): ?Cliente
    {
        return Cliente::query()
            ->where('id', $clienteId)
            ->where('empresa_id', $empresaId)
            ->first();
    }

    public function crearClienteCliente(array $payload)
    {
        return Cliente::query()->create([
            'empresa_id' => $payload['empresa_id'],
            'tipo_persona' => $payload['tipo_persona'],
            'tipo_documento_id' => $payload['tipo_documento_id'],
            'numero_documento' => $payload['numero_documento'],
            'nombre' => $payload['nombre'],
            'apellido' => $payload['apellido'],
            'razon_social' => $payload['razon_social'] ?? null,
            'nombre_comercial' => $payload['nombre_comercial'] ?? null,
            'email' => $payload['email'],
            'celular' => $payload['celular'],
            'telefono' => $payload['telefono'] ?? null,
            'direccion' => $payload['direccion'] ?? null,
            'pais_id' => $payload['pais_id'],
            'departamento_id' => $payload['departamento_id'],
            'ciudad_id' => $payload['ciudad_id'],
            'fecha_nacimiento' => $payload['fecha_nacimiento'] ?? null,
            'genero' => $payload['genero'] ?? null,
            'limite_credito' => $payload['limite_credito'] ?? null,
            'saldo_credito' => $payload['saldo_credito'] ?? null,
            'dias_credito' => $payload['dias_credito'] ?? null,
            'estado_id' => $payload['estado_id'],
            'creado_por' => $payload['creado_por'],
            'actualizado_por' => $payload['actualizado_por'],
        ]);
    }

    public function actualizarClienteCliente(int $clienteId, int $empresaId, array $payload): ?Cliente
    {
        $cliente = $this->obtenerClienteClientePorId($clienteId, $empresaId);

        if (!$cliente) {
            return null;
        }

        $cliente->update([
            'tipo_persona' => $payload['tipo_persona'],
            'tipo_documento_id' => $payload['tipo_documento_id'],
            'numero_documento' => $payload['numero_documento'],
            'nombre' => $payload['nombre'],
            'apellido' => $payload['apellido'],
            'razon_social' => $payload['razon_social'] ?? null,
            'nombre_comercial' => $payload['nombre_comercial'] ?? null,
            'email' => $payload['email'],
            'celular' => $payload['celular'],
            'telefono' => $payload['telefono'] ?? null,
            'direccion' => $payload['direccion'] ?? null,
            'pais_id' => $payload['pais_id'],
            'departamento_id' => $payload['departamento_id'],
            'ciudad_id' => $payload['ciudad_id'],
            'fecha_nacimiento' => $payload['fecha_nacimiento'] ?? null,
            'genero' => $payload['genero'] ?? null,
            'limite_credito' => $payload['limite_credito'] ?? null,
            'saldo_credito' => $payload['saldo_credito'] ?? null,
            'dias_credito' => $payload['dias_credito'] ?? null,
            'estado_id' => $payload['estado_id'],
            'actualizado_por' => $payload['actualizado_por'],
        ]);

        return $cliente->fresh();
    }

    public function cambiarEstadoClienteCliente(int $clienteId, int $empresaId, int $estadoId, int $actualizadoPor): ?Cliente
    {
        $cliente = $this->obtenerClienteClientePorId($clienteId, $empresaId);

        if (!$cliente) {
            return null;
        }

        $cliente->update([
            'estado_id' => $estadoId,
            'actualizado_por' => $actualizadoPor,
        ]);

        return $cliente->fresh();
    }
}
