<template>
  <span :class="badgeClass">
    <span :class="dotClass"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue';

const props = defineProps({ status: { type: String, default: 'draft' } });
const $t = getCurrentInstance()?.appContext?.config?.globalProperties?.$t || ((k) => k);

const config = {
  draft:            { badge: 'badge-gray',    dot: 'bg-slate-400' },
  confirmed:        { badge: 'badge-indigo',  dot: 'bg-nexus-400' },
  processing:       { badge: 'badge-amber',   dot: 'bg-accent-amber' },
  dispatched:       { badge: 'badge-cyan',    dot: 'bg-accent-cyan' },
  in_transit:       { badge: 'badge-cyan',    dot: 'bg-accent-cyan' },
  out_for_delivery: { badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  delivered:        { badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  returned:         { badge: 'badge-rose',    dot: 'bg-accent-rose' },
  cancelled:        { badge: 'badge-rose',    dot: 'bg-accent-rose' },
  active:           { badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  closed:           { badge: 'badge-gray',    dot: 'bg-slate-400' },
  pending:          { badge: 'badge-amber',   dot: 'bg-accent-amber' },
  collected:        { badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  paid:             { badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  failed:           { badge: 'badge-rose',    dot: 'bg-accent-rose' },
  sent:             { badge: 'badge-indigo',  dot: 'bg-nexus-400' },
  read:             { badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  refunded:         { badge: 'badge-amber',   dot: 'bg-accent-amber' },
};

const c = computed(() => config[props.status] || { badge: 'badge-gray', dot: 'bg-slate-400' });
const label = computed(() => $t(`statuses.${props.status}`) || props.status);
const badgeClass = computed(() => `badge ${c.value.badge}`);
const dotClass = computed(() => `w-1.5 h-1.5 rounded-full ${c.value.dot}`);
</script>
