<template>
  <ModuleWorkspace :module="moduleWithDescription" :fallback-title="'Reportes'" />
</template>

<script>
import ModuleWorkspace from '@/modules/cliente/shared/ModuleWorkspace.vue';

export default {
  name: 'ReportesWorkspaceView',
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
        description: base.description || 'Espacio profesional del modulo Reportes. Disenado para conectar procesos, formularios y reportes del cliente.',
      };
    },
  },
};
</script>
