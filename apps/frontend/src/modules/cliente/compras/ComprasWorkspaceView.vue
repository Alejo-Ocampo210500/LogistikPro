<template>
  <ModuleWorkspace :module="moduleWithDescription" :fallback-title="'Compras'" />
</template>

<script>
import ModuleWorkspace from '@/modules/cliente/shared/ModuleWorkspace.vue';

export default {
  name: 'ComprasWorkspaceView',
  components: {
    ModuleWorkspace,
  },
  props: {
    module: {
      type: Object,
      default: null,
    },
  },
  computed: {
    moduleWithDescription() {
      const base = this.module || {};
      return {
        ...base,
        description: base.description || 'Espacio profesional del modulo Compras. Disenado para conectar procesos, formularios y reportes del cliente.',
      };
    },
  },
};
</script>
