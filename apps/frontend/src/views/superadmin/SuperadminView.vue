<template>
  <main class="superadmin-page">
    <!-- Overlay for mobile drawer -->
    <div class="sidebar-overlay" :class="{ 'mobile-open': mobileMenuOpen }" @click="mobileMenuOpen = false"></div>

    <aside class="sidebar" :class="{ 'mobile-open': mobileMenuOpen }">
      <!-- Close button for mobile -->
      <button type="button" class="sidebar-close-btn" @click="mobileMenuOpen = false" aria-label="Cerrar menú">
        <i class="mdi mdi-close"></i>
      </button>

      <div class="brand-block">
        <div class="brand-mark">
          <img :src="logo" alt="LogistikPro" />
        </div>
        <div>
          <span class="brand-kicker">Superadmin</span>
          <h1>LogistikPro</h1>
          <small>Panel central de la plataforma</small>
        </div>
      </div>

      <div class="owner-card">
        <span>Sesión activa</span>
        <strong>{{ sessionName }}</strong>
        <small>{{ sessionCompany }}</small>
      </div>

      <nav class="sidebar-nav">

        <div v-for="item in menu" :key="item.id" class="menu-group">

          <button type="button" class="nav-item" @click="
            item.children.length
              ? openedMenu = openedMenu === item.id ? null : item.id
              : (setActiveModule(item.id), mobileMenuOpen = false)
            ">
            <div>
              <strong>{{ item.label }}</strong>
              <small>{{ item.subtitle }}</small>
            </div>

            <span class="arrow">
              {{ openedMenu === item.id ? '▼' : '▶' }}
            </span>
          </button>

          <transition name="accordion">
            <div v-if="openedMenu === item.id && item.children.length" class="submenu">
              <button v-for="child in item.children" :key="child.id" class="submenu-item"
                @click="setActiveModule(child.id); mobileMenuOpen = false">
                {{ child.label }}
              </button>
            </div>
          </transition>

        </div>

      </nav>
    </aside>

    <section class="content">
      <header class="topbar">
        <div class="topbar-title-block">
          <button type="button" class="mobile-menu-toggle" @click="mobileMenuOpen = true" aria-label="Abrir menú">
            <i class="mdi mdi-menu"></i>
          </button>
          <div>
            <span class="page-kicker">Panel superadmin</span>
            <h2>Control global de LogistikPro</h2>
            <p>Administra empresas, crea nuevas cuentas y supervisa el detalle completo por compañía.</p>
          </div>
        </div>

        <div class="topbar-actions">
          <button class="reload-button" type="button" :disabled="loading || refreshing" @click="reloadPanel"
            data-action-loader="true">
            <span v-if="refreshing" class="button-inline">
              <span class="button-spinner" aria-hidden="true"></span>
              Actualizando panel...
            </span>
            <span v-else>Recargar panel</span>
          </button>

          <button class="logout-button" type="button" @click="$emit('logout')" data-action-loader="true">Cerrar
            sesión</button>
        </div>
      </header>

      <div v-if="message" :class="['flash', messageType]">
        {{ message }}
      </div>

      <v-dialog v-model="confirmDialog" max-width="520px" persistent>
        <v-card class="confirm-card">
          <v-card-title class="confirm-title">
            <div>
              <span class="confirm-kicker">Confirmar acción</span>
              <h3>{{ pendingAction === 'activate' ? 'Activar empresa' : 'Desactivar empresa' }}</h3>
            </div>
          </v-card-title>

          <v-card-text>
            <p>
              ¿Deseas {{ pendingAction === 'activate' ? 'activar' : 'desactivar' }} la empresa
              <strong>"{{ pendingEmpresa ? pendingEmpresa.nombre_comercial : '' }}"</strong>?
            </p>
          </v-card-text>

          <v-card-actions class="confirm-actions">
            <button type="button" class="secondary-button" @click="cancelChangeCompanyStatus" :disabled="saving">
              Cancelar
            </button>
            <button type="button" class="submit-button" @click="confirmChangeCompanyStatus" :disabled="saving"
              data-action-loader="true">
              Aceptar
            </button>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="changePasswordDialog" max-width="520px" persistent>
        <v-card class="confirm-card">
          <v-card-title class="confirm-title">
            <div>
              <span class="confirm-kicker">Seguridad</span>
              <h3>Cambiar contraseña del administrador</h3>
            </div>
          </v-card-title>

          <v-card-text style="padding-top: 10px;">
            <p style="margin-bottom: 20px;">
              Ingresa la nueva contraseña para el administrador de la empresa
              <strong>"{{ selectedPasswordEmpresa ? selectedPasswordEmpresa.nombre_comercial : '' }}"</strong>.
            </p>

            <div v-if="pwdValidationMessage" class="flash error"
              style="margin-bottom: 20px; padding: 12px; border-radius: 12px; background: rgba(255, 123, 123, 0.16); color: #9b2f2f; font-size: 0.9rem; font-weight: 700;">
              {{ pwdValidationMessage }}
            </div>

            <div class="password-change-field">
              <label class="password-change-label">Nueva contraseña</label>
              <div class="password-change-input-wrap">
                <span class="password-lock-badge" aria-hidden="true">
                  <i class="mdi mdi-lock-outline"></i>
                </span>
                <input v-model="newPassword" :type="showNewPassword ? 'text' : 'password'"
                  placeholder="Mínimo 8 caracteres, 1 número, 1 mayúscula y 1 minúscula"
                  class="password-change-input" />
                <button type="button" class="password-change-toggle" @click="showNewPassword = !showNewPassword"
                  :aria-label="showNewPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                  <i :class="showNewPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
                </button>
              </div>
              <div class="password-rules-row">
                <span class="rule-chip">8+ caracteres</span>
                <span class="rule-chip">1 mayúscula</span>
                <span class="rule-chip">1 número</span>
              </div>
            </div>
          </v-card-text>

          <v-card-actions class="confirm-actions">
            <button type="button" class="secondary-button" @click="closeChangePasswordDialog" :disabled="saving">
              Cancelar
            </button>
            <button type="button" class="submit-button" @click="submitChangePassword" :disabled="saving"
              data-action-loader="true">
              Guardar contraseña
            </button>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <div v-if="loading" class="loading-box">Cargando información del panel...</div>

      <section v-else class="module-shell">
        <template v-if="activeModule === 'inicio'">
          <InicioSuperAdmin :user="session.user" :estadisticas="panel.estadisticas" @navigate="setActiveModule" />
        </template>

        <template v-else-if="activeModule === 'empresas-suscripciones'">
          <div class="module-toolbar">
            <div>
              <h3>Suscripciones</h3>
            </div>
          </div>

          <SuscripcionesEmpresa :user="session.user" />
        </template>

        <template v-else-if="activeModule === 'usuarios-globales'">
          <div class="module-toolbar">
            <div>
              <h3>Usuarios Globales</h3>
            </div>
          </div>

          <UsuariosGlobales :user="session.user" @start-action="forwardActionLoader" @stop-action="forwardStopAction"
            @success="showMessage('success', $event)" @error="showMessage('error', $event)" />
        </template>

        <template v-else-if="activeModule === 'empresas-pagos'">
          <div class="module-toolbar">
            <div>
              <h3>Pagos</h3>
            </div>
          </div>

          <PagosEmpresa :user="session.user" @start-action="forwardActionLoader" @stop-action="forwardStopAction" @payment-updated="loadPanel" />
        </template>

        <template v-else-if="activeModule === 'empresas-planes'">
          <div class="module-toolbar">
            <div>
              <h3>Planes y Membresias</h3>
            </div>
          </div>

          <PlanesEmpresa :planes="panel.planes" :estados="estados" @start-action="forwardActionLoader"
            @stop-action="forwardStopAction" @plan-created="handlePlanCreated" @plan-updated="handlePlanUpdated" />
        </template>

        <template v-else-if="activeModule === 'empresas-listado'">
          <div class="module-toolbar">
            <div>
              <h3>Listado de empresas</h3>
            </div>
          </div>

          <EmpresasTable :empresas="panel.empresas" @edit="openEditModule" @supervisor="openSupervisorModule"
            @toggle-status="cambiarEstadoEmpresa" @change-password="openChangePassword" />
        </template>

        <template v-else-if="activeModule === 'empresas-crear'">
          <div class="module-toolbar">
            <div>
              <h3>{{ selectedEmpresa ? 'Editar empresa' : 'Crear empresa' }}</h3>
            </div>

            <button v-if="selectedEmpresa" type="button" class="secondary-button" @click="startNewCompany">
              Nueva empresa
            </button>
          </div>

          <EmpresaForm ref="empresaForm" :mode="selectedEmpresa ? 'editar' : 'crear'" :empresa="selectedEmpresa"
            :loading="saving" :planes="panel.planes" :estados="estados" @submit="saveCompany"
            @start-action="forwardActionLoader" @stop-action="forwardStopAction" />
        </template>

        <template v-else-if="activeModule === 'empresas-editar'">
          <div class="module-toolbar">
            <div>
              <h3>Editar empresa</h3>
            </div>
            <button type="button" class="action-back-button" @click="closeEditModule">
              <i class="mdi mdi-arrow-left"></i>
              <span>Volver</span>
            </button>
          </div>

          <template v-if="selectedEmpresa">
            <EmpresaForm ref="empresaForm" mode="editar" :empresa="selectedEmpresa" :loading="saving"
              :planes="panel.planes" :estados="estados" @submit="saveCompany" @start-action="forwardActionLoader"
              @stop-action="forwardStopAction" />
          </template>

          <template v-else-if="panel.empresas.length">
            <div class="form-select-card">
              <label for="empresaEditSelect">Selecciona la empresa que deseas editar</label>
              <select id="empresaEditSelect" v-model="selectedEditEmpresaId"
                @change="selectEmpresaToEdit(selectedEditEmpresaId)">
                <option value="" disabled>Elegir empresa...</option>
                <option v-for="empresa in panel.empresas" :key="empresa.id" :value="empresa.id">
                  {{ empresa.nombre_comercial }}
                </option>
              </select>
            </div>
          </template>

          <template v-else>
            <div class="empty-module">Aún no hay empresas registradas para editar.</div>
          </template>
        </template>

        <template v-else-if="activeModule === 'auditoria'">
          <div class="module-toolbar">
            <div>
              <h3>Auditoría</h3>
            </div>
            <button type="button" class="action-back-button" @click="closeAuditModule">
              <i class="mdi mdi-arrow-left"></i>
              <span>Volver</span>
            </button>
          </div>

          <template v-if="supervisorDetail">
            <EmpresaSupervisorDetalle :detail="supervisorDetail" />
          </template>
          <template v-else>
            <div class="empty-module">Selecciona una empresa desde el listado para ver su detalle de auditoría.</div>
          </template>
        </template>

        <template v-else-if="activeModule === 'logs'">
          <div class="module-toolbar">
            <div>
              <h3>Logs del sistema</h3>
            </div>
          </div>

          <div class="empty-module">Este módulo aún no tiene contenido disponible.</div>
        </template>

        <template v-else-if="activeModule === 'estadisticas'">
          <div class="module-toolbar">
            <div>
              <h3>Estadísticas globales</h3>
            </div>
          </div>

          <div class="empty-module">Este módulo aún no tiene contenido disponible.</div>
        </template>

        <template v-else-if="activeModule === 'soporte'">
          <div class="module-toolbar">
            <div>
              <h3>Tickets y soporte</h3>
            </div>
          </div>

          <div class="empty-module">Este módulo aún no tiene contenido disponible.</div>
        </template>

        <template v-else>
          <div class="module-toolbar">
            <div>
              <h3>Sin módulo activo</h3>
            </div>
          </div>

          <div class="empty-module">Ningún contenido para este módulo.</div>
        </template>
      </section>
    </section>
  </main>
