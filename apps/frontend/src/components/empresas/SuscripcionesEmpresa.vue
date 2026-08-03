<template>
    <section class="table-card suscripciones-card">
        <div class="table-head">
            <div>
                <span class="table-kicker">Suscripciones</span>
                <h2>Planes contratados</h2>
            </div>
            <span class="table-count">{{ suscripciones.length }} registros</span>
        </div>
        <div class="table-wrap">
            <v-data-table dense :headers="headers" :items="suscripciones" class="elevation-1 suscripciones-table">

                <!-- Empresa -->
                <template v-slot:[`item.empresa_nombre`]="{ item }">
                    <span class="empresa-chip">
                        <i class="mdi mdi-domain"></i>
                        {{ item.empresa_nombre }}
                    </span>
                </template>

                <!-- Plan -->
                <template v-slot:[`item.plan_nombre`]="{ item }">
                    <span class="plan-chip">
                        <i class="mdi mdi-crown"></i>
                        {{ item.plan_nombre }}
                    </span>
                </template>

                <!-- Fecha Inicio -->
                <template v-slot:[`item.fecha_inicio`]="{ item }">
                    <span>{{ item.fecha_inicio }}</span>
                </template>

                <!-- Fecha Final -->
                <template v-slot:[`item.fecha_final`]="{ item }">
                    <span>{{ item.fecha_final }}</span>
                </template>

                <!-- Fecha Vencimiento -->
                <template v-slot:[`item.fecha_vencimiento`]="{ item }">
                    <span :class="estadoFecha(item.fecha_vencimiento).class">
                        {{ item.fecha_vencimiento }}
                    </span>
                </template>

                <!-- Usuarios -->
                <template v-slot:[`item.usuarios_contratados`]="{ item }">
                    <div class="usuarios-pill">
                        <i class="mdi mdi-account-group"></i>
                        {{ item.usuarios_contratados }}
                    </div>
                </template>

                <!-- Valor -->
                <template v-slot:[`item.valor_pagado`]="{ item }">
                    <div class="precio-card">
                        <i class="mdi mdi-cash"></i>
                        {{ item.valor_pagado }}
                    </div>
                </template>

                <!-- Renovación -->
                <template v-slot:[`item.renovacion`]="{ item }">
                    <div :class="[
                        'status-pill',
                        item.renovacion === 'Automática'
                            ? 'status-ok'
                            : 'status-warning'
                    ]">
                        <i :class="item.renovacion === 'Automática'
                            ? 'mdi mdi-refresh'
                            : 'mdi mdi-hand-back-right'
                            "></i>

                        {{ item.renovacion }}
                    </div>
                </template>

                <!-- Estado -->
                <template v-slot:[`item.estado_id`]="{ item }">
                    <span :class="[
                        'status-pill',
                        item.estado_id == 1
                            ? 'status-ok'
                            : 'status-off'
                    ]">
                        <i :class="item.estado_id == 1
                            ? 'mdi mdi-check-circle'
                            : 'mdi mdi-close-circle'
                            "></i>

                        {{ item.estado || (item.estado_id == 1 ? 'Activa' : 'Inactiva') }}
                    </span>
                </template>

            </v-data-table>
        </div>
    </section>
</template>

<script>
import api from '@/services/api'

export default {
    name: 'SuscripcionesEmpresa',

    props: {
        user: {
            type: Object,
            required: true,
        },
        items: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            suscripciones: [],
            headers: [
                { text: 'Empresa', value: 'empresa_nombre' },
                { text: 'Plan Contratado', value: 'plan_nombre' },
                { text: 'Fecha Inicio', value: 'fecha_inicio' },
                { text: 'Fecha Final', value: 'fecha_final' },
                { text: 'Fecha Vencimiento', value: 'fecha_vencimiento' },
                { text: 'Usuarios Contratados', value: 'usuarios_contratados' },
                { text: 'Valor Pagado', value: 'valor_pagado' },
                { text: 'Renovacion', value: 'renovacion' },
                { text: 'Estado', value: 'estado_nombre' },
            ],
        };
    },

    mounted() {
        this.obtenerSuscripciones();
    },

    methods: {

        async obtenerSuscripciones() {
            try {

                const { data } = await api.get('/suscripciones');

                this.suscripciones = data.map(item => ({

                    ...item,

                    fecha_inicio: this.formatearFecha(item.fecha_inicio),

                    fecha_final: this.formatearFecha(item.fecha_final),

                    fecha_vencimiento: this.formatearFecha(item.fecha_vencimiento),

                    valor_pagado: this.formatoCOP(item.valor_pagado),

                    renovacion: item.renovacion ? 'Automática' : 'Manual'

                }));

            } catch (error) {
                console.error(error);
            }
        },

        formatearFecha(fecha) {
            if (!fecha) return '-';

            return new Intl.DateTimeFormat('es-CO', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }).format(new Date(fecha));
        },

        formatoCOP(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor || 0);
        },

        estadoFecha(fecha) {

            const hoy = new Date();
            const vencimiento = new Date(fecha);

            const diferencia = Math.ceil(
                (vencimiento - hoy) / (1000 * 60 * 60 * 24)
            );

            if (diferencia < 0) {
                return {
                    class: 'fecha-vencida'
                };
            }

            if (diferencia <= 15) {
                return {
                    class: 'fecha-alerta'
                };
            }

            return {
                class: 'fecha-ok'
            };

        }

    }

};
</script>
<style scoped>
.empresa-chip,
.plan-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 10px;
    font-weight: 700;
    transition: .2s;
}

