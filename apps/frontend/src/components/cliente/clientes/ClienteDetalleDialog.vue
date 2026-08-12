<template>
    <v-dialog :value="value" max-width="980px" persistent @input="emitirInput">
        <v-card class="dialog-card detail-dialog-card">
            <v-card-title class="dialog-card-title detail-header">
                <v-avatar size="48" class="dialog-avatar detail-avatar">
                    <v-icon large>mdi-account-details-outline</v-icon>
                </v-avatar>

                <div class="detail-header-copy">
                    <span class="dialog-kicker">Ficha del cliente</span>
                    <h3 class="dialog-title">{{ resolverNombrePrincipal(cliente) }}</h3>
                    <p class="detail-subtitle">
                        {{ cliente?.tipo_persona === 'juridica' ? 'Persona juridica' : 'Persona natural' }}
                    </p>
                </div>

                <span :class="['status-pill', esClienteActivo(cliente) ? 'status-ok' : 'status-off']">
                    {{ esClienteActivo(cliente) ? 'Activo' : 'Inactivo' }}
                </span>
            </v-card-title>

            <v-divider />

            <v-card-text class="dialog-card-body detail-body">
                <section class="detail-kpis" aria-label="Resumen del cliente">
                    <article class="detail-kpi detail-kpi-credit">
                        <span>Limite credito</span>
                        <strong>{{ money(cliente?.limite_credito) }}</strong>
                    </article>

                    <article class="detail-kpi detail-kpi-balance">
                        <span>Saldo credito</span>
                        <strong>{{ money(cliente?.saldo_credito) }}</strong>
                    </article>

                    <article class="detail-kpi detail-kpi-days">
                        <span>Dias credito</span>
                        <strong>{{ Number(cliente?.dias_credito || 0) }}</strong>
                    </article>

                    <article class="detail-kpi detail-kpi-location">
                        <span>Ubicacion</span>
                        <strong>{{ valorDetalle(cliente?.ciudad_nombre, '-') }}</strong>
                        <small>{{ valorDetalle(cliente?.departamento_nombre, '-') }}</small>
                    </article>
                </section>

                <section class="detail-grid" aria-label="Detalle completo del cliente">
                    <article class="detail-panel detail-panel--identity">
                        <h4><span class="detail-section-tag detail-section-tag--identity">Identificacion</span></h4>
                        <ul>
                            <li><span>Tipo documento</span><strong>{{ valorDetalle(cliente?.tipo_documento_nombre) }}</strong></li>
                            <li><span>Numero documento</span><strong>{{ valorDetalle(cliente?.numero_documento) }}</strong></li>
                            <li><span>Tipo persona</span><strong>{{ cliente?.tipo_persona === 'juridica' ? 'Juridica' : 'Natural' }}</strong></li>
                            <li><span>Genero</span><strong>{{ valorDetalle(cliente?.genero) }}</strong></li>
                        </ul>
                    </article>

                    <article class="detail-panel detail-panel--contact">
                        <h4><span class="detail-section-tag detail-section-tag--contact">Contacto</span></h4>
                        <ul>
                            <li><span>Email</span><strong>{{ valorDetalle(cliente?.email) }}</strong></li>
                            <li><span>Celular</span><strong>{{ valorDetalle(cliente?.celular) }}</strong></li>
                            <li><span>Telefono</span><strong>{{ valorDetalle(cliente?.telefono) }}</strong></li>
                            <li><span>Direccion</span><strong>{{ valorDetalle(cliente?.direccion) }}</strong></li>
                        </ul>
                    </article>

                    <article class="detail-panel detail-panel--business">
                        <h4><span class="detail-section-tag detail-section-tag--business">Informacion comercial</span></h4>
                        <ul>
                            <li><span>Razon social</span><strong>{{ valorDetalle(cliente?.razon_social) }}</strong></li>
                            <li><span>Nombre comercial</span><strong>{{ valorDetalle(cliente?.nombre_comercial) }}</strong></li>
                            <li><span>Nombres</span><strong>{{ valorDetalle(cliente?.nombre) }}</strong></li>
                            <li><span>Apellidos</span><strong>{{ valorDetalle(cliente?.apellido) }}</strong></li>
                        </ul>
                    </article>
                </section>

                <section class="detail-description" aria-label="Datos adicionales del cliente">
                    <h4>Datos adicionales</h4>
                    <ul>
                        <li><span>Pais</span><strong>{{ valorDetalle(cliente?.pais_nombre || cliente?.pais?.nombre) }}</strong></li>
                        <li><span>Departamento</span><strong>{{ valorDetalle(cliente?.departamento_nombre || cliente?.departamento?.nombre) }}</strong></li>
                        <li><span>Ciudad</span><strong>{{ valorDetalle(cliente?.ciudad_nombre || cliente?.ciudad?.nombre) }}</strong></li>
                        <li><span>Fecha nacimiento</span><strong>{{ formatearFecha(cliente?.fecha_nacimiento) }}</strong></li>
                    </ul>
                </section>
            </v-card-text>

            <v-divider />

            <v-card-actions class="dialog-actions">
                <button type="button" class="secondary-button" @click="cerrar">
                    Cerrar
                </button>
                <button type="button" class="submit-button" @click="$emit('edit', cliente)">
                    Editar cliente
                </button>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'ClienteDetalleDialog',

    props: {
        value: {
            type: Boolean,
            default: false,
        },
        cliente: {
            type: Object,
            default: null,
        },
        estadoActivoId: {
            type: Number,
            default: 1,
        },
    },

    methods: {
        emitirInput(nextValue) {
            this.$emit('input', nextValue);
        },

        cerrar() {
            this.$emit('input', false);
        },

        valorDetalle(value, fallback = 'No registrado') {
            if (value === null || value === undefined) {
                return fallback;
            }

            const normalizado = String(value).trim();
            return normalizado ? normalizado : fallback;
        },

        formatearFecha(value) {
            if (!value) {
                return 'No registrada';
            }

            if (typeof value === 'string') {
                const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/);

                if (match) {
                    const [, year, month, day] = match;
                    return `${day}/${month}/${year}`;
                }
            }

            const fecha = new Date(value);

            if (Number.isNaN(fecha.getTime())) {
                return this.valorDetalle(value);
            }

            return new Intl.DateTimeFormat('es-CO', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            }).format(fecha);
        },

        resolverNombrePrincipal(cliente) {
            if (!cliente) {
                return 'Cliente';
            }

            if (cliente.tipo_persona === 'juridica') {
                return cliente.razon_social || cliente.nombre_comercial || 'Sin razon social';
            }

            const nombreCompleto = `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();
            return nombreCompleto || cliente.nombre || 'Sin nombre';
        },

        esClienteActivo(cliente) {
            const estadoId = Number(cliente?.estado_id || 0);

            if (estadoId > 0) {
                return estadoId === Number(this.estadoActivoId || 1);
            }

            const estadoNombre = String(cliente?.estado?.nombre || cliente?.estado_nombre || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim()
                .toLowerCase();

            return estadoNombre === 'activo';
        },

        money(value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(Number(value || 0));
        },
    },
};
</script>

<style scoped>
.dialog-card {
    border-radius: 28px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 26px 62px rgba(15, 34, 65, 0.18);
}

.dialog-card-title {
    display: flex;
    gap: 18px;
    align-items: center;
    padding: 28px 24px 0;
}

.dialog-avatar {
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
}

.dialog-kicker {
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: rgba(23, 48, 79, 0.58);
    font-size: 0.72rem;
    margin-bottom: 8px;
}

.dialog-title {
    margin: 0;
    font-size: 1.52rem;
    color: #17304f;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 84px;
    padding: 7px 11px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.78rem;
}

.status-ok {
    background: rgba(109, 211, 160, 0.18);
    color: #186843;
}

.status-off {
    background: rgba(255, 123, 123, 0.16);
    color: #9b2f2f;
}

.dialog-card-body {
    padding: 20px 24px;
}

.dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 14px 24px 20px;
}

.detail-dialog-card {
    border-radius: 30px;
}

.detail-header {
    padding-right: 26px;
}

.detail-header-copy {
    flex: 1;
}

.detail-avatar {
    background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 55%, #22d3ee 100%);
    color: #ffffff;
}

.detail-subtitle {
    margin: 8px 0 0;
    color: rgba(23, 48, 79, 0.64);
    font-weight: 600;
}

.detail-body {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.detail-kpis {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.detail-kpi {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    background: #ffffff;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.detail-kpi span {
    color: rgba(23, 48, 79, 0.62);
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-weight: 700;
}

.detail-kpi strong {
    color: #17304f;
    font-size: 1.12rem;
}

.detail-kpi small {
    color: rgba(23, 48, 79, 0.6);
    font-size: 0.74rem;
}

.detail-kpi-credit {
    background: linear-gradient(135deg, rgba(232, 241, 255, 0.92) 0%, rgba(255, 255, 255, 0.96) 100%);
}

.detail-kpi-balance {
    background: linear-gradient(135deg, rgba(229, 252, 237, 0.92) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.detail-kpi-days {
    background: linear-gradient(135deg, rgba(255, 244, 217, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.detail-kpi-location {
    background: linear-gradient(135deg, rgba(245, 245, 245, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.detail-panel {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(255, 255, 255, 0.96);
    padding: 14px;
}

.detail-panel--identity {
    border-color: rgba(37, 99, 235, 0.2);
    background: linear-gradient(155deg, rgba(234, 243, 255, 0.9) 0%, rgba(255, 255, 255, 0.98) 55%);
}

.detail-panel--contact {
    border-color: rgba(14, 165, 233, 0.2);
    background: linear-gradient(155deg, rgba(232, 248, 255, 0.9) 0%, rgba(255, 255, 255, 0.98) 55%);
}

.detail-panel--business {
    border-color: rgba(22, 163, 74, 0.2);
    background: linear-gradient(155deg, rgba(233, 251, 240, 0.9) 0%, rgba(255, 255, 255, 0.98) 55%);
}

.detail-panel h4,
.detail-description h4 {
    margin: 0 0 10px;
    font-size: 0.88rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(23, 48, 79, 0.7);
}

.detail-section-tag {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    letter-spacing: 0.1em;
    font-weight: 800;
}

.detail-section-tag--identity {
    background: rgba(37, 99, 235, 0.16);
    color: #1d4ed8;
}

.detail-section-tag--contact {
    background: rgba(14, 165, 233, 0.2);
    color: #0369a1;
}

.detail-section-tag--business {
    background: rgba(22, 163, 74, 0.18);
    color: #15803d;
}

.detail-panel ul,
.detail-description ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-panel li,
.detail-description li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px dashed rgba(23, 48, 79, 0.14);
    padding-bottom: 8px;
}

.detail-panel li:last-child,
.detail-description li:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.detail-panel li span,
.detail-description li span {
    color: rgba(23, 48, 79, 0.64);
    font-size: 0.8rem;
}

.detail-panel li strong,
.detail-description li strong {
    color: #17304f;
    text-align: right;
}

.detail-description {
    border-radius: 16px;
    border: 1px solid rgba(23, 48, 79, 0.1);
    background: rgba(255, 255, 255, 0.96);
    padding: 14px;
}

.secondary-button {
    border: 1px solid rgba(23, 48, 79, 0.14);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.95);
    color: #17304f;
    font-weight: 800;
    height: 44px;
    min-width: 122px;
    padding: 0 18px;
}

.submit-button {
    border: 0;
    border-radius: 14px;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    font-weight: 800;
    height: 44px;
    min-width: 122px;
    padding: 0 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
}

@media (max-width: 1120px) {
    .detail-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 720px) {
    .dialog-title {
        font-size: 1.22rem;
    }

    .dialog-card-title {
        align-items: flex-start;
    }
}
</style>
