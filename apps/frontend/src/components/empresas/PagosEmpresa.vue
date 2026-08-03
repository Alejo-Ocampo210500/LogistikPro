<template>
    <section class="table-card pagos-card">
        <div class="table-head">

            <div>
                <span class="table-kicker">
                    Pagos Registrados
                </span>

                <h2>
                    Historial de pagos
                </h2>
            </div>


            <div class="header-actions">

                <v-btn class="gold-button" rounded dark @click="abrirPagoManual" data-action-loader="true">
                    <v-icon left>
                        mdi-cash-plus
                    </v-icon>

                    Registrar pago manual
                </v-btn>


                <span class="table-count">
                    {{ pagos.length }} registros
                </span>

            </div>

        </div>
        <div class="stats-grid">
            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <i class="mdi mdi-cash-multiple"></i>
                </div>

                <div class="stat-info">
                    <span>Ingresos acumulados</span>
                    <h3>{{ totalIngresos }}</h3>
                </div>
            </div>

            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <i class="mdi mdi-file-document-outline"></i>
                </div>

                <div class="stat-info">
                    <span>Pagos aprobados</span>
                    <h3>{{ pagosAprobados }}</h3>
                </div>
            </div>

            <div class="stat-card stat-yellow">
                <div class="stat-icon">
                    <i class="mdi mdi-refresh-circle"></i>
                </div>

                <div class="stat-info">
                    <span>Renovaciones</span>
                    <h3>{{ renovaciones }}</h3>
                </div>
            </div>

            <div class="stat-card stat-purple">
                <div class="stat-icon">
                    <i class="mdi mdi-domain"></i>
                </div>

                <div class="stat-info">
                    <span>Empresas con pagos</span>
                    <h3>{{ empresasConPagos }}</h3>
                </div>
            </div>
        </div>
        <div class="table-wrap">
            <v-data-table dense :headers="headers" :items="pagosProcesados" class="elevation-1 pagos-table">

                <!--ITEM EMPRESA-->
                <template v-slot:[`item.empresa_nombre`]="{ item }">
                    <span class="empresa-chip">
                        <i class="mdi mdi-domain"></i>
                        {{ item.empresa_nombre }}
                    </span>
                </template>

                <!--ITEM PLAN-->
                <template v-slot:[`item.plan_nombre`]="{ item }">
                    <span class="plan-chip">
                        <i class="mdi mdi-crown"></i>
                        {{ item.plan_nombre }}
                    </span>
                </template>

                <!--ITEM TIPO DE PAGO-->
                <template v-slot:[`item.tipo_pago`]="{ item }">
                    <span :class="['status-pill', tipoPagoClass(tipoPagoLabel(item))]">
                        <i :class="tipoPagoIcon(tipoPagoLabel(item))"></i>
                        {{ tipoPagoLabel(item) }}
                    </span>
                </template>

                <!--ITEM METODO DE PAGO-->
                <template v-slot:[`item.metodo_pago`]="{ item }">
                    <span class="metodo-chip">
                        <i class="mdi mdi-credit-card-outline"></i>
                        {{ item.metodo_pago }}
                    </span>
                </template>

                <!--ITEM VALOR-->
                <template v-slot:[`item.valor`]="{ item }">
                    <div class="precio-card">
                        <i class="mdi mdi-cash"></i>
                        {{ formatoCOP(item.valor) }}
                    </div>
                </template>

                <!--ITEM ESTADO DE PAGO-->
                <template v-slot:[`item.estado_pago`]="{ item }">

                    <span :class="['status-pill', item.estado_pago == 'Aprobado' ? 'status-ok' : item.estado_pago == 'Pendiente' ? 'status-warning'
                        : item.estado_pago == 'Rechazado' ? 'status-off' : 'status-cancelado']">
                        <i :class="[item.estado_pago == 'Aprobado' ? 'mdi mdi-check-circle' : item.estado_pago == 'Pendiente' ? 'mdi mdi-clock-outline'
                            : item.estado_pago == 'Rechazado'
                                ? 'mdi mdi-close-circle'
                                : 'mdi mdi-cancel']"></i>
                        {{ item.estado_pago }}
                    </span>
                </template>

                <!--ITEM FECHA DE INICIO-->
                <template v-slot:[`item.fecha_inicio`]="{ item }">
                    <div class="fecha-chip">
                        <i class="mdi mdi-calendar-start"></i>
                        {{ formatearFecha(item.fecha_inicio) }}
                    </div>
                </template>

                <!--ITEM FECHA DE VENCIMIENTO-->
                <template v-slot:[`item.fecha_vencimiento`]="{ item }">
                    <div class="fecha-chip">
                        <i class="mdi mdi-calendar-end"></i>
                        {{ formatearFecha(item.fecha_vencimiento) }}
                    </div>
                </template>

                <!--ITEM REFERENCIA-->
                <template v-slot:[`item.referencia`]="{ item }">
                    <span class="referencia-chip">
                        <i class="mdi mdi-barcode"></i>
                        {{ item.referencia }}
                    </span>
                </template>

                <template v-slot:[`item.acciones`]="{ item }">

                    <div class="acciones">

                        <div class="action-item">
                            <v-btn icon color="primary" @click="verDetalle(item)" data-action-loader="true">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            <span>Ver detalles</span>
                        </div>

                        <div class="action-item">
                            <v-btn icon color="warning" @click="gestionarPago(item)" data-action-loader="true">
                                <v-icon>mdi-cloud-arrow-down-outline</v-icon>
                            </v-btn>
                            <span>Descargar factura</span>
                        </div>

                        <div class="action-item">
                            <v-btn icon color="error" @click="gestionarPago(item)" data-action-loader="true">
                                <v-icon>mdi-email-arrow-right-outline</v-icon>
                            </v-btn>
                            <span>Enviar comprobante por Email</span>
                        </div>

                    </div>

                </template>
            </v-data-table>

            <!--DIALOG PARA VER DETALLE DE PAGO -->
            <v-dialog v-model="dialogDetalle" max-width="900" persistent>
                <v-card class="dialog-card">

                    <!-- HEADER -->
                    <v-card-title class="dialog-card-title">

                        <div class="dialog-header-left">

                            <div class="dialog-icon">

                                <v-icon color="white" size="28">
                                    mdi-file-document-check-outline
                                </v-icon>

                            </div>

                            <div>

                                <span class="dialog-kicker">
                                    Gestión de pagos
                                </span>

                                <h3 class="dialog-title">
                                    Detalle del pago de suscripción
                                </h3>

                                <div class="dialog-meta">

                                    <span class="meta-chip empresa">

                                        <v-icon small>
                                            mdi-domain
                                        </v-icon>

                                        {{ pagoSeleccionado.empresa_nombre }}

                                    </span>

                                <span class="meta-chip plan">

                                        <v-icon small>
                                            mdi-crown
                                        </v-icon>

                                        {{ pagoSeleccionado.plan_nombre }}

                                    </span>

                                    <span class="meta-chip payment-type">
                                        <v-icon small>
                                            mdi-swap-horizontal
                                        </v-icon>
                                        {{ tipoPagoLabel(pagoSeleccionado) }}
                                    </span>

                                </div>

                            </div>

                        </div>

                        <v-spacer></v-spacer>

                        <span :class="[
                            'status-pill',
                            pagoSeleccionado.estado_pago == 'Aprobado'
                                ? 'status-ok'
                                : pagoSeleccionado.estado_pago == 'Pendiente'
                                    ? 'status-warning'
                                    : pagoSeleccionado.estado_pago == 'Rechazado'
                                        ? 'status-off'
                                        : 'status-cancelado'
                        ]">

                            <v-icon small left>
                                {{
                                    pagoSeleccionado.estado_pago == 'Aprobado'
                                        ? 'mdi-check-circle'
                                        : pagoSeleccionado.estado_pago == 'Pendiente'
                                            ? 'mdi-clock-outline'
                                            : pagoSeleccionado.estado_pago == 'Rechazado'
                                                ? 'mdi-close-circle'
                                                : 'mdi-cancel'
                                }}
                            </v-icon>

                            {{ pagoSeleccionado.estado_pago }}

                        </span>

                        <v-btn icon class="ml-3" @click="dialogDetalle = false">

                            <v-icon>
                                mdi-close
                            </v-icon>

                        </v-btn>

                    </v-card-title>

                    <v-divider></v-divider>

                    <!-- RESUMEN -->
                    <div class="detalle-resumen">

                        <div class="summary-card success">

                            <span class="summary-label">
                                Valor pagado
                            </span>

                            <h2>
                                {{ formatoCOP(pagoSeleccionado.valor) }}
                            </h2>

                        </div>

                        <div class="summary-card">

                            <span class="summary-label">
                                Estado
                            </span>

                            <span :class="[
                                'status-pill',
                                pagoSeleccionado.estado_pago == 'Aprobado'
                                    ? 'status-ok'
                                    : pagoSeleccionado.estado_pago == 'Pendiente'
                                        ? 'status-warning'
                                        : pagoSeleccionado.estado_pago == 'Rechazado'
                                            ? 'status-off'
                                            : 'status-cancelado'
                            ]">

                                {{ pagoSeleccionado.estado_pago }}

                            </span>

                        </div>

                    </div>

                    <!-- DETALLE -->
                    <div class="detalle-grid">

                        <div class="info-card">
                            <label>Empresa</label>

                            <div class="info-value">
                                <v-icon small color="#2563eb">
                                    mdi-domain
                                </v-icon>

                                {{ pagoSeleccionado.empresa_nombre }}
                            </div>
                        </div>

                        <div class="info-card">
                            <label>Plan contratado</label>

                            <div class="info-value">
                                <v-icon small color="#7c3aed">
                                    mdi-crown
                                </v-icon>

                                {{ pagoSeleccionado.plan_nombre }}
                            </div>
                        </div>

                        <div class="info-card">
                            <label>Método de pago</label>

                            <div class="info-value">
                                <v-icon small>
                                    mdi-credit-card-outline
                                </v-icon>

                                {{ pagoSeleccionado.metodo_pago }}
                            </div>
                        </div>

                        <div class="info-card">
                            <label>Tipo de pago</label>

                            <div class="info-value">
                                <v-icon small>
                                    mdi-swap-horizontal
                                </v-icon>

                                {{ tipoPagoLabel(pagoSeleccionado) }}
                            </div>
                        </div>

                        <div class="info-card">
                            <label>Referencia</label>

                            <div class="codigo">
                                {{ pagoSeleccionado.referencia }}
                            </div>
                        </div>

                        <div class="info-card">
                            <label>Fecha inicio</label>

                            <div class="info-value">
                                <v-icon small>
                                    mdi-calendar-start
                                </v-icon>

                                {{ formatearFecha(pagoSeleccionado.fecha_inicio) }}
                            </div>
                        </div>

                        <div class="info-card">
                            <label>Fecha vencimiento</label>

                            <div class="info-value">
                                <v-icon small>
                                    mdi-calendar-end
                                </v-icon>

                                {{ formatearFecha(pagoSeleccionado.fecha_vencimiento) }}
                            </div>
                        </div>

                    </div>

                    <!-- HISTORIAL DE PAGOS DE LA EMPRESA -->
                    <div v-if="pagosEmpresa && pagosEmpresa.length > 1" class="historial-pagos-section">
                        <v-divider></v-divider>
                        <div class="section-title">
                            <v-icon color="#17304f" class="mr-2">mdi-history</v-icon>
                            Historial de pagos de la empresa ({{ pagosEmpresa.length }})
                        </div>
                        <div class="historial-list-container">
                            <table class="historial-table">
                                <thead>
                                    <tr>
                                        <th>Fecha Pago</th>
                                        <th>Plan</th>
                                        <th>Valor</th>
                                        <th>Estado</th>
                                        <th>Referencia</th>
                                        <th class="text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="p in pagosEmpresa" :key="p.id" :class="{ 'fila-seleccionada': p.id === pagoSeleccionado.id }">
                                        <td>{{ formatearFecha(p.fecha_pago) }}</td>
                                        <td>
                                            <span class="plan-chip-small">
                                                {{ p.plan_nombre }}
                                            </span>
                                        </td>
                                        <td><strong>{{ formatoCOP(p.valor) }}</strong></td>
                                        <td>
                                            <span :class="['status-pill-small', p.estado_pago == 'Aprobado' ? 'status-ok' : p.estado_pago == 'Pendiente' ? 'status-warning' : p.estado_pago == 'Rechazado' ? 'status-off' : 'status-cancelado']">
                                                {{ p.estado_pago }}
                                            </span>
                                        </td>
                                        <td><code>{{ p.referencia || '-' }}</code></td>
                                        <td class="text-right">
                        <v-btn small color="primary" outlined @click.stop="verDetalleDesdeHistorial(p)" data-action-loader="true">
                                                Ver detalle
                                            </v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <v-divider></v-divider>

                    <v-card-actions class="dialog-actions">

                        <button type="button" class="secondary-button" @click="dialogDetalle = false">

                            Cerrar

                        </button>

                    </v-card-actions>

                </v-card>
            </v-dialog>

            <v-dialog
                v-model="dialogHistorialDetalle"
                max-width="840"
                persistent
                content-class="historial-detalle-modal"
                :retain-focus="false"
            >
                <v-card class="dialog-card historial-dialog-card">
                    <div class="historial-head-band"></div>

                    <v-card-title class="dialog-card-title historial-title-wrap">
                        <div class="dialog-header-left">
                            <div class="dialog-icon historial-icon">
                                <v-icon color="white" size="28">
                                    mdi-file-document-search-outline
                                </v-icon>
                            </div>

                            <div>
                                <span class="dialog-kicker">
                                    Historial de pagos
                                </span>

                                <h3 class="dialog-title historial-title">
                                    Detalle del pago seleccionado
                                </h3>

                                <div class="dialog-meta historial-meta">
                                    <span class="meta-chip empresa">
                                        <v-icon small>
                                            mdi-domain
                                        </v-icon>
                                        {{ pagoHistorialSeleccionado.empresa_nombre }}
                                    </span>

                                    <span class="meta-chip plan">
                                        <v-icon small>
                                            mdi-crown
                                        </v-icon>
                                        {{ pagoHistorialSeleccionado.plan_nombre }}
                                    </span>

                                    <span class="meta-chip payment-type">
                                        <v-icon small>
                                            mdi-swap-horizontal
                                        </v-icon>
                                        {{ tipoPagoLabel(pagoHistorialSeleccionado) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="historial-head-actions">
                            <span :class="[
                                'status-pill',
                                pagoHistorialSeleccionado.estado_pago == 'Aprobado'
                                    ? 'status-ok'
                                    : pagoHistorialSeleccionado.estado_pago == 'Pendiente'
                                        ? 'status-warning'
                                        : pagoHistorialSeleccionado.estado_pago == 'Rechazado'
                                            ? 'status-off'
                                            : 'status-cancelado'
                            ]">
                                <v-icon small left>
                                    {{
                                        pagoHistorialSeleccionado.estado_pago == 'Aprobado'
                                            ? 'mdi-check-circle'
                                            : pagoHistorialSeleccionado.estado_pago == 'Pendiente'
                                                ? 'mdi-clock-outline'
                                                : pagoHistorialSeleccionado.estado_pago == 'Rechazado'
                                                    ? 'mdi-close-circle'
                                                    : 'mdi-cancel'
                                    }}
                                </v-icon>

                                {{ pagoHistorialSeleccionado.estado_pago }}
                            </span>

                            <v-btn icon class="ml-2" @click="dialogHistorialDetalle = false">
                                <v-icon>
                                    mdi-close
                                </v-icon>
                            </v-btn>
                        </div>
                    </v-card-title>

                    <div class="historial-kpi-strip">
                        <div class="historial-kpi-item kpi-valor">
                            <span class="historial-kpi-label">Valor pagado</span>
                            <strong>{{ formatoCOP(pagoHistorialSeleccionado.valor) }}</strong>
                            <small class="historial-kpi-sub">Registro confirmado en historial</small>
                        </div>

                        <div class="historial-kpi-item kpi-fecha">
                            <span class="historial-kpi-label">Fecha de pago</span>
                            <strong>{{ formatearFecha(pagoHistorialSeleccionado.fecha_pago) }}</strong>
                            <small class="historial-kpi-sub">Corte contable del movimiento</small>
                        </div>

                        <div class="historial-kpi-item kpi-metodo">
                            <span class="historial-kpi-label">Método</span>
                            <strong>{{ pagoHistorialSeleccionado.metodo_pago || '-' }}</strong>
                            <small class="historial-kpi-sub">Canal registrado para la transacción</small>
                        </div>
                    </div>

                    <div class="historial-body-grid">
                        <div class="historial-col principal">
                            <div class="historial-info-card">
                                <label>Empresa</label>
                                <div class="info-value">
                                    <v-icon small color="#2563eb">
                                        mdi-domain
                                    </v-icon>
                                    {{ pagoHistorialSeleccionado.empresa_nombre }}
                                </div>
                            </div>

                            <div class="historial-info-card">
                                <label>Plan contratado</label>
                                <div class="info-value">
                                    <v-icon small color="#7c3aed">
                                        mdi-crown
                                    </v-icon>
                                    {{ pagoHistorialSeleccionado.plan_nombre }}
                                </div>
                            </div>

                            <div class="historial-info-card historial-referencia-card">
                                <label>Referencia</label>
                                <div class="codigo historial-codigo">
                                    {{ pagoHistorialSeleccionado.referencia || '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="historial-col secundaria">
                            <div class="historial-info-card">
                                <label>Estado de pago</label>
                                <div>
                                    <span :class="[
                                        'status-pill',
                                        pagoHistorialSeleccionado.estado_pago == 'Aprobado'
                                            ? 'status-ok'
                                            : pagoHistorialSeleccionado.estado_pago == 'Pendiente'
                                                ? 'status-warning'
                                                : pagoHistorialSeleccionado.estado_pago == 'Rechazado'
                                                    ? 'status-off'
                                                    : 'status-cancelado'
                                    ]">
                                        {{ pagoHistorialSeleccionado.estado_pago }}
                                    </span>
                                </div>
                            </div>

                            <div class="historial-info-card">
                                <label>Tipo de pago</label>
                                <div class="info-value">
                                    <v-icon small>
                                        mdi-swap-horizontal
                                    </v-icon>
                                    {{ tipoPagoLabel(pagoHistorialSeleccionado) }}
                                </div>
                            </div>

                            <div class="historial-trace-card">
                                <div class="trace-title">
                                    <v-icon small color="#17304f" class="mr-1">mdi-timeline-clock-outline</v-icon>
                                    Trazabilidad
                                </div>

                                <div class="trace-row">
                                    <span>Inicio</span>
                                    <strong>{{ formatearFecha(pagoHistorialSeleccionado.fecha_inicio) }}</strong>
                                </div>

                                <div class="trace-row">
                                    <span>Vencimiento</span>
                                    <strong>{{ formatearFecha(pagoHistorialSeleccionado.fecha_vencimiento) }}</strong>
                                </div>

                                <div class="trace-row">
                                    <span>ID registro</span>
                                    <strong>#{{ pagoHistorialSeleccionado.id || '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <v-divider></v-divider>

                    <v-card-actions class="dialog-actions">
                        <button type="button" class="secondary-button" @click="dialogHistorialDetalle = false">
                            Cerrar
                        </button>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialogPagoManual" max-width="700" persistent>

                <v-card class="dialog-card">


                    <v-card-title class="dialog-card-title">

                        <div class="dialog-header-left">

                            <div class="dialog-icon">
                                <v-icon color="white">
                                    mdi-cash-plus
                                </v-icon>
                            </div>


                            <div>

                                <span class="dialog-kicker">
                                    Gestión manual
                                </span>

                                <h3 class="dialog-title">
                                    Registrar pago manual
                                </h3>

                            </div>


                        </div>
                        <v-spacer></v-spacer>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-autocomplete v-model="empresaSeleccionada" :items="empresas" item-text="nombre_comercial"
                            item-value="id" label="Seleccione empresa" outlined prepend-inner-icon="mdi-domain"
                            @input="seleccionarEmpresa" />

                        <div v-if="empresaSeleccionada">

                            <!-- SI TIENE PLAN ACTIVO -->
                            <v-alert v-if="suscripcionActual" type="info" outlined>

                                <strong>
                                    Plan actual:
                                </strong>

                                {{ suscripcionActual.plan_nombre }}

                                <br>

                                <strong>
                                    Vencimiento:
                                </strong>

                                {{ formatearFecha(suscripcionActual.fecha_vencimiento) }}

                            </v-alert>

                            <!-- OPCIONES SOLO SI TIENE PLAN -->
                            <v-radio-group v-if="suscripcionActual" v-model="accionPago">

                                <v-radio label="Renovar plan actual" value="renovar" />

                                <v-radio label="Cambiar de plan" value="cambiar" />

                            </v-radio-group>

                            <!-- SELECCIONAR PLAN -->
                            <v-select v-if="!suscripcionActual || accionPago === 'cambiar'" v-model="nuevoPlan"
                                :items="planes" item-text="nombre" item-value="id" label="Seleccione plan" outlined
                                prepend-inner-icon="mdi-crown" @change="seleccionarPlan" />

                            <!-- VALOR DEL PLAN (READONLY / NO MODIFICABLE) -->
                            <v-text-field v-model="valor" label="Valor del plan" outlined readonly
                                prepend-inner-icon="mdi-cash" />

                            <!-- FECHAS DE SUSCRIPCIÓN -->
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="fechaInicio" label="Fecha de inicio" type="date" outlined
                                        prepend-inner-icon="mdi-calendar-start" />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="fechaVencimiento" label="Fecha de vencimiento" type="date"
                                        outlined prepend-inner-icon="mdi-calendar-end" />
                                </v-col>
                            </v-row>

                            <!-- OTROS CAMPOS DE PAGO MANUAL -->
                            <div>
                                <v-select v-model="metodoPago" :items="metodosPago" item-text="nombre" item-value="id"
                                    label="Método de pago" outlined prepend-inner-icon="mdi-credit-card" />

                                <v-text-field v-model="referencia" label="Referencia" outlined
                                    prepend-inner-icon="mdi-barcode" />

                                <v-textarea v-model="observaciones" label="Observaciones" outlined rows="3"
                                    prepend-inner-icon="mdi-note-text" />
                            </div>
                        </div>
                    </v-card-text>


                    <v-divider></v-divider>


                    <v-card-actions>


                        <v-spacer></v-spacer>


                        <v-btn text @click="cancelarPagoManual">
                            Cancelar
                        </v-btn>


                        <v-btn class="gold-button" rounded dark @click="confirmarPago" data-action-loader="true">
                            <v-icon left>
                                mdi-content-save-check
                            </v-icon>

                            Guardar pago
                        </v-btn>


                    </v-card-actions>



                </v-card>


            </v-dialog>

            <!-- <v-dialog v-model="dialogDecisionPlan" max-width="500" persistent>

                <v-card class="dialog-card">


                    <v-card-title class="dialog-card-title">

                        <div class="dialog-header-left">

                            <div class="dialog-icon">

                                <v-icon color="white">
                                    mdi-crown-outline
                                </v-icon>

                            </div>


                            <div>

                                <span class="dialog-kicker">
                                    Suscripción existente
                                </span>

                                <h3 class="dialog-title">
                                    Gestión del plan actual
                                </h3>

                            </div>


                        </div>


                    </v-card-title>


                    <v-divider></v-divider>


                    <v-card-text>


                        <v-alert type="info" outlined>

                            La empresa ya tiene una suscripción activa.

                            <br><br>

                            <strong>
                                Plan actual:
                            </strong>

                            {{ suscripcionActual?.plan_nombre }}

                            <br>

                            <strong>
                                Vencimiento:
                            </strong>

                            {{ formatearFecha(suscripcionActual?.fecha_vencimiento) }}

                            <br><br>

                            ¿Desea renovar este plan o cambiarlo?

                        </v-alert>


                    </v-card-text>



                    <v-divider></v-divider>


                    <v-card-actions>


                        <v-spacer></v-spacer>


                        <v-btn text @click="dialogDecisionPlan = false">

                            Cancelar

                        </v-btn>



                        <v-btn class="gold-button" rounded dark @click="renovarPlanActual">

                            <v-icon left>
                                mdi-refresh
                            </v-icon>

                            Renovar

                        </v-btn>



                        <v-btn color="primary" rounded dark @click="cambiarPlan">

                            <v-icon left>
                                mdi-swap-horizontal
                            </v-icon>

                            Cambiar plan

                        </v-btn>



                    </v-card-actions>


                </v-card>


            </v-dialog> -->
            <v-dialog v-model="dialogCambioPlan" max-width="550" persistent>


                <v-card class="dialog-card">


                    <v-card-title class="dialog-card-title">


                        <div class="dialog-header-left">


                            <div class="dialog-icon">

                                <v-icon color="white">
                                    mdi-crown
                                </v-icon>

                            </div>


                            <div>

                                <span class="dialog-kicker">
                                    Cambio de suscripción
                                </span>


                                <h3 class="dialog-title">
                                    Seleccione nuevo plan
                                </h3>


                            </div>


                        </div>


                    </v-card-title>



                    <v-divider></v-divider>



                    <v-card-text>


                        <v-select v-model="nuevoPlan" :items="planes" item-text="nombre" item-value="id"
                            @change="seleccionarPlan" />


                    </v-card-text>



                    <v-divider></v-divider>


                    <v-card-actions>


                        <v-spacer></v-spacer>


                        <v-btn text @click="dialogCambioPlan = false">

                            Cancelar

                        </v-btn>



                        <v-btn class="gold-button" rounded dark @click="confirmarCambioPlan">

                            <v-icon left>
                                mdi-check
                            </v-icon>

                            Confirmar cambio

                        </v-btn>


                    </v-card-actions>



                </v-card>


            </v-dialog>
        </div>
    </section>
</template>

<script>
import api from '@/services/api'

export default {
    name: "PagosEmpresa",
    props: {
        user: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            dialogDecisionPlan: false,
            dialogCambioPlan: false,
            empresaConPagos: null,
            dialogPagoManual: false,
            empresas: [],
            planes: [],
            empresaSeleccionada: null,
            suscripcionActual: null,
            accionPago: 'renovar',
            nuevoPlan: null,
            metodosPago: [],
            metodoPago: null,
            valor: null,
            referencia: '',
            observaciones: '',
            fechaInicio: '',
            fechaVencimiento: '',
            pagos: [],
            dialogDetalle: false,
            dialogHistorialDetalle: false,
            pagoSeleccionado: {},
            pagoHistorialSeleccionado: {},
            headers: [
                { text: 'Empresa', value: 'empresa_nombre' },
                { text: 'Plan', value: 'plan_nombre' },
                { text: 'Tipo', value: 'tipo_pago' },
                { text: 'Inicio', value: 'fecha_inicio' },
                { text: 'Vence', value: 'fecha_vencimiento' },
                { text: 'Método', value: 'metodo_pago' },
                { text: 'Valor', value: 'valor' },
                { text: 'Estado', value: 'estado_pago' },
                { text: 'Referencia', value: 'referencia' },
                { text: 'Acciones', value: 'acciones', sortable: false }
            ]
        };
    },
    mounted() {
        this.listarPlanes();
        this.listarPagos();
    },
    computed: {
        pagosProcesados() {
            const cronologicos = [...this.pagos].sort((a, b) => {
                const fechaDiff = this.ordenarFechaAsc(a) - this.ordenarFechaAsc(b);
                if (fechaDiff !== 0) {
                    return fechaDiff;
                }
                return (Number(a.id) || 0) - (Number(b.id) || 0);
            });
            const historialPorEmpresa = new Map();

            const procesados = cronologicos.map(item => {
                const tipoPago = this.resolverTipoPago(item, historialPorEmpresa);

                return {
                    ...item,
                    tipo_pago: item.tipo_pago || tipoPago,
                };
            });

            return procesados.sort((a, b) => {
                const fechaDiff = this.ordenarFechaDesc(b) - this.ordenarFechaDesc(a);
                if (fechaDiff !== 0) {
                    return fechaDiff;
                }
                return (Number(b.id) || 0) - (Number(a.id) || 0);
            });
        },

        pagosAprobados() {
            return this.pagosProcesados.filter(item => this.esPagoAprobado(item)).length;
        },

        totalIngresos() {
            const total = this.pagosProcesados.reduce((suma, item) => {
                if (!this.esPagoAprobado(item)) {
                    return suma;
                }

                return suma + this.normalizarValor(item.valor);
            }, 0);

            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(total);
        },

        renovaciones() {
            return this.pagosProcesados.filter(item => String(item.tipo_pago || '').toLowerCase() === 'renovación').length;
        },

        empresasConPagos() {
            return new Set(
                this.pagosProcesados
                    .filter(item => this.esPagoAprobado(item))
                    .map(item => item.empresa_id)
            ).size;
        },

        pagosEmpresa() {
            if (!this.pagoSeleccionado || !this.pagoSeleccionado.empresa_id) {
                return [];
            }
            return this.pagosProcesados.filter(
                p => p.empresa_id === this.pagoSeleccionado.empresa_id
            );
        }
    },
    watch: {
        accionPago(val) {
            if (val === 'renovar' && this.suscripcionActual) {
                this.nuevoPlan = null;
                const plan = this.planes.find(p => p.id == this.suscripcionActual.plan_id);
                if (plan) {
                    this.valor = plan.precio || plan.valor;
                }
                this.fechaInicio = this.suscripcionActual.fecha_inicio ? this.suscripcionActual.fecha_inicio.slice(0, 10) : '';
                this.fechaVencimiento = this.suscripcionActual.fecha_vencimiento ? this.suscripcionActual.fecha_vencimiento.slice(0, 10) : '';
            } else if (val === 'cambiar') {
                this.valor = null;
                this.nuevoPlan = null;
            }
        }
    },

    methods: {
        empresaTienePagosRegistrados(empresaId) {
            if (!empresaId) {
                return false;
            }

            return this.pagos.some(item => Number(item.empresa_id) === Number(empresaId));
        },
        seleccionarPlan() {
            const plan = this.planes.find(p => p.id == this.nuevoPlan);

            if (plan) {
                this.valor = plan.precio || plan.valor;
            }
        },
        async listarMetodosPago() {
            try {
                const response = await api.get('/metodos-pago/listar');
                this.metodosPago = response.data;
            } catch (error) {
                console.error('Error al listar métodos de pago:', error);
            }
        },
        async confirmarPago() {
            try {
                const isManual = this.dialogPagoManual;

                if (isManual) {
                    if (!this.empresaSeleccionada) {
                        this.$toast.error('Debe seleccionar una empresa.');
                        return;
                    }
                    const planId = this.accionPago === 'renovar' ? (this.suscripcionActual?.plan_id) : this.nuevoPlan;
                    if (!planId) {
                        this.$toast.error('Debe seleccionar un plan.');
                        return;
                    }
                    if (!this.metodoPago) {
                        this.$toast.error('Debe seleccionar un método de pago.');
                        return;
                    }
                    if (!this.fechaInicio || !this.fechaVencimiento) {
                        this.$toast.error('Las fechas de inicio y vencimiento son requeridas.');
                        return;
                    }

                    const payload = {
                        empresa_id: this.empresaSeleccionada,
                        plan_id: planId,
                        metodo_pago_id: this.metodoPago,
                        valor: this.valor,
                        fecha_inicio: this.fechaInicio,
                        fecha_vencimiento: this.fechaVencimiento,
                        referencia: this.referencia,
                        observaciones: this.observaciones
                    };

                    this.$emit('start-action', 'Registrando pago manual...', null, 2500);

                    const response = await api.post(
                        '/suscripciones/registrar-pago-manual',
                        payload
                    );

                    const pagoCreado = response?.data?.data;

                    if (pagoCreado && pagoCreado.id) {
                        this.pagos = [
                            pagoCreado,
                            ...this.pagos.filter(item => item.id !== pagoCreado.id)
                        ];
                    }

                    this.cancelarPagoManual();
                    this.$toast.success(response.data.message || 'Pago manual registrado exitosamente.');
                    this.$emit('payment-updated');
                    this.listarPagos();

                } else {
                    const pagoId = this.pagoSeleccionado.id;
                    const fInicio = this.pagoSeleccionado.fecha_inicio;
                    const fVencimiento = this.pagoSeleccionado.fecha_vencimiento;

                    if (!pagoId) {
                        this.$toast.error('No se pudo encontrar un registro de pago para confirmar.');
                        return;
                    }
                    if (!fInicio || !fVencimiento) {
                        this.$toast.error('Las fechas de inicio y vencimiento son requeridas.');
                        return;
                    }

                    const payload = {
                        pago_id: pagoId,
                        estado_pago_id: 2, // Aprobado
                        fecha_pago: new Date().toISOString().slice(0, 10),
                        estado_id: 1, // Activa
                        fecha_inicio: fInicio,
                        fecha_vencimiento: fVencimiento
                    };

                    const response = await api.put(
                        '/suscripciones/confirmar-pago',
                        payload
                    );

                    this.$toast.success(response.data.message);
                    this.dialogDetalle = false;
                    this.$emit('payment-updated');
                    await this.listarPagos();
                }

            } catch (error) {
                this.$toast.error(
                    error.response?.data?.mensaje || 'Error al procesar el pago'
                );
                console.error(error);
            } finally {
                this.$emit('stop-action');
            }
        },
        async listarPlanes() {
            try {
                const response = await api.get('/planes/listar');
                this.planes = response.data;
            } catch (error) {
                console.error('Error al listar planes:', error);
            }
        },
        confirmarCambioPlan() {

            if (!this.nuevoPlan) {

                this.$toast.error('Seleccione un plan');

                return;

            }


            this.dialogCambioPlan = false;


            this.accionPago = 'cambiar';


            console.log({

                empresa: this.empresaSeleccionada,

                plan: this.nuevoPlan,

                accion: this.accionPago

            });


        },
        cambiarPlan() {

            this.accionPago = 'cambiar';

            this.dialogDecisionPlan = false;

            this.dialogCambioPlan = true;

        },
        renovarPlanActual() {

            this.accionPago = 'renovar';

            this.dialogDecisionPlan = false;

            this.cargarSuscripcionEmpresa();

        },
        async seleccionarEmpresa() {

            try {
                this.suscripcionActual = null;

                const tienePagos = this.empresaTienePagosRegistrados(this.empresaSeleccionada);

                const response = await api.get(
                    `/suscripciones/pagos-planes/${this.empresaSeleccionada}`
                );


                console.log('Suscripción:', response.data);


                if (response.data && tienePagos) {

                    // Tiene plan activo
                    this.suscripcionActual = response.data;

                    // Por defecto renovar
                    this.accionPago = 'renovar';

                    // Limpiar plan nuevo
                    this.nuevoPlan = null;

                    // Cargar precio del plan actual
                    const plan = this.planes.find(p => p.id == this.suscripcionActual.plan_id);
                    if (plan) {
                        this.valor = plan.precio || plan.valor;
                    }

                    // Cargar fechas actuales
                    this.fechaInicio = this.suscripcionActual.fecha_inicio ? this.suscripcionActual.fecha_inicio.slice(0, 10) : '';
                    this.fechaVencimiento = this.suscripcionActual.fecha_vencimiento ? this.suscripcionActual.fecha_vencimiento.slice(0, 10) : '';

                } else {

                    // No se muestra plan actual para empresas sin historial de pagos
                    this.suscripcionActual = null;

                    // Cambiar porque debe escoger un plan nuevo
                    this.accionPago = 'cambiar';


                    this.nuevoPlan = null;
                    this.valor = null;
                    this.fechaInicio = '';
                    this.fechaVencimiento = '';


                    if (response.data && !tienePagos) {
                        this.$toast.info('Empresa nueva sin pagos registrados. Debe seleccionar un plan nuevo.');
                    } else {
                        this.$toast.info('Actualmente esta empresa no tiene un plan activo con LogistikPro. Selecciona un plan.');
                    }

                }


            } catch (error) {

                console.error('Error al validar suscripción:', error);

                this.suscripcionActual = null;
                this.valor = null;
                this.fechaInicio = '';
                this.fechaVencimiento = '';

            }

        },
        cancelarPagoManual() {

            this.dialogPagoManual = false;
            this.dialogDecisionPlan = false;
            this.dialogCambioPlan = false;
            this.empresaSeleccionada = null;
            this.empresaConPagos = null;
            this.suscripcionActual = null;
            this.accionPago = 'renovar';
            this.nuevoPlan = null;
            this.metodoPago = null;
            this.valor = null;
            this.referencia = '';
            this.observaciones = '';
            this.fechaInicio = '';
            this.fechaVencimiento = '';

        },
        async listarEmpresas() {
            try {

                const response = await api.get('/superadmin/panel');

                console.log('Respuesta panel:', response.data);

                this.empresas = response.data.empresas || response.data;

            } catch (error) {

                console.error('Error al listar empresas:', error);

            }
        },
        async abrirPagoManual() {

            this.dialogPagoManual = true;

            await this.listarEmpresas();
            await this.listarPlanes();
            await this.listarMetodosPago();

        },
        verDetalle(item) {
            this.abrirDetallePago(item);
        },
        verDetalleDesdeHistorial(pago) {
            this.pagoHistorialSeleccionado = { ...pago };
            this.$nextTick(() => {
                this.dialogHistorialDetalle = true;
            });
        },
        gestionarPago() {
            // Acciones visuales pendientes de implementación: el loader global ya da feedback inmediato.
        },
        abrirDetallePago(pago) {
            this.pagoSeleccionado = { ...pago };
            this.dialogDetalle = true;
        },
        async listarPagos() {
            try {
                const response = await api.get(`/empresas/pagos/${this.user.empresa_id}`);
                this.pagos = [...response.data].sort((a, b) => this.ordenarFechaDesc(b) - this.ordenarFechaDesc(a) || (Number(b.id) || 0) - (Number(a.id) || 0));
            } catch (error) {
                console.error('Error al listar pagos:', error);
            }
        },
        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor || 0);
        },
        normalizarValor(valor) {
            if (typeof valor === 'number') {
                return valor;
            }

            const limpio = String(valor ?? '').trim().replace(/\s+/g, '');

            if (!limpio) {
                return 0;
            }

            let numero = limpio.replace(/[^\d,.-]/g, '');

            if (numero.includes(',')) {
                numero = numero.replace(/\./g, '').replace(',', '.');
            } else if (numero.includes('.')) {
                const partes = numero.split('.');

                if (partes.length > 2) {
                    numero = partes.join('');
                } else if (partes[1] && partes[1].length === 3) {
                    numero = partes.join('');
                }
            }

            const parsed = Number(numero);

            return Number.isNaN(parsed) ? 0 : parsed;
        },
        esPagoAprobado(item) {
            const estado = String(item?.estado_pago || '').toLowerCase();
            return estado === 'aprobado' || item?.estado_pago_id == 2;
        },
        resolverTipoPago(item, historialPorEmpresa) {
            if (!this.esPagoAprobado(item)) {
                return item.tipo_pago || 'Pendiente';
            }

            const empresaId = item.empresa_id;
            const planId = item.plan_id != null ? Number(item.plan_id) : null;
            const historial = historialPorEmpresa.get(empresaId) || null;

            let tipoPago = item.tipo_pago || '';

            if (!tipoPago) {
                if (!historial) {
                    tipoPago = 'Primer pago';
                } else if (historial.plan_id !== null && planId !== null && historial.plan_id === planId) {
                    tipoPago = 'Renovación';
                } else {
                    tipoPago = 'Cambio de plan';
                }
            }

            historialPorEmpresa.set(empresaId, {
                plan_id: planId,
                tipo_pago: tipoPago,
            });

            return tipoPago;
        },
        tipoPagoClass(tipoPago) {
            const value = String(tipoPago || '').toLowerCase();

            if (value === 'renovación') {
                return 'status-warning';
            }

            if (value === 'cambio de plan') {
                return 'status-cancelado';
            }

            if (value === 'pendiente') {
                return 'status-off';
            }

            return 'status-ok';
        },
        tipoPagoIcon(tipoPago) {
            const value = String(tipoPago || '').toLowerCase();

            if (value === 'renovación') {
                return 'mdi mdi-refresh';
            }

            if (value === 'cambio de plan') {
                return 'mdi mdi-swap-horizontal';
            }

            if (value === 'pendiente') {
                return 'mdi mdi-clock-outline';
            }

            return 'mdi mdi-ray-start-arrow';
        },
        tipoPagoLabel(pago) {
            if (!pago) {
                return '-';
            }

            return pago.tipo_pago || 'Pendiente';
        },
        parseFecha(fecha) {
            if (!fecha) {
                return 0;
            }

            const parsed = new Date(fecha);
            return Number.isNaN(parsed.getTime()) ? 0 : parsed.getTime();
        },
        ordenarFechaAsc(item) {
            return this.parseFecha(item.fecha_pago || item.created_at || item.updated_at);
        },
        ordenarFechaDesc(item) {
            return this.parseFecha(item.fecha_pago || item.created_at || item.updated_at);
        },
        formatearFecha(fecha) {
            if (!fecha) return '-';

            return new Intl.DateTimeFormat('es-CO', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }).format(new Date(fecha));
        },
    },
};
</script>

