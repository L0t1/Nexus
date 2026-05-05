<template>
  <span :class="badgeClass">
    <span :class="dotClass"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ status: { type: String, default: 'draft' } });

const config = {
  draft:            { label: 'Draft',            badge: 'badge-gray',    dot: 'bg-slate-400' },
  confirmed:        { label: 'Confirmed',        badge: 'badge-indigo',  dot: 'bg-nexus-400' },
  processing:       { label: 'Processing',       badge: 'badge-amber',   dot: 'bg-accent-amber' },
  dispatched:       { label: 'Dispatched',        badge: 'badge-cyan',    dot: 'bg-accent-cyan' },
  in_transit:       { label: 'In Transit',        badge: 'badge-cyan',    dot: 'bg-accent-cyan' },
  out_for_delivery: { label: 'Out for Delivery',  badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  delivered:        { label: 'Delivered',          badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  returned:         { label: 'Returned',           badge: 'badge-rose',    dot: 'bg-accent-rose' },
  cancelled:        { label: 'Cancelled',          badge: 'badge-rose',    dot: 'bg-accent-rose' },
  active:           { label: 'Active',             badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  closed:           { label: 'Closed',             badge: 'badge-gray',    dot: 'bg-slate-400' },
  pending:          { label: 'Pending',            badge: 'badge-amber',   dot: 'bg-accent-amber' },
  collected:        { label: 'Collected',          badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  paid:             { label: 'Paid',               badge: 'badge-emerald', dot: 'bg-accent-emerald' },
  failed:           { label: 'Failed',             badge: 'badge-rose',    dot: 'bg-accent-rose' },
  sent:             { label: 'Sent',               badge: 'badge-indigo',  dot: 'bg-nexus-400' },
  read:             { label: 'Read',               badge: 'badge-emerald', dot: 'bg-accent-emerald' },
};

const c = computed(() => config[props.status] || { label: props.status, badge: 'badge-gray', dot: 'bg-slate-400' });
const label = computed(() => c.value.label);
const badgeClass = computed(() => `badge ${c.value.badge}`);
const dotClass = computed(() => `w-1.5 h-1.5 rounded-full ${c.value.dot}`);
</script>
