<template>
  <div :class="['glass rounded-xl p-5 transition-all duration-300 hover:glow group', `hover:border-${color}-500/20`]">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs text-nexus-500 uppercase tracking-wider font-medium">{{ label }}</div>
        <div class="text-2xl font-bold text-nexus-100 mt-1">{{ value }}</div>
        <div v-if="trend" :class="['text-xs mt-1 font-medium', trend > 0 ? 'text-accent-emerald' : 'text-accent-rose']">
          {{ trend > 0 ? '↑' : '↓' }} {{ Math.abs(trend) }}% from yesterday
        </div>
      </div>
      <div :class="[
        'w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300',
        colorClasses
      ]">
        <svg v-if="icon === 'orders'" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <svg v-else-if="icon === 'revenue'" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else-if="icon === 'pending'" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else-if="icon === 'transit'" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  icon: { type: String, default: 'orders' },
  trend: { type: Number, default: null },
  color: { type: String, default: 'nexus' },
});

const colorClasses = computed(() => {
  const map = {
    nexus: 'bg-nexus-500/10 text-nexus-400 group-hover:bg-nexus-500/20',
    emerald: 'bg-accent-emerald/10 text-accent-emerald group-hover:bg-accent-emerald/20',
    amber: 'bg-accent-amber/10 text-accent-amber group-hover:bg-accent-amber/20',
    cyan: 'bg-accent-cyan/10 text-accent-cyan group-hover:bg-accent-cyan/20',
    rose: 'bg-accent-rose/10 text-accent-rose group-hover:bg-accent-rose/20',
  };
  return map[props.color] || map.nexus;
});
</script>