<style scoped>
.gold-button {
    height: 50px !important;
    padding: 0 20px !important;

    border-radius: 14px !important;

    background: linear-gradient(135deg,
            #f4b740 0%,
            #ffd277 45%,
            #d99210 100%) !important;

    color: #0b1530 !important;

    font-weight: 800 !important;

    text-transform: none !important;

    box-shadow:
        0 8px 20px rgba(250, 175, 1, .25);

    transition: all .25s ease;
}


.gold-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 14px 30px rgba(250, 175, 1, .35);

}


.gold-button:active {

    transform: translateY(0);

}


.gold-button .v-icon {

    color: #0b1530 !important;

}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-top: 18px;
    margin-bottom: 18px;
}

.stat-card {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px;
    border-radius: 22px;
    border: 1px solid rgba(23, 48, 79, 0.08);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 253, 0.98));
    box-shadow: 0 16px 34px rgba(14, 28, 54, 0.07);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    opacity: .08;
    pointer-events: none;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 22px 44px rgba(14, 28, 54, 0.12);
}

.stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    color: white;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
}

.stat-info span {
    display: block;
    margin-bottom: 6px;
    color: rgba(23, 48, 79, 0.62);
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.72rem;
    font-weight: 700;
}

.stat-info h3 {
    margin: 0;
    color: #17304f;
    font-size: 1.5rem;
    font-weight: 800;
}

