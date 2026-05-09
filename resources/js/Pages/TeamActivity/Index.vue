<template>
  <AuthenticatedLayout :title="$t('teamActivity.title')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('teamActivity.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('teamActivity.subtitle') }}</p></div>
        <select v-model="selectedDays" @change="reload" class="input-premium w-auto text-xs bg-transparent">
          <option :value="7">{{ $t('teamActivity.last7') }}</option><option :value="14">{{ $t('teamActivity.last14') }}</option><option :value="30">{{ $t('teamActivity.last30') }}</option>
        </select>
      </div>

      <!-- Agent Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="agent in agentStats" :key="agent.user_id" class="glass-glow rounded-2xl p-5">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-violet/20 to-accent-cyan/20 flex items-center justify-center text-sm font-bold text-nexus-200">{{ agent.name?.[0] }}</div>
            <div><div class="text-sm font-semibold text-nexus-100">{{ agent.name }}</div><div class="text-[10px] text-nexus-500">{{ agent.active_days }} {{ $t('teamActivity.activeDays') }}</div></div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-2 rounded-lg bg-nexus-900/30"><div class="text-lg font-bold text-nexus-200">{{ agent.actions }}</div><div class="text-[10px] text-nexus-600 uppercase">{{ $t('teamActivity.actions') }}</div></div>
            <div class="text-center p-2 rounded-lg bg-nexus-900/30"><div class="text-lg font-bold text-accent-cyan">{{ Math.round(agent.actions / Math.max(agent.active_days, 1)) }}</div><div class="text-[10px] text-nexus-600 uppercase">{{ $t('teamActivity.perDay') }}</div></div>
          </div>
        </div>
      </div>

      <!-- Action Breakdown -->
      <div class="glass rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-nexus-200 mb-3">{{ $t('teamActivity.activityBreakdown') }}</h3>
        <div class="flex flex-wrap gap-2">
          <div v-for="(count, action) in actionTypes" :key="action" class="px-3 py-1.5 rounded-lg bg-nexus-500/10 text-xs">
            <span class="text-nexus-400 capitalize">{{ action.replace('.', ' → ') }}</span>
            <span class="ml-1.5 font-bold text-nexus-200">{{ count }}</span>
          </div>
        </div>
      </div>

      <!-- Activity Feed -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('teamActivity.activityFeed') }}</h3></div>
        <div class="divide-y divide-white/[0.03] max-h-[600px] overflow-y-auto">
          <div v-for="log in logs?.data" :key="log.id" class="px-6 py-3 hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div :class="['w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold', log.user ? 'bg-accent-violet/15 text-accent-violet' : 'bg-nexus-500/10 text-nexus-600']">{{ log.user?.name?.[0] || 'S' }}</div>
                <div>
                  <div class="text-sm text-nexus-200">{{ log.description }}</div>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] text-nexus-500">{{ log.user?.name || $t('teamActivity.system') }}</span>
                    <span class="text-[10px] text-nexus-700">·</span>
                    <span class="text-[10px] text-nexus-600">{{ formatTime(log.created_at) }}</span>
                  </div>
                </div>
              </div>
              <span class="text-[9px] font-mono px-2 py-0.5 rounded bg-nexus-800/50 text-nexus-500">{{ log.action }}</span>
            </div>
          </div>
          <div v-if="!logs?.data?.length" class="px-6 py-12 text-center text-sm text-nexus-600">{{ $t('teamActivity.noActivity') }}</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({ logs: Object, agentStats: Array, actionTypes: Object, days: Number, filters: Object });
const selectedDays = ref(props.days || 7);
function reload() { router.get('/team-activity', { days: selectedDays.value }, { preserveState: true }); }
function formatTime(d) { return d ? new Date(d).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }) : ''; }
</script>
