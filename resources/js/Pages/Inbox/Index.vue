<template>
  <AuthenticatedLayout :title="$t('inbox.title')">
    <div class="space-y-5 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">{{ $t('inbox.title') }}</h2>
          <p class="text-nexus-500 text-sm mt-1">{{ conversations?.total || conversations?.data?.length || 0 }} {{ $t('inbox.conversations') }}</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1 flex flex-wrap gap-3 items-center">
          <div class="relative flex-1 max-w-md group">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-nexus-600 group-focus-within:text-nexus-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input v-model="search" type="text" :placeholder="$t('inbox.searchConversations')" class="input-premium pl-10" />
          </div>
          <div class="flex gap-1.5 p-1 rounded-xl bg-white/[0.02] border border-white/[0.04]">
            <button v-for="s in statusFilters" :key="s.value" @click="setStatus(s.value)" :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-all', activeStatus === s.value ? 'bg-nexus-500/15 text-nexus-300 shadow-sm' : 'text-nexus-600 hover:text-nexus-400']">{{ $t(s.label) }}</button>
          </div>
        </div>
        <!-- Legend -->
        <div class="text-[10px] text-nexus-500 bg-white/[0.02] px-3 py-2 rounded-lg border border-white/[0.04] flex gap-4 hidden sm:flex">
          <div><span class="font-bold text-nexus-200">{{ $t('inbox.active') }}:</span> {{ $t('inbox.activeLegend') }}</div>
          <div><span class="font-bold text-nexus-200">{{ $t('inbox.closed') }}:</span> {{ $t('inbox.closedLegend') }}</div>
        </div>
      </div>

      <!-- Conversation List -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="divide-y divide-white/[0.03]">
          <div v-for="conv in conversations?.data" :key="conv.id" @click="$inertia.visit(`/inbox/${conv.id}`)" class="px-5 py-4 flex items-center gap-4 hover:bg-nexus-500/[0.03] cursor-pointer transition-all group">
            <div class="relative flex-shrink-0">
              <div :class="['w-11 h-11 rounded-xl flex items-center justify-center text-sm font-bold transition-all', conv.is_unread ? 'bg-gradient-to-br from-nexus-500/30 to-accent-cyan/30 text-nexus-200 shadow-lg shadow-nexus-500/10' : 'bg-white/[0.04] text-nexus-400']">
                {{ conv.contact?.name?.[0]?.toUpperCase() || '?' }}
              </div>
              <div class="absolute -bottom-0.5 -right-0.5"><PlatformIcon :platform="conv.platform" class="w-4 h-4" /></div>
              <div v-if="conv.is_unread" class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-accent-cyan dot-pulse"></div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <span :class="['text-sm truncate', conv.is_unread ? 'font-semibold text-nexus-100' : 'font-medium text-nexus-300']">{{ conv.contact?.name || $t('chat.unknown') }}</span>
                <span class="text-[10px] text-nexus-600 flex-shrink-0 ml-3">{{ formatTime(conv.last_message_at) }}</span>
              </div>
              <div class="flex items-center justify-between">
                <p :class="['text-xs truncate pr-4', conv.is_unread ? 'text-nexus-400' : 'text-nexus-600']">{{ conv.last_message_preview || $t('inbox.noMessages') }}</p>
                <div class="flex items-center gap-2 flex-shrink-0">
                  <StatusBadge v-if="conv.status !== 'active'" :status="conv.status" />
                  <span v-if="conv.unread_count > 0" class="min-w-[20px] h-5 px-1.5 rounded-full bg-accent-cyan/15 text-accent-cyan text-[10px] font-bold flex items-center justify-center">{{ conv.unread_count }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="!conversations?.data?.length" class="p-16 text-center">
          <div class="w-16 h-16 rounded-2xl bg-nexus-500/5 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
          </div>
          <p class="text-nexus-600 text-sm">{{ $t('inbox.noConversations') }}</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  conversations: { type: Object, default: () => ({ data: [] }) },
  filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const activeStatus = ref(props.filters.status || '');
const statusFilters = [
  { label: 'inbox.all', value: '' },
  { label: 'inbox.active', value: 'active' },
  { label: 'inbox.closed', value: 'closed' },
];

let pollingInterval = null;

onMounted(() => {
  // Auto-refresh the inbox list every 3 seconds
  pollingInterval = setInterval(() => {
    router.reload({ only: ['conversations', 'stats'], preserveState: true, preserveScroll: true });
  }, 3000);
});

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
});

let timeout = null;
watch(search, (val) => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    router.get('/inbox', { search: val, status: activeStatus.value }, { preserveState: true });
  }, 300);
});

function setStatus(val) {
  activeStatus.value = val;
  router.get('/inbox', { search: search.value, status: val }, { preserveState: true });
}

function formatTime(d) {
  if (!d) return '';
  const date = new Date(d);
  const now = new Date();
  const diff = now - date;
  if (diff < 60000) return 'now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h`;
  return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
}
</script>
