<template>
  <main class="client-page">
    <div class="sidebar-overlay" :class="{ 'mobile-open': mobileMenuOpen }" @click="mobileMenuOpen = false"></div>

    <aside class="client-sidebar" :class="{ 'mobile-open': mobileMenuOpen }">
      <button type="button" class="sidebar-close-btn" @click="mobileMenuOpen = false" aria-label="Cerrar menu">
        <i class="mdi mdi-close"></i>
      </button>

      <div class="brand-block">
        <div class="brand-mark">
          <img :src="logo" alt="LogistikPro" class="brand-logo" />
        </div>
        <div>
          <!-- <span class="brand-kicker">MI EMPRESA</span> -->
          <h1>LogistikPro</h1>
          <!-- <small>{{ companyName }}</small> -->
           <!-- <h2 style="font-size: 0.9rem; font-weight: 400; color: rgba(255, 255, 255, 0.72); margin-top: 2px;">Gestion Total Para Tu Negocio</h2> -->
        </div>
      </div>

      <div class="owner-card">
        <span class="owner-kicker">
          <i class="mdi mdi-domain"></i>
          Informacion del negocio
        </span>
        <strong class="owner-name">{{ companyName }}</strong>

        <small class="owner-legal-name">{{ companyLegalName }}</small>

        <div class="owner-meta-grid">
          <div class="owner-meta-item">
            <span class="owner-meta-label">
              <i class="mdi mdi-card-account-details-outline"></i>
              NIT
            </span>
            <strong>{{ companyNit }}</strong>
          </div>

          <div class="owner-meta-item">
            <span class="owner-meta-label">
              <i class="mdi mdi-map-marker-radius-outline"></i>
              Departamento
            </span>
            <strong>{{ companyDepartamento }}</strong>
          </div>

          <div class="owner-meta-item">
            <span class="owner-meta-label">
              <i class="mdi mdi-city-variant-outline"></i>
              Ciudad
            </span>
            <strong>{{ companyCiudad }}</strong>
          </div>
        </div>

        <div class="owner-status-row">
          <span class="owner-meta-label owner-meta-label--status">
            <i class="mdi mdi-check-decagram-outline"></i>
            Estado
          </span>
          <strong :class="['owner-status-pill', companyStatusClass]">{{ companyStatus }}</strong>
        </div>

        <!-- <small class="owner-meta">Empresa ID: {{ companyIdText }}</small> -->
      </div>

      <nav class="sidebar-nav">
        <div v-for="item in filteredMenu" :key="item.id" class="menu-group">
          <button type="button" class="nav-item" :class="{ active: openedMenu === item.id || moduleBelongsTo(item) }"
            @click="toggleMenu(item)">
            <div class="nav-item-main">
              <i :class="['mdi', item.icon || 'mdi-folder-outline']"></i>
              <div>
                <strong>{{ item.label }}</strong>
                <small>{{ item.subtitle }}</small>
              </div>
            </div>

            <div class="nav-item-right">
              <span v-if="item.tag" class="menu-tag">{{ item.tag }}</span>
              <span class="arrow">{{ openedMenu === item.id ? '▼' : '▶' }}</span>
            </div>
          </button>

          <transition name="accordion">
            <div v-if="openedMenu === item.id && item.children && item.children.length" class="submenu">
              <button v-for="child in item.children" :key="child.id" type="button" class="submenu-item"
                :class="{ active: activeModule === child.id }"
                @click="setActiveModule(child.id); mobileMenuOpen = false">
                <span>{{ child.label }}</span>
                <span v-if="child.tag" class="menu-tag small">{{ child.tag }}</span>
              </button>
            </div>
          </transition>
        </div>
      </nav>
    </aside>

    <section class="client-content">
      <header class="client-topbar">
        <div class="topbar-inline">
          <button
            type="button"
            class="mobile-menu-toggle topbar-menu-toggle"
            @click="mobileMenuOpen = true"
            aria-label="Abrir menu"
          >
            <i class="mdi mdi-menu"></i>
          </button>

          <div class="topbar-greeting">
            <h2>Hola, {{ companyName }}</h2>
          </div>

          <button class="logout-button logout-button--compact" type="button" @click="$emit('logout')">
            Cerrar sesion
          </button>
        </div>

      </header>

      <div v-if="isFreeTrial" class="trial-banner">
        <div class="trial-banner-content">
          <div class="trial-icon-wrap">
            <i class="mdi mdi-clock-fast trial-icon"></i>
          </div>
          <div class="trial-text">
            <h4>Estas en prueba gratuita</h4>
            <p>Las pruebas duran 1 mes desde la creacion de tu cuenta. Disfruta de todas las funcionalidades.</p>
          </div>
        </div>
        <div class="trial-countdown">
          <span class="countdown-label">Tiempo restante</span>
          <strong class="countdown-value">{{ trialRemainingText }}</strong>
        </div>
      </div>

      <section class="module-canvas" aria-label="Contenido inicial de cliente">
        <!-- <div class="module-permission-chip">
          <span>Permiso requerido</span>
          <strong>{{ activeModuleCan || 'N/A' }}</strong>
        </div> -->

        <component
          :is="activeModuleComponent"
          :module="activeModulePayload"
          :session="session"
          :module-id="activeModule"
          @start-action="forwardActionLoader"
          @stop-action="forwardStopAction"
        />
      </section>
    </section>
  </main>