</template>

<script>
import api from '@/services/api';
import menu from '@/config/superadmin/menu';
import logo from '@/assets/branding/isotipo-logistikpro.png';
import EmpresaForm from '@/components/superadmin/EmpresaForm.vue';
import EmpresasTable from '@/components/superadmin/EmpresasTable.vue';
import EmpresaSupervisorDetalle from '@/components/superadmin/EmpresaSupervisorDetalle.vue';
import InicioSuperAdmin from '@/components/superadmin/InicioSuperAdmin.vue';
import SuscripcionesEmpresa from '@/components/empresas/SuscripcionesEmpresa.vue';
import PlanesEmpresa from '@/components/mantenimiento/PlanesEmpresa.vue';
import UsuariosGlobales from '@/components/mantenimiento/UsuariosGlobales.vue';
import PagosEmpresa from '@/components/empresas/PagosEmpresa.vue';

export default {
  name: 'SuperadminView',

  components: {
    EmpresaForm,
    EmpresasTable,
    EmpresaSupervisorDetalle,
    InicioSuperAdmin,
    SuscripcionesEmpresa,
    PlanesEmpresa,
    UsuariosGlobales,
    PagosEmpresa
  },

  props: {
    session: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      mobileMenuOpen: false,
      openedMenu: null,
      logo,
      menu,
      activeModule: 'inicio',
      loading: true,
      refreshing: false,
      saving: false,
      message: '',
      messageType: 'info',
      messageTimer: null,
      selectedEmpresa: null,
      selectedEditEmpresaId: '',
      selectedSupervisorEmpresaId: null,
      supervisorDetail: null,
      confirmDialog: false,
      pendingEmpresa: null,
      pendingAction: '',
      changePasswordDialog: false,
      selectedPasswordEmpresa: null,
      newPassword: '',
      showNewPassword: false,
      pwdValidationMessage: '',
      estados: [],
      panel: {
        estadisticas: {
          empresas_total: 0,
          empresas_activas: 0,
          usuarios_total: 0,
          superadmins_total: 0,
        },
        empresas: [],
        planes: [],
      },
    };
  },

  computed: {
    sessionName() {
      return this.session && this.session.user ? `${this.session.user.nombre} ${this.session.user.apellido}` : 'Superadmin';
    },

    sessionCompany() {
      return this.session && this.session.empresa ? this.session.empresa.nombre_comercial : 'LogistikPro Central';
    },
  },

  mounted() {
    this.loadPanel();
  },

  beforeDestroy() {
    if (this.messageTimer) {
      window.clearTimeout(this.messageTimer);
    }
  },

  methods: {
    cambiarEstadoEmpresa(empresa) {
      this.pendingEmpresa = empresa;
      this.pendingAction = empresa.estado ? 'deactivate' : 'activate';
      this.confirmDialog = true;
    },

    openChangePassword(empresa) {
      this.selectedPasswordEmpresa = empresa;
      this.newPassword = '';
      this.showNewPassword = false;
      this.pwdValidationMessage = '';
      this.changePasswordDialog = true;
    },

    closeChangePasswordDialog() {
      this.changePasswordDialog = false;
      this.selectedPasswordEmpresa = null;
      this.newPassword = '';
      this.showNewPassword = false;
      this.pwdValidationMessage = '';
    },

    async submitChangePassword() {
      if (!this.newPassword) {
        this.pwdValidationMessage = 'La contraseña es obligatoria.';
        return;
      }
      if (this.newPassword.length < 8) {
        this.pwdValidationMessage = 'La contraseña debe tener al menos 8 caracteres.';
        return;
      }
      if (!/[0-9]/.test(this.newPassword)) {
        this.pwdValidationMessage = 'La contraseña debe contener al menos un número.';
        return;
      }
      if (!/[A-Z]/.test(this.newPassword)) {
        this.pwdValidationMessage = 'La contraseña debe contener al menos una letra mayúscula.';
        return;
      }
      if (!/[a-z]/.test(this.newPassword)) {
        this.pwdValidationMessage = 'La contraseña debe contener al menos una letra minúscula.';
        return;
      }

      this.saving = true;
      this.forwardActionLoader('Actualizando contraseña...');

      try {
        await api.put(`/superadmin/empresas/${this.selectedPasswordEmpresa.id}/cambiar-password`, {
          password: this.newPassword
        });
        this.showMessage('success', 'Contraseña del administrador actualizada correctamente.');
        this.closeChangePasswordDialog();
      } catch (error) {
        this.pwdValidationMessage = this.resolveError(error);
      } finally {
        this.saving = false;
        this.forwardStopAction();
      }
    },

    cancelChangeCompanyStatus() {
      this.confirmDialog = false;
      this.pendingEmpresa = null;
      this.pendingAction = '';
    },

    async confirmChangeCompanyStatus() {
      if (!this.pendingEmpresa) {
        this.cancelChangeCompanyStatus();
        return;
      }

      const action = this.pendingAction === 'activate' ? 'activar' : 'desactivar';
      const successMessage = this.pendingAction === 'activate'
        ? 'Empresa activada correctamente.'
        : 'Empresa desactivada correctamente.';

      this.confirmDialog = false;
      this.forwardActionLoader(`${action.charAt(0).toUpperCase() + action.slice(1)} empresa...`);

      try {
        await api.put(`/superadmin/empresas/${this.pendingEmpresa.id}/cambiar-estado`);
        await this.loadPanel();
        this.showMessage('success', successMessage);
      } catch (error) {
        this.showMessage('error', this.resolveError(error));
      } finally {
        this.pendingEmpresa = null;
        this.pendingAction = '';
        this.forwardStopAction();
      }
    },
    clearMessageTimer() {
      if (this.messageTimer) {
        window.clearTimeout(this.messageTimer);
        this.messageTimer = null;
      }
    },

    showMessage(type, text) {
      this.clearMessageTimer();
      this.messageType = type;
      this.message = text;
      this.messageTimer = window.setTimeout(() => {
        this.message = '';
        this.messageTimer = null;
      }, 3000);
    },
    setActiveModule(moduleId) {
      this.activeModule = moduleId;
      this.message = '';
      this.clearMessageTimer();

      if (moduleId === 'empresas-crear') {
        this.selectedEmpresa = null;
        this.selectedEditEmpresaId = '';
      }

      if (moduleId === 'empresas-listado') {
        this.selectedEmpresa = null;
        this.selectedEditEmpresaId = '';
      }

      if (moduleId === 'empresas-editar' && !this.selectedEmpresa) {
        this.showMessage('info', 'Selecciona una empresa desde el listado para editarla.');
      }

      if (moduleId === 'auditoria' && !this.selectedSupervisorEmpresaId && this.panel.empresas.length > 0) {
        this.selectedSupervisorEmpresaId = this.panel.empresas[0].id;
        this.loadSupervisorById(this.selectedSupervisorEmpresaId);
      }
    },

    async loadPanel() {
      return this.loadPanelWithMode(false);
    },

    async reloadPanel() {
      if (this.loading || this.refreshing) {
        return;
      }

      this.startGlobalReload();
    },

    async loadPanelWithMode(refreshing) {
      if (refreshing) {
        this.refreshing = true;
      } else {
        this.loading = true;
      }

      this.message = '';

      try {
        const { data } = await api.get('/superadmin/panel');
        this.panel = data;

        const { data: estadosData } = await api.get('/estados');
        this.estados = estadosData;

        if (this.panel.empresas.length > 0 && !this.selectedSupervisorEmpresaId) {
          this.selectedSupervisorEmpresaId = this.panel.empresas[0].id;
        }
      } catch (error) {
        this.showMessage('error', this.resolveError(error));
      } finally {
        if (refreshing) {
          this.refreshing = false;
        } else {
          this.loading = false;
        }
      }
    },

    async loadSupervisorById(empresaId) {
      if (!empresaId) {
        this.supervisorDetail = null;
        return;
      }

      try {
        const { data } = await api.get(`/superadmin/empresas/${empresaId}`);
        this.supervisorDetail = data;
      } catch (error) {
        this.showMessage('error', this.resolveError(error));
      }
    },

    startGlobalReload() {
      this.refreshing = true;
      window.setTimeout(() => {
        window.location.reload();
      }, 1800);
    },

    startNewCompany() {
      this.selectedEmpresa = null;
      this.activeModule = 'empresas-crear';
    },

    openEditModule(empresa) {
      this.selectedEmpresa = empresa;
      this.activeModule = 'empresas-editar';
    },

    async openSupervisorModule(empresa) {
      this.selectedSupervisorEmpresaId = empresa.id;
      this.activeModule = 'auditoria';
      await this.loadSupervisorById(empresa.id);
    },

    closeEditModule() {
      this.activeModule = 'empresas-listado';
      this.selectedEmpresa = null;
      this.selectedEditEmpresaId = '';
      this.message = '';
    },

    closeAuditModule() {
      this.activeModule = 'empresas-listado';
      this.selectedSupervisorEmpresaId = null;
      this.supervisorDetail = null;
      this.message = '';
    },

    selectEmpresaToEdit(empresaId) {
      const empresa = this.panel.empresas.find(item => item.id === empresaId);
      this.selectedEmpresa = empresa || null;
      this.selectedEditEmpresaId = empresaId || null;
    },

    resolveError(error) {
      if (error.response && error.response.data && error.response.data.errors) {
        const errors = Object.values(error.response.data.errors).reduce((carry, current) => carry.concat(current), []);
        return errors[0] || 'No se pudo completar la operación.';
      }

      if (error.response && error.response.data && error.response.data.mensaje) {
        return error.response.data.mensaje;
      }

      return 'No se pudo completar la operación.';
    },

    forwardActionLoader(message, callback, duration) {
      this.$emit('start-action', message, callback, duration);
    },

    forwardStopAction() {
      this.$emit('stop-action');
    },

    async handlePlanCreated() {
      await this.loadPanel();
      this.showMessage('success', 'Plan creado correctamente.');
    },

    async handlePlanUpdated() {
      await this.loadPanel();
      this.showMessage('success', 'Plan actualizado correctamente.');
    },

    async saveCompany(form) {
      this.saving = true;
      this.message = '';

      const successMessage = form.id
        ? 'Empresa actualizada correctamente.'
        : 'Empresa creada correctamente.';

      try {
        if (form.id) {
          await api.put(`/superadmin/empresas/${form.id}`, form);
          this.selectedEmpresa = null;
        } else {
          await api.post('/superadmin/empresas', form);
          if (this.$refs.empresaForm) {
            this.$refs.empresaForm.reset();
          }
        }

        await this.loadPanel();
        this.showMessage('success', successMessage);
        this.activeModule = 'empresas-listado';
      } catch (error) {
        this.showMessage('error', this.resolveError(error));
      } finally {
        this.saving = false;
        this.$emit('stop-action');
      }
    },
  },
};
</script>

