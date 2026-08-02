<template>
  <section class="dashboard-shell" aria-label="Dashboard profesional de cliente">
    <header class="dashboard-hero">
      <div class="hero-copy">
        <span class="hero-kicker">Centro de Operaciones</span>
        <h3>Centro de Gestión Empresarial</h3>
        <p>
          Supervisa la operación de tu empresa desde un único lugar. Accede a indicadores,
          ventas, inventario, caja y actividad operativa con información centralizada y
          preparada para análisis en tiempo real.
        </p>
      </div>
      <div class="hero-toolbar">
        <!-- <button type="button" class="toolbar-btn">
          <i class="mdi mdi-calendar-range"></i>
          <span>Rango de fechas</span>
        </button> -->
        <button type="button" class="toolbar-btn toolbar-btn--strong">
          <i class="mdi mdi-chart-line"></i>
          <span>Ver Reportes</span>
        </button>
      </div>
    </header>

    <section class="kpi-grid" aria-label="Indicadores base">
      <article v-for="kpi in kpis" :key="kpi.key" class="kpi-card">
        <div class="kpi-head">
          <span>{{ kpi.label }}</span>
          <i :class="['mdi', kpi.icon]"></i>
        </div>
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-line"></div>
      </article>
    </section>

    <section class="dashboard-grid">
      <article class="panel panel--trend">
        <div class="panel-head">
          <h4>Ventas por periodo</h4>
          <span>Dia / semana / mes</span>
        </div>
        <div class="trend-canvas">
          <div class="trend-axis trend-axis--y">
            <span v-for="n in 5" :key="`axis-y-${n}`"></span>
          </div>
          <div class="trend-area">
            <div class="trend-line"></div>
            <div class="trend-columns">
              <span v-for="n in 12" :key="`col-${n}`" :style="{ '--h': `${18 + ((n * 13) % 58)}%` }"></span>
            </div>
          </div>
        </div>
      </article>

      <article class="panel panel--mix">
        <div class="panel-head">
          <h4>Canales de venta</h4>
          <span>Composicion comercial</span>
        </div>
        <div class="mix-content">
          <div class="donut"></div>
          <div class="legend-list">
            <div v-for="n in 4" :key="`legend-${n}`" class="legend-item">
              <span class="legend-dot"></span>
              <div class="skeleton skeleton-line"></div>
            </div>
          </div>
        </div>
      </article>

      <article class="panel panel--alerts">
        <div class="panel-head">
          <h4>Alertas y prioridades</h4>
          <span>Cola operativa</span>
        </div>
        <div class="alerts-list">
          <div v-for="n in 6" :key="`alert-${n}`" class="alert-row">
            <span class="alert-pulse"></span>
            <div class="alert-copy">
              <div class="skeleton skeleton-line"></div>
              <div class="skeleton skeleton-short"></div>
            </div>
          </div>
        </div>
      </article>

      <article class="panel panel--table">
        <div class="panel-head">
          <h4>Ultimas ventas</h4>
          <span>Tabla</span>
        </div>
        <div class="table-shell">
          <div class="table-row table-row--head">
            <span v-for="n in 5" :key="`head-${n}`"></span>
          </div>
          <div v-for="n in 7" :key="`body-${n}`" class="table-row">
            <span v-for="m in 5" :key="`cell-${n}-${m}`"></span>
          </div>
        </div>
      </article>
    </section>
  </section>
</template>

<script>
export default {
  name: 'DashboardResumenView',
  data() {
    return {
      kpis: [
        { key: 'sales-day', label: 'Ventas del dia', icon: 'mdi-calendar-today-outline' },
        { key: 'sales-week', label: 'Ventas de la semana', icon: 'mdi-calendar-week-outline' },
        { key: 'sales-month', label: 'Ventas del mes', icon: 'mdi-calendar-month-outline' },
        { key: 'profit', label: 'Utilidad', icon: 'mdi-chart-line' },
        { key: 'top-products', label: 'Producto mas vendido', icon: 'mdi-star-outline' },
        { key: 'low-stock', label: 'Productos con poco stock', icon: 'mdi-alert-outline' },
        { key: 'cash-status', label: 'Estado de caja', icon: 'mdi-safe-square-outline' },
        { key: 'pending-alerts', label: 'Alertas importantes', icon: 'mdi-bell-alert-outline' },
      ],
    };
  },
};
</script>

