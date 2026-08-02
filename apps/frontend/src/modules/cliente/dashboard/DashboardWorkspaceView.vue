<template>
  <ModuleWorkspace :module="moduleWithDescription" :fallback-title="'Dashboard'" />
</template>

<script>
import ModuleWorkspace from '@/modules/cliente/shared/ModuleWorkspace.vue';

export default {
  name: 'DashboardWorkspaceView',
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
        description: base.description || 'Espacio profesional del modulo Dashboard. Disenado para conectar procesos, formularios y reportes del cliente.',
      };
    },
  },
};
</script>