<style scoped>
.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding-right: 6px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sidebar {
  min-height: 100vh;
  height: 100vh;
  position: sticky;
  top: 0;
  align-self: stretch;
  overflow: hidden;
  padding: 28px 22px;
  background: linear-gradient(180deg, #07122f 0%, #09193b 100%);
  color: #ffffff;
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.sidebar-nav::-webkit-scrollbar {
  width: 6px;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, .15);
  border-radius: 20px;
}

.menu-group {
  margin-bottom: 10px;
}

.nav-item {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.submenu {
  overflow: hidden;
  padding-left: 14px;
  margin-top: 8px;
}

.submenu-item {
  width: 100%;
  border: none;
  background: transparent;
  color: rgba(255, 255, 255, .75);
  padding: 10px 14px;
  text-align: left;
  border-radius: 10px;
  cursor: pointer;
  transition: .2s;
}

.submenu-item:hover {
  background: rgba(255, 255, 255, .08);
  color: white;
}

.arrow {
  font-size: 12px;
}

.submenu {
  margin-left: 16px;
  margin-top: 6px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.submenu-item {
  border: 0;
  background: transparent;
  color: rgba(255, 255, 255, .75);
  text-align: left;
  padding: 8px 12px;
  border-radius: 10px;
  cursor: pointer;
  transition: .2s;
}

.submenu-item:hover {
  background: rgba(255, 255, 255, .08);
  color: white;
}

.superadmin-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 290px minmax(0, 1fr);
  background:
    radial-gradient(circle at top left, rgba(60, 121, 255, 0.15), transparent 26%),
    linear-gradient(180deg, #eef3fb 0%, #e8eef7 100%);
  color: #17304f;
}

.brand-block {
  display: flex;
  align-items: center;
  gap: 14px;
}

.brand-mark {
  width: 62px;
  height: 62px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.06);
  display: grid;
  place-items: center;
}

.brand-mark img {
  width: 46px;
  height: 46px;
  object-fit: contain;
}

.brand-kicker,
.page-kicker,
.owner-card span,
.module-kicker {
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: rgba(245, 247, 255, 0.62);
}

.brand-block h1,
.topbar h2 {
  margin: 0;
}

.brand-block h1 {
  font-size: 1.35rem;
}

.owner-card {
  padding: 18px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.owner-card strong {
  display: block;
  margin: 8px 0 4px;
}

.owner-card small {
  color: rgba(245, 247, 255, 0.7);
}

.nav-item,
.logout-button,
.secondary-button {
  border: 0;
  border-radius: 14px;
  padding: 12px 14px;
  font-weight: 800;
  cursor: pointer;
}

.nav-item {
  background: rgba(255, 255, 255, 0.05);
  color: #ffffff;
  text-align: left;
}

.nav-item strong {
  display: block;
  margin-bottom: 4px;
}

.nav-item small {
  color: rgba(245, 247, 255, 0.66);
}

.nav-item.active {
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
}

.nav-danger {
  background: rgba(255, 123, 123, 0.12);
  color: #ffd1d1;
}

.nav-danger small {
  color: rgba(255, 209, 209, 0.82);
}

.content {
  padding: 28px;
  min-height: 100vh;
}

.topbar {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 22px;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.topbar h2 {
  font-size: clamp(2rem, 3vw, 2.8rem);
  color: #17304f;
  margin-top: 6px;
}

.topbar p {
  margin: 8px 0 0;
  color: rgba(23, 48, 79, 0.72);
}

.logout-button {
  background: rgba(255, 255, 255, 0.95);
  color: #17304f;
  border: 1px solid rgba(23, 48, 79, 0.12);
}

.reload-button {
  border: 0;
  border-radius: 14px;
  padding: 12px 16px;
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 16px 28px rgba(244, 183, 64, 0.22);
}

.secondary-button {
  background: rgba(23, 48, 79, 0.08);
  color: #17304f;
}

.button-inline {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.button-spinner {
  width: 16px;
  height: 16px;
  border-radius: 999px;
  border: 2px solid rgba(11, 21, 48, 0.24);
  border-top-color: #0b1530;
  animation: spin 0.8s linear infinite;
}

.password-change-field {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.password-change-label {
  font-weight: 700;
  color: #17304f;
}

.password-change-input-wrap {
  display: flex;
  align-items: center;
  border: 1px solid rgba(23, 48, 79, 0.14);
  border-radius: 14px;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(248, 250, 253, 0.98) 0%, rgba(239, 245, 252, 0.98) 100%);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.password-lock-badge {
  width: 44px;
  height: 44px;
  margin-left: 4px;
  border-radius: 11px;
  background: rgba(23, 48, 79, 0.08);
  color: #17304f;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.password-change-input {
  flex: 1 1 auto;
  min-width: 0;
  height: 50px;
  padding: 0 12px;
  border: 0;
  background: transparent;
  color: #17304f;
  outline: none;
}

.password-change-toggle {
  width: 44px;
  height: 44px;
  margin-right: 4px;
  border: 0;
  border-radius: 11px;
  background: rgba(23, 48, 79, 0.08);
  color: #17304f;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: none;
}

.password-change-toggle i {
  font-size: 1.08rem;
}

.password-change-toggle:hover {
  background: rgba(23, 48, 79, 0.08);
  transform: none !important;
}

.password-rules-row {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.rule-chip {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 999px;
  background: rgba(250, 175, 1, 0.14);
  color: #7b5200;
  font-size: 0.76rem;
  font-weight: 800;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 18px;
}

.module-shell {
  display: grid;
  gap: 18px;
}

.module-toolbar {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 20px 24px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(23, 48, 79, 0.08);
  box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.module-toolbar h3 {
  margin: 0;
  font-size: 1.4rem;
  color: #17304f;
}

.module-toolbar p {
  margin: 8px 0 0;
  color: rgba(23, 48, 79, 0.72);
}

.module-selector select {
  min-width: 240px;
  height: 50px;
  padding: 0 14px;
  border-radius: 14px;
  border: 1px solid rgba(23, 48, 79, 0.14);
  background: rgba(248, 250, 253, 0.96);
  color: #17304f;
  outline: none;
}

button {
  transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}

button:hover {
  transform: translateY(-1px);
}

button:active {
  transform: translateY(0);
}

.action-back-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border: 1px solid rgba(23, 48, 79, 0.12);
  border-radius: 999px;
  padding: 10px 16px;
  background: rgba(255, 255, 255, 0.98);
  color: #17304f;
  font-weight: 800;
  cursor: pointer;
}

.action-back-button:hover {
  background: #f4f7fb;
  box-shadow: 0 8px 20px rgba(23, 48, 79, 0.08);
}

.action-back-button i {
  font-size: 18px;
}

.audit-subtitle {
  margin-top: 6px;
  color: rgba(23, 48, 79, 0.64);
  font-size: 0.95rem;
}

.loading-box {
  padding: 20px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(23, 48, 79, 0.08);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Sidebar Overlay */
.sidebar-overlay {
  display: none;
}

/* Sidebar close button */
.sidebar-close-btn {
  display: none;
}

/* Mobile menu toggle */
.mobile-menu-toggle {
  display: none;
}

@media (max-width: 1024px) {
  .superadmin-page {
    grid-template-columns: 1fr !important;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 290px;
    height: 100vh;
    z-index: 1000;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: none;
  }

  .sidebar.mobile-open {
    transform: translateX(0);
    box-shadow: 10px 0 30px rgba(0, 0, 0, 0.4);
  }

  .sidebar-overlay {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(4, 11, 28, 0.6);
    backdrop-filter: blur(4px);
    z-index: 999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  .sidebar-overlay.mobile-open {
    opacity: 1;
    pointer-events: auto;
  }

  .sidebar-close-btn {
    display: grid;
    place-items: center;
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: #ffffff;
    cursor: pointer;
    z-index: 10;
  }

  .sidebar-close-btn i {
    font-size: 20px;
  }

  .mobile-menu-toggle {
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(23, 48, 79, 0.12);
    color: #17304f;
    cursor: pointer;
    flex-shrink: 0;
  }

  .mobile-menu-toggle i {
    font-size: 24px;
  }

  .topbar-title-block {
    display: flex;
    align-items: center;
    gap: 16px;
    width: 100%;
  }

  .metrics-grid {
    grid-template-columns: 1fr;
  }

  .module-toolbar {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }
}

@media (max-width: 640px) {
  .content {
    padding: 18px;
  }

  .topbar {
    flex-direction: column;
  }

  .module-selector select {
    min-width: 100%;
  }
}

.form-select-card {
  padding: 22px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(23, 48, 79, 0.08);
  margin-top: 18px;
}

.confirm-card {
  border-radius: 24px;
  overflow: hidden;
  background: #ffffff;
  box-shadow: 0 24px 60px rgba(15, 34, 65, 0.16);
}

.confirm-title {
  padding: 28px 24px 0;
  gap: 16px;
}

.confirm-kicker {
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #8f9bb3;
  margin-bottom: 8px;
}

.confirm-card h3 {
  margin: 0;
  font-size: 1.5rem;
  color: #10243e;
}

.confirm-card p {
  margin: 0;
  color: #334155;
  line-height: 1.65;
}

.confirm-card strong {
  font-weight: 700;
}

.confirm-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px 24px;
}

.confirm-actions .secondary-button {
  min-width: 120px;
  background: rgba(23, 48, 79, 0.08);
  color: #17304f;
}

.confirm-actions .submit-button {
  min-width: 120px;
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
}

.form-select-card label {
  display: block;
  margin-bottom: 10px;
  color: #17304f;
  font-weight: 700;
}

.form-select-card select {
  width: 100%;
  min-width: 240px;
  height: 50px;
  padding: 0 14px;
  border-radius: 14px;
  border: 1px solid rgba(23, 48, 79, 0.18);
  background: rgba(248, 250, 253, 0.96);
  color: #17304f;
}
</style>
