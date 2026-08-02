<template>
  <section class="detail-card">
    <div v-if="!detail" class="empty-state">
      <strong>Selecciona una empresa</strong>
      <p>Escoge una compañía desde el listado para ver su detalle completo.</p>
    </div>

    <template v-else>
      <div class="detail-head">
        <div>
          <span class="detail-kicker">Supervisor</span>
          <h2>{{ detail.empresa.nombre_comercial }}</h2>
          <p>{{ detail.empresa.razon_social }}</p>
        </div>
        <span :class="['status-pill', getStatusClass(detail.empresa.estado)]">
          {{ detail.empresa.estado ? detail.empresa.estado.nombre : '-' }}
        </span>
      </div>

      <div class="stats-grid">
        <article>
          <span>Usuarios</span>
          <strong>{{ detail.estadisticas.usuarios_total }}</strong>
        </article>
        <article>
          <span>Activos</span>
          <strong>{{ detail.estadisticas.usuarios_activos }}</strong>
        </article>
        <article>
          <span>Inactivos</span>
          <strong>{{ detail.estadisticas.usuarios_inactivos }}</strong>
        </article>
        <article>
          <span>Plan</span>
          <strong>{{ formatPlan(detail.empresa.plan) }}</strong>
        </article>
      </div>

      <div class="info-grid">
        <article>
          <span>NIT</span>
          <strong>{{ detail.empresa.nit }}</strong>
        </article>
        <article>
          <span>Correo</span>
          <strong>{{ detail.empresa.email || 'No registrado' }}</strong>
        </article>
        <article>
          <span>Ciudad</span>
          <strong>{{ detail.empresa.ciudad || 'No registrada' }}</strong>
        </article>
        <article>
          <span>Departamento</span>
          <strong>{{ detail.empresa.departamento || 'No registrado' }}</strong>
        </article>
      </div>

      <div class="section-title">
        <span>Módulos habilitados</span>
      </div>

      <div class="modules-grid">
        <article
          v-for="modulo in detail.modulos"
          :key="modulo.codigo"
          :class="['module-card', modulo.habilitado ? 'enabled' : 'disabled']"
        >
          <strong>{{ modulo.nombre }}</strong>
          <p>{{ modulo.descripcion }}</p>
          <small>{{ modulo.habilitado ? 'Habilitado' : 'No habilitado' }}</small>
        </article>
      </div>

      <div class="section-title">
        <span>Usuarios de la empresa</span>
      </div>

      <div class="users-table-wrap">
        <table>
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="usuario in detail.usuarios" :key="usuario.id">
              <td>{{ usuario.nombre }} {{ usuario.apellido }}</td>
              <td>{{ usuario.email }}</td>
              <td>{{ usuario.rol ? usuario.rol.nombre : '-' }}</td>
              <td>
                <span :class="['status-pill', getStatusClass(usuario.estado)]">
                  {{ usuario.estado ? usuario.estado.nombre : '-' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </section>
</template>

<script>
export default {
  name: 'EmpresaSupervisorDetalle',

  props: {
    detail: {
      type: Object,
      default: null,
    },
  },

  methods: {
    formatPlan(value) {
      if (!value) {
        return '-';
      }

      return value.charAt(0).toUpperCase() + value.slice(1);
    },

    getStatusClass(estado) {
      if (!estado) return 'status-off';
      const name = String(estado.nombre).toLowerCase();
      if (name === 'activo') return 'status-ok';
      if (name === 'inactivo' || name === 'bloqueado' || name === 'cancelada') return 'status-off';
      return 'status-warning';
    },
  },
};
</script>

<style scoped>
.detail-card {
  padding: 24px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(23, 48, 79, 0.08);
  box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.empty-state {
  min-height: 360px;
  display: grid;
  place-items: center;
  text-align: center;
  color: rgba(23, 48, 79, 0.72);
}

.detail-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
}

.detail-kicker,
.section-title span {
  display: block;
  margin-bottom: 6px;
  color: rgba(23, 48, 79, 0.58);
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
}

.detail-head h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #17304f;
}

.detail-head p {
  margin: 8px 0 0;
  color: rgba(23, 48, 79, 0.72);
}

.stats-grid,
.info-grid,
.modules-grid {
  display: grid;
  gap: 14px;
}

.stats-grid {
  grid-template-columns: repeat(4, minmax(0, 1fr));
  margin-bottom: 18px;
}

.stats-grid article,
.info-grid article {
  padding: 16px;
  border-radius: 18px;
  background: rgba(248, 250, 253, 0.96);
  border: 1px solid rgba(23, 48, 79, 0.08);
}

.stats-grid span,
.info-grid span {
  display: block;
  margin-bottom: 8px;
  color: rgba(23, 48, 79, 0.58);
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.7rem;
}

.stats-grid strong,
.info-grid strong {
  color: #17304f;
}

.info-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-bottom: 18px;
}

.modules-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-bottom: 22px;
}

.module-card {
  padding: 16px;
  border-radius: 18px;
  border: 1px solid rgba(23, 48, 79, 0.08);
}

.module-card.enabled {
  background: rgba(109, 211, 160, 0.12);
}

.module-card.disabled {
  background: rgba(255, 123, 123, 0.08);
}

.module-card strong {
  display: block;
  margin-bottom: 8px;
}

.module-card p {
  margin: 0;
  color: rgba(23, 48, 79, 0.72);
  line-height: 1.55;
}

.module-card small {
  display: block;
  margin-top: 10px;
  font-weight: 800;
}

.users-table-wrap {
  overflow: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 12px 10px;
  border-bottom: 1px solid rgba(23, 48, 79, 0.08);
  text-align: left;
}

th {
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(23, 48, 79, 0.64);
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 84px;
  padding: 8px 12px;
  border-radius: 999px;
  font-weight: 800;
}

.status-ok {
  background: rgba(109, 211, 160, 0.18);
  color: #186843;
}

.status-off {
  background: rgba(255, 123, 123, 0.16);
  color: #9b2f2f;
}

.status-warning {
  background: rgba(250, 175, 1, 0.16);
  color: #996600;
}

@media (max-width: 900px) {
  .stats-grid,
  .info-grid,
  .modules-grid {
    grid-template-columns: 1fr;
  }

  .detail-head {
    flex-direction: column;
  }
}
</style>