.empresa-chip {
    background: rgba(37, 99, 235, .08);
    color: #1d4ed8;
}

.empresa-chip i {
    font-size: 16px;
}

.plan-chip {
    background: rgba(124, 58, 237, .10);
    color: #7c3aed;
}

.plan-chip i {
    font-size: 16px;
}

.empresa-chip:hover,
.plan-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, .08);
}

.empresa-cell strong {
    display: block;
    color: #17304f;
    font-weight: 800;
}

.empresa-cell small {
    color: #7b8794;
    font-size: .72rem;
}

.plan-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    background: #eef4ff;
    color: #17304f;
    font-weight: 700;
}

.plan-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #2563eb;
}

.usuarios-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 12px;
    background: #eef4ff;
    color: #17304f;
    font-weight: 700;
}

.usuarios-pill i {
    color: #2563eb;
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
    font-weight: 800;
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 24px;
}

.stat-card {

    position: relative;

    display: flex;
    align-items: center;
    gap: 18px;

    padding: 22px;

    border-radius: 22px;

    background: #fff;

    border: 1px solid rgba(23, 48, 79, .08);

    box-shadow: 0 16px 35px rgba(14, 28, 54, .08);

    overflow: hidden;

    transition: .25s;

}

.stat-card:hover {

    transform: translateY(-4px);

    box-shadow: 0 24px 48px rgba(14, 28, 54, .12);

}

.stat-card::before {

    content: '';

    position: absolute;

    top: 0;
    left: 0;

    width: 100%;
    height: 5px;

}

.stat-blue::before {
    background: #17304f;
}

.stat-green::before {
    background: #16a34a;
}

.stat-yellow::before {
    background: #FAAF01;
}

.stat-purple::before {
    background: #7c3aed;
}

.stat-icon {

    width: 58px;
    height: 58px;

    border-radius: 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 28px;

    color: white;

}

.stat-blue .stat-icon {
    background: linear-gradient(135deg, #17304f, #2f5d96);
}

.stat-green .stat-icon {
    background: linear-gradient(135deg, #16a34a, #4ade80);
}

.stat-yellow .stat-icon {
    background: linear-gradient(135deg, #FAAF01, #ffd66b);
    color: #17304f;
}

.stat-purple .stat-icon {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
}

.stat-info {

    flex: 1;

}

.stat-info span {

    display: block;

    color: #6b7280;

    font-size: .76rem;

    text-transform: uppercase;

    letter-spacing: .08em;

}

.stat-info h3 {

    margin-top: 6px;

    font-size: 1.65rem;

    font-weight: 800;

    color: #17304f;

}

.table-card {
    padding: 24px;
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.table-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
}

.table-kicker {
    display: block;
    margin-bottom: 6px;
    color: rgba(23, 48, 79, 0.58);
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.72rem;
}

h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #17304f;
}

.table-count {
    padding: 9px 12px;
    border-radius: 999px;
    background: rgba(250, 175, 1, 0.12);
    color: #996600;
    font-weight: 800;
}

.table-wrap {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-x: contain;
}

.suscripciones-table .v-data-table__wrapper {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

.suscripciones-table .v-data-table__wrapper table {
    width: max-content;
    min-width: 1120px;
    border-collapse: collapse;
}

.suscripciones-table th,
.suscripciones-table td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid rgba(23, 48, 79, 0.08);
}

.suscripciones-table th {
    color: rgba(23, 48, 79, 0.64);
    font-size: 0.84rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.suscripciones-table td {
    color: #17304f;
}

.suscripciones-table tbody tr:hover {
    background: rgba(244, 247, 255, 0.78);
}

@media (max-width: 900px) {
    .table-card {
        padding: 16px;
    }

    .table-head {
        flex-direction: column;
    }

    .table-count {
        align-self: flex-start;
    }
}
</style>