<style scoped>
.dashboard-shell {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.dashboard-hero {
  border-radius: 22px;
  border: 1px solid rgba(23, 48, 79, 0.12);
  background:
    radial-gradient(circle at 88% 6%, rgba(244, 183, 64, 0.24), transparent 34%),
    linear-gradient(120deg, #ffffff 0%, #f4f8ff 100%);
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
}

.hero-kicker {
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.68rem;
  color: rgba(23, 48, 79, 0.6);
}

.hero-copy h3 {
  margin: 8px 0 8px;
  color: #17304f;
  font-size: clamp(1.35rem, 2.2vw, 1.8rem);
}

.hero-copy p {
  margin: 0;
  color: rgba(23, 48, 79, 0.74);
  max-width: 760px;
}

.hero-toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.toolbar-btn {
  border: 1px solid rgba(23, 48, 79, 0.18);
  border-radius: 12px;
  background: #ffffff;
  color: #17304f;
  font-weight: 800;
  height: 42px;
  padding: 0 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.toolbar-btn--strong {
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  border-color: rgba(185, 122, 10, 0.38);
  color: #0b1530;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.kpi-card {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  border: 1px solid rgba(23, 48, 79, 0.14);
  background:
    radial-gradient(circle at 88% 0%, rgba(244, 183, 64, 0.22), transparent 46%),
    linear-gradient(150deg, rgba(255, 255, 255, 0.96) 0%, rgba(245, 250, 255, 0.94) 100%);
  box-shadow: 0 10px 20px rgba(23, 48, 79, 0.07);
  padding: 14px;
  transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
}

.kpi-card::before {
  content: '';
  position: absolute;
  inset: 0 auto 0 0;
  width: 4px;
  border-radius: 16px 0 0 16px;
  background: linear-gradient(180deg, #f4b740 0%, #d99210 100%);
}

.kpi-card::after {
  content: '';
  position: absolute;
  left: 14px;
  right: 14px;
  bottom: 0;
  height: 2px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(23, 48, 79, 0), rgba(23, 48, 79, 0.2), rgba(23, 48, 79, 0));
  opacity: 0.45;
}

.kpi-card:hover {
  transform: translateY(-2px);
  border-color: rgba(23, 48, 79, 0.24);
  box-shadow: 0 14px 26px rgba(23, 48, 79, 0.11);
}

.kpi-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  color: rgba(23, 48, 79, 0.86);
  font-size: 0.84rem;
  font-weight: 800;
}

.kpi-head i {
  width: 30px;
  height: 30px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: rgba(23, 48, 79, 0.82);
  background: linear-gradient(140deg, rgba(23, 48, 79, 0.08), rgba(23, 48, 79, 0.18));
  font-size: 18px;
}

.kpi-card:nth-child(2n)::before {
  background: linear-gradient(180deg, #2b5f99 0%, #17304f 100%);
}

.kpi-card:nth-child(3n)::before {
  background: linear-gradient(180deg, #0ea6a6 0%, #0d7f7f 100%);
}

.kpi-card:nth-child(4n)::before {
  background: linear-gradient(180deg, #6582aa 0%, #395779 100%);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 12px;
}

.panel {
  border-radius: 18px;
  border: 1px solid rgba(23, 48, 79, 0.11);
  background: #ffffff;
  padding: 14px;
}

.panel--trend {
  grid-column: span 8;
}

.panel--mix {
  grid-column: span 4;
}

.panel--alerts {
  grid-column: span 4;
}

.panel--table {
  grid-column: span 8;
}

.panel-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  gap: 10px;
}

.panel-head h4 {
  margin: 0;
  color: #17304f;
  font-size: 0.98rem;
}

.panel-head span {
  color: rgba(23, 48, 79, 0.62);
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 800;
}

.trend-canvas {
  min-height: 250px;
  border-radius: 12px;
  border: 1px dashed rgba(23, 48, 79, 0.18);
  background: linear-gradient(180deg, #f8fbff 0%, #eff5fd 100%);
  display: grid;
  grid-template-columns: 54px 1fr;
  padding: 14px;
  gap: 10px;
}

.trend-axis {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.trend-axis span {
  height: 1px;
  width: 100%;
  background: rgba(23, 48, 79, 0.2);
}

.trend-area {
  position: relative;
  display: flex;
  align-items: flex-end;
}

.trend-line {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 36%;
  height: 3px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(217, 146, 16, 0.2), rgba(244, 183, 64, 0.9), rgba(217, 146, 16, 0.2));
}

.trend-columns {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 8px;
  align-items: end;
}

.trend-columns span {
  height: var(--h);
  min-height: 18px;
  border-radius: 8px 8px 3px 3px;
  background: linear-gradient(180deg, rgba(244, 183, 64, 0.9) 0%, rgba(217, 146, 16, 0.82) 100%);
}

.mix-content {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.donut {
  width: 164px;
  height: 164px;
  margin: 0 auto;
  border-radius: 50%;
  background: conic-gradient(rgba(244, 183, 64, 0.95) 0deg 104deg,
      rgba(23, 48, 79, 0.8) 104deg 220deg,
      rgba(126, 152, 190, 0.8) 220deg 300deg,
      rgba(23, 48, 79, 0.24) 300deg 360deg);
  position: relative;
}

.donut::after {
  content: '';
  position: absolute;
  inset: 24px;
  border-radius: 50%;
  background: #ffffff;
}

.legend-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #d99210;
}

.alerts-list {
  display: flex;
  flex-direction: column;
  gap: 9px;
}

.alert-row {
  border-radius: 10px;
  border: 1px solid rgba(23, 48, 79, 0.08);
  padding: 9px;
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.alert-pulse {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-top: 5px;
  background: #f4b740;
  box-shadow: 0 0 0 0 rgba(244, 183, 64, 0.45);
  animation: pulse 1.8s infinite;
}

.alert-copy {
  flex: 1;
}

.table-shell {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.table-row {
  display: grid;
  grid-template-columns: 1.2fr 1fr 0.9fr 0.9fr 0.7fr;
  gap: 8px;
}

.table-row span {
  height: 11px;
  border-radius: 6px;
  background: rgba(23, 48, 79, 0.12);
}

.table-row--head span {
  height: 9px;
  background: rgba(23, 48, 79, 0.24);
}

.skeleton {
  border-radius: 7px;
  background: linear-gradient(90deg, rgba(23, 48, 79, 0.1) 0%, rgba(23, 48, 79, 0.22) 50%, rgba(23, 48, 79, 0.1) 100%);
  background-size: 220% 100%;
  animation: shimmer 1.85s linear infinite;
}

.skeleton-title {
  height: 18px;
  width: 68%;
  background: linear-gradient(90deg, rgba(23, 48, 79, 0.16) 0%, rgba(23, 48, 79, 0.3) 50%, rgba(23, 48, 79, 0.16) 100%);
}

.skeleton-line {
  margin-top: 8px;
  height: 9px;
  width: 92%;
  background: linear-gradient(90deg, rgba(23, 48, 79, 0.14) 0%, rgba(23, 48, 79, 0.24) 50%, rgba(23, 48, 79, 0.14) 100%);
}

.skeleton-short {
  margin-top: 8px;
  height: 8px;
  width: 48%;
}

@keyframes shimmer {
  0% {
    background-position: 100% 0;
  }

  100% {
    background-position: -100% 0;
  }
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(244, 183, 64, 0.5);
  }

  75% {
    box-shadow: 0 0 0 8px rgba(244, 183, 64, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(244, 183, 64, 0);
  }
}

@media (max-width: 1160px) {
  .kpi-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .panel--trend,
  .panel--mix,
  .panel--alerts,
  .panel--table {
    grid-column: span 12;
  }
}

@media (max-width: 700px) {
  .dashboard-hero {
    flex-direction: column;
  }

  .kpi-grid {
    grid-template-columns: 1fr;
  }

  .trend-canvas {
    grid-template-columns: 1fr;
  }

  .trend-axis {
    display: none;
  }

  .table-row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (prefers-reduced-motion: reduce) {
  .kpi-card,
  .skeleton,
  .alert-pulse {
    animation: none !important;
    transition: none !important;
  }
}
</style>
