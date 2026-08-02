<template>
    <section class="dashboard-shell">
        <div class="dashboard-top-panel">
            <div class="hero-panel">
                <div class="hero-copy">
                    <span class="hero-kicker">Panel de control</span>
                    <h1>Bienvenido de nuevo, {{ user.nombre }} {{ user.apellido }}</h1>
                    <p>
                        Esta es la vista principal de LogistikPro. Controla empresas, planes, usuarios y auditorías
                        desde un
                        dashboard profesional diseñado para decisiones rápidas.
                    </p>
                </div>

                <div class="hero-actions">
                    <button type="button" class="hero-button" @click="$emit('navigate', 'empresas-listado')">
                        Ver empresas
                    </button>
                    <button type="button" class="hero-button hero-button--secondary"
                        @click="$emit('navigate', 'empresas-planes')">
                        Administrar planes
                    </button>
                </div>
            </div>

            <div class="metrics-row">
                <article class="metric-card metric-card--teal">
                    <div class="metric-card-top">
                        <span class="metric-label">Empresas activas</span>
                        <i class="mdi mdi-check-circle-outline metric-icon"></i>
                    </div>
                    <strong>{{ estadisticas.empresas_activas || 0 }}</strong>
                    <p>Compañías operando sin incidencias.</p>
                </article>

                <article class="metric-card metric-card--blue">
                    <div class="metric-card-top">
                        <span class="metric-label">Total empresas</span>
                        <i class="mdi mdi-domain metric-icon"></i>
                    </div>
                    <strong>{{ estadisticas.empresas_total || 0 }}</strong>
                    <p>Registro completo de empresas activas e inactivas.</p>
                </article>

                <article class="metric-card metric-card--purple">
                    <div class="metric-card-top">
                        <span class="metric-label">Usuarios</span>
                        <i class="mdi mdi-account-group-outline metric-icon"></i>
                    </div>
                    <strong>{{ estadisticas.usuarios_total || 0 }}</strong>
                    <p>Cuentas conectadas y permisos asignados.</p>
                </article>

                <article class="metric-card metric-card--amber">
                    <div class="metric-card-top">
                        <span class="metric-label">Superadmins</span>
                        <i class="mdi mdi-shield-account-outline metric-icon"></i>
                    </div>
                    <strong>{{ estadisticas.superadmins_total || 0 }}</strong>
                    <p>Usuarios con control completo de la plataforma.</p>
                </article>
            </div>
        </div>

        <div class="dashboard-body">
            <div class="dashboard-charts">
                <article class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <span class="dashboard-kicker">Rendimiento</span>
                            <h2>Visión rápida</h2>
                        </div>
                        <span class="badge">Actualizado ahora</span>
                    </div>

                    <div class="chart-bar-row">
                        <span>Empresas activas</span>
                        <div class="chart-bar">
                            <div class="chart-bar-fill chart-bar-fill--primary"
                                :style="barWidth(estadisticas.empresas_activas)"></div>
                        </div>
                        <strong>{{ estadisticas.empresas_activas || 0 }}</strong>
                    </div>

                    <div class="chart-bar-row">
                        <span>Empresas totales</span>
                        <div class="chart-bar">
                            <div class="chart-bar-fill chart-bar-fill--secondary"
                                :style="barWidth(estadisticas.empresas_total)"></div>
                        </div>
                        <strong>{{ estadisticas.empresas_total || 0 }}</strong>
                    </div>

                    <div class="chart-bar-row">
                        <span>Usuarios</span>
                        <div class="chart-bar">
                            <div class="chart-bar-fill chart-bar-fill--tertiary"
                                :style="barWidth(estadisticas.usuarios_total)"></div>
                        </div>
                        <strong>{{ estadisticas.usuarios_total || 0 }}</strong>
                    </div>

                    <div class="chart-bar-row">
                        <span>Superadmins</span>
                        <div class="chart-bar">
                            <div class="chart-bar-fill chart-bar-fill--accent"
                                :style="barWidth(estadisticas.superadmins_total)"></div>
                        </div>
                        <strong>{{ estadisticas.superadmins_total || 0 }}</strong>
                    </div>
                </article>

                <article class="status-card">
                    <div class="status-card-header">
                        <span class="dashboard-kicker">Acceso rápido</span>
                        <h2>Módulos clave</h2>
                    </div>

                    <div class="status-actions">
                        <button type="button" class="status-button" @click="$emit('navigate', 'empresas-listado')">
                            Empresas
                        </button>
                        <button type="button" class="status-button" @click="$emit('navigate', 'empresas-planes')">
                            Planes
                        </button>
                        <button type="button" class="status-button" @click="$emit('navigate', 'auditoria')">
                            Auditoría
                        </button>
                        <button type="button" class="status-button"
                            @click="$emit('navigate', 'empresas-suscripciones')">
                            Suscripciones
                        </button>
                    </div>

                    <div class="status-notes">
                        <p>El panel centraliza las decisiones y permite navegar rápido al módulo necesario sin perder
                            contexto.</p>
                    </div>
                </article>
            </div>

            <article class="info-card">
                <div class="info-card-header">
                    <span class="dashboard-kicker">Resumen de la plataforma</span>
                    <h2>Estado actual</h2>
                </div>

                <div class="platform-grid compact-grid">
                    <article class="platform-card platform-card--blue compact-card">
                        <div class="platform-card-header">
                            <span>Empresas activas</span>
                            <strong>{{ estadisticas.empresas_activas || 0 }}</strong>
                        </div>
                        <p class="platform-card-copy">Activas</p>
                    </article>

                    <article class="platform-card platform-card--teal compact-card">
                        <div class="platform-card-header">
                            <span>Total empresas</span>
                            <strong>{{ estadisticas.empresas_total || 0 }}</strong>
                        </div>
                        <p class="platform-card-copy">Registradas</p>
                    </article>

                    <article class="platform-card platform-card--indigo compact-card">
                        <div class="platform-card-header">
                            <span>Usuarios</span>
                            <strong>{{ estadisticas.usuarios_total || 0 }}</strong>
                        </div>
                        <p class="platform-card-copy">Activos</p>
                    </article>

                    <article class="platform-card platform-card--amber compact-card">
                        <div class="platform-card-header">
                            <span>Superadmins</span>
                            <strong>{{ estadisticas.superadmins_total || 0 }}</strong>
                        </div>
                        <p class="platform-card-copy">Administradores</p>
                    </article>
                </div>

                <p class="info-copy">
                    El panel centraliza la gestión de la plataforma y presenta indicadores clave en tiempo real. Accede
                    rápidamente a empresas, planes, suscripciones y auditorías desde los accesos directos para
                    administrar el ecosistema de LogistikPro de forma eficiente.

                </p>
            </article>
        </div>
    </section>
