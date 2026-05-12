<template>
  <AuthenticatedLayout :title="$t('pageTitles.csat')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('csat.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('csat.subtitle') }}</p></div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-glow rounded-2xl p-5 text-center">
          <div class="text-3xl font-bold text-gradient">{{ avgRating || '—' }}</div>
          <div class="text-xs text-nexus-500 mt-1">{{ $t('csat.avgRating') }}</div>
          <div class="flex justify-center gap-0.5 mt-2">
            <svg v-for="s in 5" :key="s" :class="['w-4 h-4', s <= Math.round(avgRating) ? 'text-accent-amber' : 'text-nexus-800']" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          </div>
        </div>
        <div class="glass-glow rounded-2xl p-5 text-center">
          <div class="text-3xl font-bold text-nexus-200">{{ totalRatings }}</div>
          <div class="text-xs text-nexus-500 mt-1">{{ $t('csat.totalResponses') }}</div>
        </div>
        <div class="glass-glow rounded-2xl p-5 text-center">
          <div :class="['text-3xl font-bold', nps >= 50 ? 'text-accent-emerald' : nps >= 0 ? 'text-accent-amber' : 'text-accent-rose']">{{ nps }}</div>
          <div class="text-xs text-nexus-500 mt-1">{{ $t('csat.npsScore') }}</div>
        </div>
        <div class="glass-glow rounded-2xl p-5">
          <div class="space-y-1.5">
            <div v-for="s in [5,4,3,2,1]" :key="s" class="flex items-center gap-2 text-xs">
              <span class="w-3 text-nexus-400">{{ s }}</span>
              <div class="flex-1 h-2 bg-nexus-800/50 rounded-full overflow-hidden"><div class="h-full bg-accent-amber rounded-full transition-all" :style="{ width: barWidth(s) }"></div></div>
              <span class="w-6 text-right text-nexus-500">{{ distribution[s] || 0 }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Survey list -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('csat.surveyResponses') }}</h3></div>
        <div class="divide-y divide-white/[0.03]">
          <div v-for="s in surveys?.data" :key="s.id" class="px-6 py-4 hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="flex gap-0.5">
                  <svg v-for="i in 5" :key="i" :class="['w-4 h-4', i <= (s.rating || 0) ? 'text-accent-amber' : 'text-nexus-800']" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                </div>
                <span class="text-sm text-nexus-200">{{ s.order?.customer_name || s.contact?.name }}</span>
                <span class="font-mono text-xs text-nexus-500">{{ s.order?.order_number }}</span>
              </div>
              <span :class="['text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full font-semibold', s.status === 'completed' ? 'bg-accent-emerald/10 text-accent-emerald' : s.status === 'pending' ? 'bg-accent-amber/10 text-accent-amber' : 'bg-nexus-500/10 text-nexus-500']">{{ s.status }}</span>
            </div>
            <div v-if="s.feedback" class="mt-2 text-sm text-nexus-400 italic">"{{ s.feedback }}"</div>
          </div>
          <div v-if="!surveys?.data?.length" class="px-6 py-12 text-center text-sm text-nexus-600">{{ $t('csat.noSurveys') }}</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({ surveys: Object, avgRating: Number, totalRatings: Number, distribution: Object, nps: Number, days: Number });
function barWidth(rating) { const total = props.totalRatings || 1; return ((props.distribution?.[rating] || 0) / total * 100) + '%'; }
</script>