.stat-blue::before {
    background: linear-gradient(135deg, #2563eb, #60a5fa);
}

.stat-blue .stat-icon {
    background: linear-gradient(135deg, #2563eb, #60a5fa);
}

.stat-green::before {
    background: linear-gradient(135deg, #16a34a, #22c55e);
}

.stat-green .stat-icon {
    background: linear-gradient(135deg, #16a34a, #22c55e);
}

.stat-yellow::before {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

.stat-yellow .stat-icon {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

.stat-purple::before {
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
}

.stat-purple .stat-icon {
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
}

.meta-chip.payment-type {
    background: #fff7e6;
    color: #b45309;
}

.dialog-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.dialog-icon {

    width: 58px;
    height: 58px;

    border-radius: 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: linear-gradient(135deg, #17304f, #284d73);

    box-shadow: 0 10px 25px rgba(23, 48, 79, .18);

}

.dialog-meta {

    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;

}

.meta-chip {

    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 6px 12px;

    border-radius: 999px;

    font-size: 12px;
    font-weight: 700;

}

.meta-chip.empresa {

    background: #eef5ff;
    color: #2563eb;

}

.meta-chip.plan {

    background: #f5f0ff;
    color: #7c3aed;

}

.detalle-resumen {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 18px;
    padding: 24px;
}

.summary-card {

    border: 1px solid #edf2f7;
    background: #fafbfd;
    border-radius: 18px;
    padding: 20px;

}

.summary-card.success {

    background: linear-gradient(135deg, #16a34a, #22c55e);
    color: white;
    border: none;

}

.summary-card.success h2 {

    margin-top: 6px;
    font-size: 30px;
    font-weight: 800;

}

.summary-label {

    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: .8;
    font-weight: 700;

}

.detalle-grid {

    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
    padding: 24px;

}

.info-card {

    background: #fbfcfe;
    border: 1px solid #edf2f7;
    border-radius: 16px;
    padding: 18px;

}

.info-card label {

    display: block;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    margin-bottom: 10px;
    font-weight: 700;

}

.info-value {

    display: flex;
    align-items: center;
    gap: 10px;

    font-size: 15px;
    font-weight: 700;
    color: #17304f;

}

.codigo {

    display: inline-block;
    padding: 8px 12px;
    border-radius: 10px;

    background: #eef2f7;

    font-family: monospace;
    font-weight: 700;
    color: #17304f;

}

.dialog-actions {

    justify-content: flex-end;
    padding: 20px 24px;

}

.detalle-dialog {

    border-radius: 24px;
    overflow: hidden;

}

.dialog-header {

    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 28px;

    background: #fff;

}

.dialog-kicker {

    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 2px;
    color: #6b7280;
    font-weight: 700;

}

.dialog-header h2 {

    margin-top: 6px;
    color: #17304f;

}

.detalle-grid {

    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;

    padding: 30px;

}

.detalle-item {

    display: flex;
    flex-direction: column;
    gap: 10px;

}

.detalle-item label {

    font-size: 13px;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;

}

.dialog-footer {

    padding: 0 30px 24px;
    display: flex;
    justify-content: flex-end;

}

.acciones {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
}


.action-item .v-btn {
    width: 30px !important;
    height: 30px !important;
}

/* .action-item {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
} */

.action-item span {

    position: absolute;
    bottom: calc(100% + 8px);

    left: 50%;
    transform: translateX(-50%);

    background: #17304f;
    color: #fff;

    padding: 6px 10px;
    border-radius: 8px;

    font-size: 12px;
    font-weight: 600;

    white-space: nowrap;

    opacity: 0;
    visibility: hidden;

    transition: .2s;

    pointer-events: none;

}

.action-item:hover span {

    opacity: 1;
    visibility: visible;

}

.status-cancelado {

    background: rgba(107, 114, 128, .15);
    color: #6b7280;

}

.empresa-chip,
.plan-chip,
.metodo-chip {

    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 12px;
    font-weight: 700;

}

.empresa-chip {

    background: rgba(37, 99, 235, .08);
    color: #2563eb;

}

.plan-chip {

    background: rgba(124, 58, 237, .10);
    color: #7c3aed;

}

.metodo-chip {

    background: rgba(23, 48, 79, .08);
    color: #17304f;

}

.fecha-chip {

    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #17304f;
    font-weight: 600;

}

.precio-card {

    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 12px;
    background: rgba(22, 163, 74, .12);
    color: #16a34a;
    font-weight: 800;

}

.status-pill {

    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 999px;
    font-weight: 700;

}

.status-ok {

    background: rgba(22, 163, 74, .15);
    color: #15803d;

}

.status-warning {

    background: rgba(250, 175, 1, .18);
    color: #b45309;

}

.status-off {

    background: rgba(239, 68, 68, .15);
    color: #dc2626;

}

.acciones {

    display: flex;
    gap: 8px;

}

.referencia-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 10px;
    background: #f5f7fb;
    color: #17304f;
    font-family: monospace;
    font-weight: 700;
}

.metodo-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(23, 48, 79, .07);
    color: #17304f;
    font-weight: 700;
}

.table-card {
    padding: 24px;
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.table-wrap {
    border: none;
    box-shadow: none;
    background: transparent;
    padding: 0;
    margin-top: 8px;
}

/* Título + Kicker */
.table-head {
    margin-bottom: 24px;
    display: flex;
    align-items: baseline;
    justify-content: space-between;
}

.table-kicker {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1.6px;
    font-weight: 600;
    color: var(--color-text-subtle);
    margin-bottom: 6px;
}

.table-head h2 {
    font-size: 22px;
    color: var(--color-text-title);
    margin: 0;
    font-weight: 600;
}

.table-count {
    padding: 9px 12px;
    border-radius: 999px;
    background: rgba(250, 175, 1, 0.12);
    color: #996600;
    font-weight: 800;
}

.historial-pagos-section {
    padding: 24px;
    background: #f8fafc;
}

.historial-pagos-section .section-title {
    font-size: 16px;
    font-weight: 700;
    color: #17304f;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
}

.historial-list-container {
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    background: white;
}

.historial-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.historial-table th,
.historial-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #edf2f7;
}

.historial-table th {
    background: #fafbfd;
    color: #64748b;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.historial-table tbody tr:hover {
    background: #f1f5f9;
}

.historial-table tr.fila-seleccionada {
    background: rgba(37, 99, 235, 0.06);
}

.plan-chip-small {
    padding: 4px 8px;
    background: rgba(124, 58, 237, 0.1);
    color: #7c3aed;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
}

.status-pill-small {
    display: inline-flex;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.historial-dialog-card {
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 30px 70px rgba(15, 25, 45, 0.24);
}

.historial-head-band {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #17304f 0%, #2563eb 42%, #22c55e 100%);
}

.historial-title-wrap {
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding-top: 22px;
}

.historial-title {
    margin-top: 4px;
}

.historial-meta {
    margin-top: 12px;
}

.historial-icon {
    background: linear-gradient(135deg, #17304f, #2563eb);
}

.historial-head-actions {
    display: inline-flex;
    align-items: center;
    margin-left: 12px;
}

.historial-kpi-strip {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    padding: 6px 24px 0;
}

.historial-kpi-item {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    padding: 14px 16px;
    background: #f8fbff;
}

.historial-kpi-item strong {
    display: block;
    margin-top: 4px;
    color: #17304f;
    font-size: 15px;
    font-weight: 800;
}

.historial-kpi-sub {
    display: block;
    margin-top: 6px;
    color: #5b6f87;
    font-size: 11px;
    font-weight: 600;
}

.historial-kpi-label {
    font-size: 11px;
    letter-spacing: .9px;
    text-transform: uppercase;
    color: #5b6f87;
    font-weight: 700;
}

.kpi-valor {
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.08), rgba(34, 197, 94, 0.1));
}

.kpi-fecha {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(96, 165, 250, 0.12));
}

.kpi-metodo {
    background: linear-gradient(135deg, rgba(23, 48, 79, 0.05), rgba(23, 48, 79, 0.11));
}

.historial-body-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 14px;
    padding: 20px 24px 26px;
}

.historial-col {
    display: grid;
    gap: 12px;
}

.historial-info-card {
    background: #fbfdff;
    border: 1px solid #e8eef5;
    border-radius: 14px;
    padding: 16px;
}

.historial-info-card label {
    display: block;
    margin-bottom: 9px;
    color: #667991;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 11px;
    font-weight: 800;
}

.historial-referencia-card {
    position: relative;
}

.historial-codigo {
    width: 100%;
    font-size: 13px;
    background: #eef3f8;
}

.historial-trace-card {
    border-radius: 14px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: linear-gradient(180deg, #ffffff, #f6f9fd);
    padding: 14px 16px;
}

.trace-title {
    display: inline-flex;
    align-items: center;
    font-weight: 800;
    color: #17304f;
    font-size: 13px;
    margin-bottom: 10px;
}

.trace-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 8px 0;
    border-bottom: 1px dashed rgba(23, 48, 79, 0.13);
}

.trace-row:last-child {
    border-bottom: none;
}

.trace-row span {
    color: #64748b;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .7px;
    font-weight: 700;
}

.trace-row strong {
    color: #17304f;
    font-size: 13px;
    font-weight: 800;
    text-align: right;
}

@media (max-width: 1100px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .historial-kpi-strip {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .historial-title-wrap {
        flex-direction: column;
    }

    .historial-head-actions {
        margin-left: 0;
        width: 100%;
        justify-content: space-between;
    }

    .historial-body-grid {
        grid-template-columns: 1fr;
    }

    .historial-referencia-card {
        grid-column: auto;
    }

    .trace-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .trace-row strong {
        text-align: left;
    }
}
</style>