</template>

<script>
export default {
    name: 'InicioSuperAdmin',

    props: {
        user: {
            type: Object,
            required: true,
        },
        estadisticas: {
            type: Object,
            default: () => ({
                empresas_total: 0,
                empresas_activas: 0,
                usuarios_total: 0,
                superadmins_total: 0,
            }),
        },
    },

    methods: {
        barWidth(value) {
            const max = Math.max(
                this.estadisticas.empresas_total,
                this.estadisticas.empresas_activas,
                this.estadisticas.usuarios_total,
                this.estadisticas.superadmins_total,
                1,
            );
            return { width: `${Math.max(18, Math.round((value / max) * 100))}%` };
        },
    },
};
</script>

<style scoped>
.dashboard-shell {
    display: grid;
    gap: 24px;
}

.dashboard-top-panel {
    display: grid;
    gap: 22px;
}

.hero-panel {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 32px;
    border-radius: 32px;
    background: linear-gradient(135deg, #0d2145 0%, #122b61 100%);
    color: #ffffff;
    box-shadow: 0 28px 90px rgba(15, 34, 65, 0.18);
    align-items: center;
}

.hero-copy {
    max-width: 58%;
}

.hero-kicker {
    display: inline-block;
    margin-bottom: 14px;
    color: rgba(255, 255, 255, 0.72);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.78rem;
}

.hero-panel h1 {
    margin: 0;
    font-size: clamp(2rem, 3vw, 3.2rem);
    line-height: 1.05;
}

.hero-panel p {
    margin: 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 1rem;
    line-height: 1.75;
}

.hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
}

.hero-button {
    padding: 14px 22px;
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
    color: #0b1530;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.hero-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 20px 30px rgba(212, 147, 35, 0.2);
}

.hero-button--secondary {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.metrics-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
}

.metric-card {
    padding: 18px;
    border-radius: 22px;
    background: #ffffff;
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 16px 30px rgba(15, 34, 65, 0.08);
    display: grid;
    gap: 12px;
    min-height: 150px;
}

.metric-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

.metric-label {
    color: rgba(23, 48, 79, 0.72);
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.75rem;
    font-weight: 700;
}

.metric-icon {
    font-size: 1.15rem;
    color: rgba(23, 48, 79, 0.38);
}

.metric-card strong {
    font-size: 2.3rem;
    color: #0f172a;
}

.metric-card p {
    margin: 0;
    color: rgba(23, 48, 79, 0.7);
    line-height: 1.6;
}

.metric-card--teal {
    border-color: rgba(14, 165, 233, 0.2);
}

.metric-card--blue {
    border-color: rgba(37, 99, 235, 0.2);
}

.metric-card--purple {
    border-color: rgba(79, 70, 229, 0.2);
}

.metric-card--amber {
    border-color: rgba(245, 158, 11, 0.2);
}

.dashboard-body {
    display: grid;
    grid-template-columns: 1.4fr 0.9fr;
    gap: 18px;
}

.dashboard-charts,
.status-card,
.info-card,
.chart-card {
    display: grid;
    gap: 18px;
}

