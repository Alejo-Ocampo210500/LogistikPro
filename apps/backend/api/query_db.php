<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$suscripciones = DB::table('suscripciones')->get();
foreach ($suscripciones as $s) {
    echo "ID: {$s->id}, Empresa: {$s->empresa_id}, Plan: {$s->plan_id}, Estado: {$s->estado_id}, Renovacion: " . json_encode($s->renovacion) . "\n";
}

$pagos = DB::table('pagos')->get();
echo "\nTotal pagos: " . count($pagos) . "\n";
foreach ($pagos as $p) {
    echo "Pago ID: {$p->id}, Suscripcion: {$p->suscripcion_id}, Valor: {$p->valor}, Estado: {$p->estado_pago_id}\n";
}
