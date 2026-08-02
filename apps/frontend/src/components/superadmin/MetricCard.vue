<template>
  <article class="metric-card">
    <span class="metric-label">{{ label }}</span>
    <strong class="metric-value">{{ value }}</strong>
    <small v-if="hint" class="metric-hint">{{ hint }}</small>
  </article>
</template>

<script>
export default {
  name: 'MetricCard',

  props: {
    label: {
      type: String,
      required: true,
    },
    value: {
      type: [String, Number],
      required: true,
    },
    hint: {
      type: String,
      default: '',
    },
  },
};
</script>

<style scoped>
.metric-card {
  padding: 18px 20px;
  border-radius: 22px;
  background: linear-gradient(180deg, rgba(10, 22, 52, 0.94), rgba(5, 14, 33, 0.98));
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.18);
}

.metric-label {
  display: block;
  margin-bottom: 10px;
  color: rgba(245, 247, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
}

.metric-value {
  display: block;
  font-size: 2rem;
  line-height: 1;
  color: #ffffff;
}

.metric-hint {
  display: block;
  margin-top: 8px;
  color: rgba(245, 247, 255, 0.68);
  line-height: 1.5;
}
</style>