.chart-card,
.status-card,
.info-card {
    padding: 28px;
    border-radius: 28px;
    background: #ffffff;
    border: 1px solid rgba(23, 48, 79, 0.08);
    box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.chart-card-header,
.status-card-header,
.info-card-header {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: flex-start;
}

.chart-card-header h2,
.status-card-header h2,
.info-card-header h2 {
    margin: 0;
    font-size: 1.45rem;
    color: #17304f;
}

.badge {
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.12);
    color: #1d4ed8;
    font-weight: 700;
    font-size: 0.84rem;
}

.chart-bar-row {
    display: grid;
    grid-template-columns: 1.2fr 1.6fr auto;
    align-items: center;
    gap: 16px;
}

.chart-bar-row span {
    color: #334155;
    white-space: nowrap;
}

.chart-bar {
    width: 100%;
    height: 14px;
    background: rgba(226, 232, 240, 0.8);
    border-radius: 999px;
    overflow: hidden;
}

.chart-bar-fill {
    height: 100%;
    border-radius: 999px;
}

.chart-bar-fill--primary {
    background: #2563eb;
}

.chart-bar-fill--secondary {
    background: #0ea5e9;
}

.chart-bar-fill--tertiary {
    background: #22c55e;
}

.chart-bar-fill--accent {
    background: #f59e0b;
}

.chart-bar-row strong {
    color: #0f172a;
    font-weight: 700;
}

.status-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.status-button {
    width: 100%;
    padding: 16px 18px;
    border: 1px solid rgba(23, 48, 79, 0.12);
    border-radius: 18px;
    background: #f8fafc;
    color: #102a43;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.status-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 30px rgba(15, 34, 65, 0.08);
}

.status-notes p,
.info-copy {
    margin: 0;
    color: rgba(23, 48, 79, 0.72);
    line-height: 1.8;
}

.platform-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(240px, 1fr));
    gap: 18px;
    margin-top: 16px;
}

.platform-card {
    padding: 18px;
    border-radius: 22px;
    background: #ffffff;
    border: 1px solid rgba(23, 48, 79, 0.1);
    box-shadow: 0 18px 38px rgba(15, 34, 65, 0.08);
    display: grid;
    gap: 10px;
    position: relative;
    overflow: hidden;
    min-height: 150px;
}

.compact-grid {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
}

.compact-card {
    min-height: 140px;
}

.platform-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 6px;
    background: rgba(23, 48, 79, 0.08);
}

.platform-card--blue::before {
    background: linear-gradient(90deg, rgba(37, 99, 235, 1), rgba(37, 99, 235, 0.4));
}

.platform-card--teal::before {
    background: linear-gradient(90deg, rgba(14, 165, 233, 1), rgba(14, 165, 233, 0.4));
}

.platform-card--indigo::before {
    background: linear-gradient(90deg, rgba(79, 70, 229, 1), rgba(79, 70, 229, 0.4));
}

.platform-card--amber::before {
    background: linear-gradient(90deg, rgba(245, 158, 11, 1), rgba(245, 158, 11, 0.4));
}

.platform-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.platform-card-header span {
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.78rem;
}

.platform-card-header strong {
    font-size: 2.4rem;
    line-height: 1;
    color: #0f172a;
}

.platform-card-copy {
    margin: 0;
    color: rgba(23, 48, 79, 0.72);
    line-height: 1.6;
    font-size: 0.95rem;
}

.platform-card-tag {
    display: inline-flex;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.platform-card-tag.platform-card-tag--blue {
    background: rgba(37, 99, 235, 0.12);
    color: #1d4ed8;
}

.platform-card-tag.platform-card-tag--teal {
    background: rgba(14, 165, 233, 0.12);
    color: #0369a1;
}

.platform-card-tag.platform-card-tag--indigo {
    background: rgba(79, 70, 229, 0.12);
    color: #4338ca;
}

.platform-card-tag.platform-card-tag--amber {
    background: rgba(245, 158, 11, 0.14);
    color: #92400e;
}

.info-card strong {
    display: block;
    font-size: 1.9rem;
    color: #17304f;
}

.info-card strong {
    display: block;
    font-size: 1.9rem;
    color: #17304f;
}

@media (max-width: 1024px) {

    .dashboard-body,
    .summary-cards {
        grid-template-columns: 1fr;
    }

    .hero-panel {
        flex-direction: column;
        align-items: stretch;
    }

    .hero-copy {
        max-width: 100%;
    }
}

@media (max-width: 720px) {
    .hero-actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .hero-actions button {
        width: 100%;
    }

    .status-actions,
    .info-grid {
        grid-template-columns: 1fr;
    }

    .hero-panel,
    .summary-card,
    .chart-card,
    .status-card,
    .info-card {
        padding: 22px;
    }
}

@media (max-width: 600px) {
    .chart-bar-row {
        grid-template-columns: 1fr auto;
        gap: 8px;
    }

    .chart-bar {
        grid-column: span 2;
        order: 3;
        margin-top: 4px;
    }
}

@media (max-width: 480px) {
    .compact-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
