<template>
  <AuthenticatedLayout :title="$t('pageTitles.forecast')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('forecast.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('forecast.subtitle') }}</p></div>
      </div>

      <!-- Restock Alerts -->
      <div v-if="restockRecommendations.length" class="glass-glow rounded-2xl p-5 border border-accent-amber/20">
        <div class="flex items-center gap-2 mb-3">
          <svg class="w-5 h-5 text-accent-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" /></svg>
          <h3 class="text-sm font-semibold text-accent-amber">{{ $t('forecast.restockRec') }}</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div v-for="r in restockRecommendations" :key="r.product" class="p-3 rounded-xl bg-nexus-900/40">
            <div class="text-sm font-medium text-nexus-200">{{ r.product }}</div>
            <div class="grid grid-cols-3 gap-2 mt-2 text-center">
              <div><div class="text-lg font-bold text-accent-rose">{{ r.current_stock }}</div><div class="text-[9px] text-nexus-600 uppercase">{{ $t('forecast.stock') }}</div></div>
              <div><div class="text-lg font-bold text-accent-amber">{{ r.predicted_7d }}</div><div class="text-[9px] text-nexus-600 uppercase">{{ $t('forecast.demand7d') }}</div></div>
              <div><div class="text-lg font-bold text-accent-rose">{{ r.shortfall }}</div><div class="text-[9px] text-nexus-600 uppercase">{{ $t('forecast.shortfall') }}</div></div>
            </div>
            <div class="mt-2 flex items-center gap-1 text-xs">
              <span :class="['w-2 h-2 rounded-full', r.trend === 'rising' ? 'bg-accent-emerald' : r.trend === 'falling' ? 'bg-accent-rose' : 'bg-nexus-500']"></span>
              <span class="text-nexus-400 capitalize">{{ $t('forecast.' + (r.trend || 'stable')) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Low Stock Products -->
      <div v-if="lowStockProducts.length" class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04] flex items-center gap-2">
          <svg class="w-4 h-4 text-accent-rose" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3" /></svg>
          <h3 class="text-sm font-semibold text-nexus-200">{{ $t('forecast.lowStock') }}</h3>
        </div>
        <div class="divide-y divide-white/[0.03]">
          <div v-for="p in lowStockProducts" :key="p.id" class="px-6 py-3 flex items-center justify-between">
            <div class="text-sm text-nexus-200">{{ p.name }} <span v-if="p.sku" class="text-xs text-nexus-600 ml-1">({{ p.sku }})</span></div>
            <span :class="['text-sm font-bold', p.stock <= 3 ? 'text-accent-rose' : 'text-accent-amber']">{{ p.stock }} {{ $t('forecast.left') }}</span>
          </div>
        </div>
      </div>

      <!-- Forecast per Product -->
      <div v-for="(productForecasts, productName) in forecasts" :key="productName" class="glass-glow rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-nexus-200">{{ productName }}</h3>
          <span :class="['text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1', trendClass(productForecasts[0]?.trend)]">
            <span class="w-2 h-2 rounded-full" :class="trendDot(productForecasts[0]?.trend)"></span>
            {{ $t('forecast.' + (productForecasts[0]?.trend || 'stable')) }}
          </span>
        </div>
        <div class="grid grid-cols-7 gap-1.5">
          <div v-for="f in productForecasts.slice(0, 14)" :key="f.forecast_date" class="text-center p-2 rounded-lg bg-nexus-900/30">
            <div class="text-[9px] text-nexus-600">{{ formatDay(f.forecast_date) }}</div>
            <div class="text-sm font-bold text-nexus-200 mt-0.5">{{ f.predicted_demand }}</div>
            <div class="text-[8px] text-nexus-700 mt-0.5">{{ f.confidence }}%</div>
          </div>
        </div>
      </div>

      <div v-if="!Object.keys(forecasts).length" class="p-16 text-center glass-glow rounded-2xl">
        <div class="w-16 h-16 rounded-2xl bg-accent-cyan/5 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-accent-cyan/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z" /></svg>
        </div>
        <p class="text-nexus-600 text-sm">{{ $t('forecast.noData') }}</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineProps({ forecasts: { type: Object, default: () => ({}) }, salesHistory: Object, lowStockProducts: Array, restockRecommendations: Array });
function formatDay(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }) : ''; }
function trendClass(t) { return { rising: 'bg-accent-emerald/10 text-accent-emerald', falling: 'bg-accent-rose/10 text-accent-rose', stable: 'bg-nexus-500/10 text-nexus-400' }[t] || 'bg-nexus-500/10 text-nexus-400'; }
function trendDot(t) { return { rising: 'bg-accent-emerald', falling: 'bg-accent-rose', stable: 'bg-nexus-500' }[t] || 'bg-nexus-500'; }
</script>
