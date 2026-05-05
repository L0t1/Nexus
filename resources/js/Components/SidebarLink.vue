<template>
  <component
    :is="href ? 'a' : 'button'"
    :href="href"
    @click="navigate"
    :class="[
      'group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden',
      active
        ? 'bg-nexus-500/15 text-nexus-200 border border-nexus-500/20'
        : 'text-nexus-400 hover:text-nexus-200 hover:bg-surface-glass border border-transparent',
      collapsed && 'justify-center px-0'
    ]"
    :title="collapsed ? label : undefined"
  >
    <!-- Active indicator -->
    <div v-if="active" class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-nexus-500"></div>

    <!-- Icon -->
    <div :class="['w-5 h-5 flex-shrink-0', active ? 'text-nexus-400' : 'text-nexus-500 group-hover:text-nexus-400']">
      <!-- Dashboard -->
      <svg v-if="icon === 'dashboard'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5z" />
      </svg>
      <!-- Inbox -->
      <svg v-else-if="icon === 'inbox'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
      </svg>
      <!-- Orders -->
      <svg v-else-if="icon === 'orders'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
      <!-- Contacts -->
      <svg v-else-if="icon === 'contacts'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <!-- Analytics -->
      <svg v-else-if="icon === 'analytics'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
      </svg>
      <!-- Settings -->
      <svg v-else-if="icon === 'settings'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
    </div>

    <!-- Label -->
    <span v-if="!collapsed" class="whitespace-nowrap animate-fade-in">{{ label }}</span>

    <!-- Badge -->
    <span
      v-if="badge && !collapsed"
      class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-nexus-500/20 text-nexus-300 animate-fade-in"
    >
      {{ badge > 99 ? '99+' : badge }}
    </span>

    <!-- Collapsed badge dot -->
    <span
      v-if="badge && collapsed"
      class="absolute top-1 right-1 w-2 h-2 rounded-full bg-accent-rose animate-pulse-glow"
    ></span>
  </component>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
  href: { type: String, default: null },
  icon: { type: String, required: true },
  label: { type: String, required: true },
  badge: { type: [Number, String], default: null },
  collapsed: { type: Boolean, default: false },
  active: { type: Boolean, default: false },
});

function navigate(e) {
  if (props.href) {
    e.preventDefault();
    router.visit(props.href);
  }
}
</script>
