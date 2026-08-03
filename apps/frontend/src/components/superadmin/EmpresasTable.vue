<template>
  <section class="table-card">
    <div class="table-head">
      <div>
        <span class="table-kicker">Empresas registradas</span>
        <h2>Control de compañías</h2>
      </div>
      <span class="table-count">{{ empresas.length }} empresas</span>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Empresa</th>
            <th>NIT</th>
            <th>Departamento</th>
            <th>Ciudad</th>
            <th>Plan</th>
            <th>Usuarios</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="empresa in empresas" :key="empresa.id">
            <td>
              <strong>{{ empresa.nombre_comercial }}</strong>
              <small>{{ empresa.razon_social }}</small>
            </td>
            <td>{{ empresa.nit }}</td>
            <td>{{ getDepartamentoNombre(empresa) }}</td>
            <td>{{ getCiudadNombre(empresa) }}</td>
            <td>
              <span :class="['plan-badge', `plan-${empresa.plan}`]">
                {{ formatPlan(empresa.plan) }}
              </span>
            </td>
            <td>{{ empresa.users_count }}</td>
            <td>
              <span :class="['status-pill', getStatusClass(empresa.estado)]">
                {{ empresa.estado ? empresa.estado.nombre : '-' }}
              </span>
            </td>
            <td>
              <div class="row-actions">
                <button type="button" class="action-button" @click="$emit('edit', empresa)" aria-label="Editar empresa"
                  data-action-loader="true">
                  <i class="mdi mdi-pencil"></i>
                  <span class="button-tooltip">Editar</span>
                </button>
                <button type="button" class="action-button action-alt" @click="$emit('supervisor', empresa)"
                  aria-label="Ver supervisor" data-action-loader="true">
                  <i class="mdi mdi-binoculars"></i>
                  <span class="button-tooltip">Supervisor</span>
                </button>
                <button type="button" class="action-button action-password" @click="$emit('change-password', empresa)"
                  aria-label="Cambiar contraseña" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 45%, #1d4ed8 100%); color: white;">
                  <i class="mdi mdi-key-variant"></i>
                  <span class="button-tooltip">Contraseña</span>
                </button>
                <button type="button" :class="[
                  'action-button',
                  empresa.estado ? 'action-disable' : 'action-enable'
                ]" @click="$emit('toggle-status', empresa)"
                  :aria-label="empresa.estado ? 'Inactivar empresa' : 'Activar empresa'">
                  <i :class="empresa.estado ? 'mdi mdi-close-circle-outline' : 'mdi mdi-check-circle-outline'"></i>

                  <span class="button-tooltip">
                    {{ empresa.estado ? 'Inactivar' : 'Activar' }}
                  </span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script>
export default {
  name: 'EmpresasTable',

  props: {
    empresas: {
      type: Array,
      default: () => [],
    },
  },

  methods: {
    getDepartamentoNombre(empresa) {
      return empresa?.departamento || empresa?.departamento_nombre || empresa?.departamento?.nombre || '-';
    },

    getCiudadNombre(empresa) {
      return empresa?.ciudad || empresa?.ciudad_nombre || empresa?.ciudad?.nombre || '-';
    },

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
.status-warning {
  background: rgba(250, 175, 1, 0.16);
  color: #996600;
}

.action-button.action-enable {
  background: #16a34a;
  color: #ffffff;
}

.action-button.action-disable {
  background: #dc2626;
  color: #ffffff;
}

.plan-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 10px;
  border-radius: 999px;
  background: #f8fafc;
  font-weight: 700;
}

.plan-badge::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.plan-basico::before {
  background: #6b7280;
}

.plan-profesional::before {
  background: #2563eb;
}

.plan-empresarial::before {
  background: #10b981;
}

.plan-plataforma::before {
  background: #7c3aed;
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

table {
  width: max-content;
  min-width: 1120px;
  border-collapse: collapse;
}

th,
td {
  padding: 14px 12px;
  text-align: left;
  border-bottom: 1px solid rgba(23, 48, 79, 0.08);
}

th {
  color: rgba(23, 48, 79, 0.64);
  font-size: 0.84rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

td strong,
td small {
  display: block;
}

td small {
  margin-top: 4px;
  color: rgba(23, 48, 79, 0.58);
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

.row-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.action-button {
  border: 0;
  border-radius: 999px;
  padding: 9px 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  position: relative;
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
  font-weight: 800;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
}

.action-button.action-alt {
  background: rgba(11, 21, 48, 0.95);
  color: #ffffff;
}

.action-button:hover {
  transform: translateY(-1px);
}

.action-button i {
  font-size: 18px;
}

.button-tooltip {
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  background: rgba(23, 48, 79, 0.96);
  color: #ffffff;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 0.75rem;
  font-weight: 700;
  pointer-events: none;
  transition: opacity 0.15s ease, visibility 0.15s ease, transform 0.15s ease;
}

.action-button:hover .button-tooltip {
  opacity: 1;
  visibility: visible;
  transform: translateX(-50%) translateY(-4px);
}

@media (max-width: 900px) {
  .table-card {
    padding: 16px;
  }

  .table-head {
    flex-direction: column;
  }

  h2 {
    font-size: 1.24rem;
  }

  .table-count {
    align-self: flex-start;
  }
}
</style>