</template>

<script>
import logo from '@/assets/branding/logo-logistikpro.png';
import clienteMenu from '@/config/cliente/menu';
import { resolveClientModuleComponent } from '@/modules/cliente';

export default {
  name: 'BlankPageView',

  props: {
    session: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      logo,
      menu: clienteMenu,
      mobileMenuOpen: false,
      openedMenu: null,
      activeModule: null,
      currentTime: new Date(),
      timer: null,
    };
  },
  mounted() {
    this.bootstrapMenuState();

    this.timer = setInterval(() => {
      this.currentTime = new Date();
    }, 1000);
  },

  beforeDestroy() {
    clearInterval(this.timer);
  },

  computed: {
    companyName() {
      return this.session?.empresa?.nombre_comercial || 'Espacio de trabajo';
    },
    companyLegalName() {
      return this.session?.empresa?.razon_social || 'Razon social no registrada';
    },
    companyNit() {
      return this.session?.empresa?.nit || 'No registrado';
    },
    companyDepartamento() {
      return this.session?.empresa?.departamento || this.session?.empresa?.departamento_nombre || 'No registrado';
    },
    companyCiudad() {
      return this.session?.empresa?.ciudad || this.session?.empresa?.ciudad_nombre || 'No registrada';
    },
    companyStatus() {
      const estado = this.session?.empresa?.estado;

      if (estado && typeof estado === 'object') {
        return estado.nombre || 'No definido';
      }

      if (typeof estado === 'string' && estado.trim()) {
        return estado;
      }

      return this.session?.empresa?.estado_plan || this.session?.empresa?.estado_suscripcion || 'No definido';
    },
    companyStatusClass() {
      const value = String(this.companyStatus || '').toLowerCase();

      if (value.includes('activo')) {
        return 'is-active';
      }

      if (value.includes('inactivo') || value.includes('bloque') || value.includes('cancel') || value.includes('suspend')) {
        return 'is-inactive';
      }

      return 'is-neutral';
    },
    companyIdText() {
      return this.session?.empresa_id || this.session?.user?.empresa_id || 'Sin asignar';
    },
    businessPlanName() {
      return this.session?.empresa?.plan || 'Sin plan';
    },
    businessExpiryDate() {
      const rawDate =
        this.session?.empresa?.fecha_vencimiento ||
        this.session?.empresa?.vencimiento ||
        this.session?.empresa?.plan_vencimiento ||
        this.session?.empresa?.suscripcion_vencimiento ||
        null;

      if (!rawDate) {
        return null;
      }

      const parsed = new Date(rawDate);
      return Number.isNaN(parsed.getTime()) ? null : parsed;
    },
    planIsActive() {
      const estado = String(
        this.session?.empresa?.estado_plan ||
        this.session?.empresa?.estado_suscripcion ||
        this.session?.empresa?.estado ||
        ''
      ).toLowerCase();

      if (estado.includes('vencid') || estado.includes('inactiv') || estado.includes('suspend')) {
        return false;
      }

      if (this.businessExpiryDate) {
        const end = new Date(this.businessExpiryDate);
        end.setHours(23, 59, 59, 999);
        return end.getTime() >= Date.now();
      }

      return this.businessPlanName.toLowerCase() !== 'sin plan';
    },
    businessExpiryText() {
      if (!this.businessExpiryDate) {
        return 'Sin fecha registrada';
      }

      return new Intl.DateTimeFormat('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
      }).format(this.businessExpiryDate);
    },
    isSuperadmin() {
      const roleName = String(this.session?.rol?.nombre || '').toLowerCase();
      return roleName === 'superadmin' || roleName === 'superadministrador';
    },
    rawPermissions() {
      const sources = [
        this.session?.user?.permisos,
        this.session?.user?.permissions,
        this.session?.rol?.permisos,
        this.session?.rol?.permissions,
      ];

      const flattened = [];

      sources.forEach(source => {
        if (Array.isArray(source)) {
          source.forEach(item => flattened.push(item));
        }
      });

      return flattened;
    },
    permissionSet() {
      const normalized = this.rawPermissions
        .map(item => {
          if (typeof item === 'string') {
            return item;
          }

          if (item && typeof item === 'object') {
            return item.can || item.clave || item.codigo || item.nombre || item.name || '';
          }

          return '';
        })
        .map(value => String(value || '').trim().toLowerCase())
        .filter(Boolean);

      return new Set(normalized);
    },
    filteredMenu() {
      return this.menu
        .map(item => {
          const children = Array.isArray(item.children)
            ? item.children.filter(child => this.hasPermission(child.can))
            : [];

          const canSeeParent = this.hasPermission(item.can);

          if (!canSeeParent && children.length === 0) {
            return null;
          }

          return {
            ...item,
            children,
          };
        })
        .filter(Boolean);
    },
    activeModuleNode() {
      for (const item of this.filteredMenu) {
        if (!item.children || !item.children.length) {
          if (item.id === this.activeModule) {
            return item;
          }

          continue;
        }

        const found = item.children.find(child => child.id === this.activeModule);

        if (found) {
          return found;
        }
      }

      return null;
    },
    activeModuleLabel() {
      return this.activeModuleNode?.label || 'Sin modulo seleccionado';
    },
    activeModuleCan() {
      return this.activeModuleNode?.can || '';
    },
    activeModuleComponent() {
      return resolveClientModuleComponent(this.activeModule);
    },
    activeModuleDescription() {
      return `Front base del modulo ${this.activeModuleLabel}. Integracion de negocio pendiente por implementar.`;
    },
    activeModulePayload() {
      return {
        ...(this.activeModuleNode || {}),
        description: this.activeModuleDescription,
      };
    },
    isFreeTrial() {
      const planName = String(this.session?.empresa?.plan || '').toLowerCase();
      return planName.includes('prueba') || planName.includes('gratis') || planName.includes('gratuita') || planName.includes('gratuito');
    },
    trialRemainingText() {
      if (!this.session?.empresa?.created_at) {
        return 'Prueba vencida';
      }

      const createdAt = new Date(this.session.empresa.created_at);

      const expirationDate = new Date(createdAt);
      expirationDate.setMonth(expirationDate.getMonth() + 1);

      const diff = expirationDate - this.currentTime;

      if (diff <= 0) {
        return 'Prueba vencida';
      }

      const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
      const horas = Math.floor(
        (diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      const minutos = Math.floor(
        (diff % (1000 * 60 * 60)) / (1000 * 60)
      );
      const segundos = Math.floor(
        (diff % (1000 * 60)) / 1000
      );

      return `${dias}d ${horas}h ${minutos}m ${segundos}s`;
    },
  },

  watch: {
    filteredMenu: {
      immediate: true,
      handler(newMenu) {
        if (!newMenu.length) {
          this.openedMenu = null;
          this.activeModule = null;
          return;
        }

        const activeExists = newMenu.some(item => this.moduleBelongsTo(item));

        if (activeExists) {
          return;
        }

        const first = newMenu[0];
        this.openedMenu = first.id;

        if (first.children && first.children.length) {
          this.activeModule = first.children[0].id;
        } else {
          this.activeModule = first.id;
        }
      },
    },
  },

  methods: {
    bootstrapMenuState() {
      if (!this.filteredMenu.length) {
        return;
      }

      const first = this.filteredMenu[0];
      this.openedMenu = first.id;

      if (!this.activeModule) {
        this.activeModule = first.children && first.children.length ? first.children[0].id : first.id;
      }
    },

    hasPermission(can) {
      if (!can) {
        return true;
      }

      if (this.isSuperadmin) {
        return true;
      }

      if (!this.permissionSet.size) {
        return true;
      }

      const normalizedCan = String(can).toLowerCase();

      if (this.permissionSet.has('*') || this.permissionSet.has('all') || this.permissionSet.has(normalizedCan)) {
        return true;
      }

      const scope = normalizedCan.split('.')[0];
      return this.permissionSet.has(`${scope}.*`);
    },

    toggleMenu(item) {
      if (!item.children || !item.children.length) {
        this.setActiveModule(item.id);
        this.mobileMenuOpen = false;
        return;
      }

      this.openedMenu = this.openedMenu === item.id ? null : item.id;
    },

    moduleBelongsTo(item) {
      if (!item) {
        return false;
      }

      if (item.id === this.activeModule) {
        return true;
      }

      return Array.isArray(item.children) && item.children.some(child => child.id === this.activeModule);
    },

    setActiveModule(moduleId) {
      this.activeModule = moduleId;
    },

    forwardActionLoader(message, callback, duration) {
      this.$emit('start-action', message, callback, duration);
    },

    forwardStopAction() {
      this.$emit('stop-action');
    },
  },
};
</script>

