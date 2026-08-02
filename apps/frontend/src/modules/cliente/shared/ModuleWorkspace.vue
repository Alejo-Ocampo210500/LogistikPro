<template>
  <section class="workspace-shell" :aria-label="`Modulo ${moduleLabel}`">
    <header class="workspace-hero">
      <div>
        <span class="workspace-kicker">Modulo cliente</span>
        <h3>{{ moduleLabel }}</h3>
        <p>{{ moduleSubtitle }}</p>
      </div>
      <div class="workspace-badge">{{ permissionLabel }}</div>
    </header>

    <div class="workspace-grid">
      <article class="workspace-card">
        <h4>Estado del modulo</h4>
        <p>Contenedor base creado y aislado por carpeta. Lista para conectar estados locales, API services y validaciones.</p>
      </article>

      <article class="workspace-card">
        <h4>Checklist de integracion</h4>
        <ul>
          <li>Definir endpoint de lectura y escritura</li>
          <li>Mapear filtros y paginacion</li>
          <li>Configurar feedback de carga y errores</li>
          <li>Conectar permisos finos por accion</li>
        </ul>
      </article>

      <article class="workspace-card workspace-card--wide">
        <h4>Area de trabajo</h4>
        <div class="workspace-canvas">
          <div class="canvas-block"></div>
          <div class="canvas-block"></div>
          <div class="canvas-block"></div>
          <div class="canvas-block"></div>
        </div>
      </article>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ModuleWorkspace',
  props: {
    module: {
      type: Object,
      default: null,
    },
    fallbackTitle: {
      type: String,
      default: 'Modulo sin nombre',
    },
  },
  computed: {
    moduleLabel() {
      return this.module?.label || this.fallbackTitle;
    },
    moduleSubtitle() {
      return this.module?.description || 'Vista base modular sin datos quemados, preparada para desarrollo funcional.';
    },
    permissionLabel() {
      return this.module?.can || 'permiso.pendiente';
    },
  },
};
</script>

<style scoped>
.workspace-shell {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.workspace-hero {
  border-radius: 20px;
  background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 100%);
  border: 1px solid rgba(23, 48, 79, 0.12);
  padding: 20px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.workspace-kicker {
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.68rem;
  color: rgba(23, 48, 79, 0.6);
}

.workspace-hero h3 {
  margin: 8px 0 8px;
  color: #17304f;
  font-size: 1.45rem;
}

.workspace-hero p {
  margin: 0;
  color: rgba(23, 48, 79, 0.74);
  max-width: 760px;
}

.workspace-badge {
  border-radius: 999px;
  border: 1px solid rgba(23, 48, 79, 0.2);
  background: #ffffff;
  color: #17304f;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 8px 12px;
}

.workspace-grid {
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 14px;
}

.workspace-card {
  grid-column: span 6;
  border-radius: 18px;
  border: 1px solid rgba(23, 48, 79, 0.1);
  background: rgba(255, 255, 255, 0.92);
  padding: 16px;
}

.workspace-card--wide {
  grid-column: span 12;
}

.workspace-card h4 {
  margin: 0 0 10px;
  color: #17304f;
  font-size: 1rem;
}

.workspace-card p {
  margin: 0;
  color: rgba(23, 48, 79, 0.74);
  line-height: 1.55;
}

.workspace-card ul {
  margin: 0;
  padding-left: 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  color: rgba(23, 48, 79, 0.82);
}

.workspace-canvas {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.canvas-block {
  min-height: 84px;
  border-radius: 12px;
  border: 1px dashed rgba(23, 48, 79, 0.24);
  background: repeating-linear-gradient(
    45deg,
    rgba(23, 48, 79, 0.05),
    rgba(23, 48, 79, 0.05) 8px,
    rgba(23, 48, 79, 0.08) 8px,
    rgba(23, 48, 79, 0.08) 16px
  );
}

@media (max-width: 900px) {
  .workspace-card {
    grid-column: span 12;
  }
}

@media (max-width: 640px) {
  .workspace-hero {
    flex-direction: column;
  }

  .workspace-canvas {
    grid-template-columns: 1fr;
  }
}
</style>