<style scoped>
.client-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 304px minmax(0, 1fr);
  background:
    radial-gradient(circle at top left, rgba(60, 121, 255, 0.14), transparent 24%),
    linear-gradient(180deg, #eef3fb 0%, #e8eef7 100%);
  color: #17304f;
}

.client-sidebar {
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
  gap: 20px;
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

.brand-logo {
  width: 46px;
  height: 46px;
  object-fit: contain;
}

.brand-kicker {
  display: block;
  margin-bottom: 5px;
  font-size: 0.72rem;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(245, 247, 255, 0.62);
}

.brand-block h1,
.client-topbar h2 {
  margin: 0;
}

.brand-block h1 {
  font-size: 1.35rem;
}

.brand-block small {
  color: rgba(245, 247, 255, 0.72);
}

.owner-card {
  padding: 18px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.owner-kicker {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  font-size: 0.68rem;
  color: rgba(245, 247, 255, 0.62);
}

.owner-kicker i {
  font-size: 13px;
  color: rgba(244, 183, 64, 0.92);
}

.owner-name {
  display: block;
  margin: 10px 0 4px;
  font-size: 1rem;
  color: #ffffff;
}

.owner-legal-name {
  display: block;
  margin: 0 0 12px;
  color: rgba(245, 247, 255, 0.7);
  font-size: 0.78rem;
}

.owner-meta-grid {
  display: grid;
  gap: 8px;
}

.owner-meta-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 9px 10px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.owner-meta-item span {
  color: rgba(245, 247, 255, 0.66);
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.owner-meta-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.owner-meta-label i {
  font-size: 14px;
  color: rgba(244, 183, 64, 0.9);
}

.owner-meta-label--status i {
  font-size: 13px;
}

.owner-meta-item strong {
  color: #f8fafc;
  font-size: 0.78rem;
  text-align: right;
}

.owner-status-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-top: 10px;
}

.owner-status-row span {
  color: rgba(245, 247, 255, 0.68);
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.owner-status-pill {
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: capitalize;
}

.owner-status-pill.is-active {
  background: rgba(109, 211, 160, 0.2);
  color: #d2ffe8;
}

.owner-status-pill.is-inactive {
  background: rgba(255, 123, 123, 0.22);
  color: #ffd6d6;
}

.owner-status-pill.is-neutral {
  background: rgba(251, 191, 36, 0.2);
  color: #fef3c7;
}

.owner-meta {
  display: block;
  margin-top: 10px;
  color: rgba(245, 247, 255, 0.7);
  font-size: 0.73rem;
}

.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding-right: 6px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sidebar-nav::-webkit-scrollbar {
  width: 6px;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 20px;
}

.menu-group {
  margin-bottom: 2px;
}

.nav-item {
  width: 100%;
  border: 0;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.05);
  color: #ffffff;
  padding: 12px 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-align: left;
}

.nav-item-main {
  display: flex;
  align-items: center;
  gap: 10px;
}

.nav-item-main i {
  font-size: 20px;
}

.nav-item strong {
  display: block;
  margin-bottom: 4px;
}

.nav-item small {
  color: rgba(245, 247, 255, 0.68);
}

.nav-item.active {
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
}

.nav-item.active small {
  color: rgba(11, 21, 48, 0.8);
}

.nav-item-right {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-left: 8px;
}

.menu-tag {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 999px;
  background: rgba(250, 175, 1, 0.22);
  color: #fef3c7;
  border: 1px solid rgba(251, 191, 36, 0.44);
  font-size: 10px;
  font-weight: 800;
  letter-spacing: .5px;
  text-transform: uppercase;
}

.menu-tag.small {
  font-size: 9px;
  padding: 2px 7px;
}

.arrow {
  font-size: 12px;
}

.submenu {
  margin-left: 16px;
  margin-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.submenu-item {
  border: 0;
  background: transparent;
  color: rgba(255, 255, 255, .76);
  text-align: left;
  padding: 9px 12px;
  border-radius: 10px;
  cursor: pointer;
  transition: .2s;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.submenu-item:hover,
.submenu-item.active {
  background: rgba(255, 255, 255, .09);
  color: white;
}

.client-content {
  padding: 28px;
  min-height: 100vh;
}

.client-topbar {
  display: flex;
  flex-direction: column;
  gap: 0;
  margin: -28px -28px 0;
  position: sticky;
  top: 0;
  z-index: 30;
}

.topbar-inline {
  display: grid;
  grid-template-columns: 44px 1fr auto;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 8px 16px;
  border-radius: 0;
  background: linear-gradient(180deg, #07122f 0%, #09193b 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 8px 20px rgba(9, 25, 59, 0.18);
}

.logout-button {
  border: 0;
  border-radius: 14px;
  padding: 12px 14px;
  font-weight: 800;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.95);
  color: #17304f;
  border: 1px solid rgba(23, 48, 79, 0.12);
}

.topbar-title-block {
  display: flex;
  align-items: center;
  gap: 16px;
  width: 100%;
}

.mobile-menu-toggle {
  display: none;
}

.topbar-menu-toggle {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.06);
  color: #ffffff;
  visibility: hidden;
  pointer-events: none;
}

.topbar-greeting {
  justify-self: center;
  text-align: center;
}

.page-kicker,
.module-kicker {
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.68rem;
  color: rgba(244, 183, 64, 0.88);
}

.client-topbar h2 {
  font-size: clamp(0.95rem, 1.25vw, 1.08rem);
  color: #f8fafc;
  margin-top: 0;
  max-width: min(58vw, 680px);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.topbar-description {
  margin: 0;
  font-size: 0.9rem;
  color: rgba(23, 48, 79, 0.72);
}

.logout-button--compact {
  padding: 7px 11px;
  border-radius: 9px;
  font-weight: 700;
  font-size: 0.78rem;
  line-height: 1;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.24);
  color: #ffffff;
}

.logout-button--compact:hover {
  background: rgba(255, 255, 255, 0.2);
}

.module-canvas {
  margin-top: 18px;
  min-height: calc(100vh - 148px);
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid rgba(23, 48, 79, 0.08);
  box-shadow: 0 22px 60px rgba(14, 28, 54, 0.08);
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.module-permission-chip {
  display: inline-flex;
  flex-direction: column;
  gap: 6px;
  background: #f4f7fc;
  border: 1px solid #e1e8f2;
  border-radius: 14px;
  padding: 12px 14px;
}

.module-permission-chip span {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .9px;
  color: #64748b;
}

.module-permission-chip strong {
  color: #17304f;
  font-size: 13px;
}

.sidebar-overlay {
  display: none;
}

.sidebar-close-btn {
  display: none;
}

.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.24s ease;
}

.accordion-enter,
.accordion-leave-to {
  opacity: 0;
  transform: translateY(-6px);
  max-height: 0;
}

.accordion-enter-to,
.accordion-leave {
  opacity: 1;
  transform: translateY(0);
  max-height: 320px;
}

.trial-banner {
  margin: 16px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
  padding: 16px 24px;
  border-radius: 20px;
  background: linear-gradient(135deg, rgba(234, 179, 8, 0.1) 0%, rgba(245, 158, 11, 0.15) 100%);
  border: 1px solid rgba(245, 158, 11, 0.28);
  box-shadow: 0 10px 30px rgba(245, 158, 11, 0.05);
}

.trial-banner-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.trial-icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: #ffffff;
}

.trial-icon {
  font-size: 24px;
  display: inline-flex;
}

.trial-text h4 {
  margin: 0 0 4px 0;
  font-size: 1.05rem;
  font-weight: 800;
  color: #92400e;
}

.trial-text p {
  margin: 0;
  font-size: 0.9rem;
  color: #b45309;
}

.trial-countdown {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  text-align: right;
}

.countdown-label {
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #b45309;
  font-weight: 700;
  margin-bottom: 2px;
}

.countdown-value {
  font-size: 1.3rem;
  font-weight: 900;
  color: #92400e;
  font-variant-numeric: tabular-nums;
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

@media (max-width: 1024px) {
  .client-page {
    grid-template-columns: 1fr;
  }

  .client-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 300px;
    height: 100vh;
    z-index: 1000;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: none;
  }

  .client-sidebar.mobile-open {
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

  .topbar-inline {
    grid-template-columns: 44px 1fr auto;
    min-height: 54px;
  }

  .topbar-menu-toggle {
    visibility: visible;
    pointer-events: auto;
  }

  .topbar-greeting {
    justify-self: center;
    text-align: center;
  }

  .client-topbar h2 {
    max-width: none;
  }

  .logout-button--compact {
    justify-self: end;
  }

  .module-canvas {
    min-height: calc(100vh - 220px);
  }
}

@media (max-width: 768px) {
  .trial-banner {
    flex-direction: column;
    align-items: stretch;
  }

  .trial-countdown {
    align-items: flex-start;
    text-align: left;
    padding-left: 60px;
  }
}

@media (max-width: 640px) {
  .client-content {
    padding: 18px;
  }

  .client-topbar {
    margin: -18px -18px 0;
  }

  .topbar-inline {
    grid-template-columns: 44px 1fr auto;
    gap: 10px;
    min-height: 50px;
    padding: 8px 12px;
  }

  .topbar-menu-toggle {
    display: none;
  }

  .topbar-greeting {
    grid-column: 2 / 3;
    justify-self: center;
    text-align: center;
  }

  .logout-button--compact {
    grid-column: 2 / 3;
    align-self: center;
  }

  .module-canvas {
    min-height: calc(100vh - 260px);
    padding: 18px;
  }
}
</style>
