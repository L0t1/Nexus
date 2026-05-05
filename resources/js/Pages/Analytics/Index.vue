<template>
  <AuthenticatedLayout title="Analytics">
    <div class="space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">Analytics</h2><p class="text-nexus-500 text-sm mt-1">Performance metrics</p></div>
        <div class="flex gap-1.5 p-1 rounded-xl bg-white/[0.02] border border-white/[0.04] max-w-full overflow-x-auto hide-scrollbar">
          <button v-for="d in dayOpts" :key="d.value" @click="setDays(d.value)" :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap', days === d.value ? 'bg-nexus-500/15 text-nexus-300' : 'text-nexus-600 hover:text-nexus-400']">{{ d.label }}</button>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 stagger-children">
        <div class="glass stat-card stat-card-emerald rounded-2xl p-5"><div class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600 mb-1">Revenue</div><div class="text-2xl font-bold text-nexus-100">€{{ summary.total_revenue?.toFixed(2) }}</div></div>
        <div class="glass stat-card stat-card-indigo rounded-2xl p-5"><div class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600 mb-1">Orders</div><div class="text-2xl font-bold text-nexus-100">{{ summary.total_orders }}</div></div>
        <div class="glass stat-card stat-card-amber rounded-2xl p-5"><div class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600 mb-1">Avg Order</div><div class="text-2xl font-bold text-nexus-100">€{{ summary.avg_order_value?.toFixed(2) }}</div></div>
        <div class="glass stat-card stat-card-cyan rounded-2xl p-5"><div class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600 mb-1">Delivery</div><div class="text-2xl font-bold text-accent-emerald">{{ summary.delivery_rate }}%</div></div>
      </div>

      <!-- Chart -->
      <div class="glass-glow rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-nexus-200 mb-5">Revenue Over Time</h3>
        <div class="h-56 flex items-end gap-[2px]">
          <div v-for="(pt, i) in chartData" :key="i" class="flex-1 flex flex-col items-center group relative">
            <div class="absolute -top-9 left-1/2 -translate-x-1/2 px-2.5 py-1.5 rounded-lg bg-nexus-800 border border-white/[0.06] text-[10px] text-nexus-200 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 shadow-xl">€{{ pt.revenue.toFixed(2) }} · {{ pt.orders }} orders</div>
            <div class="w-full rounded-t transition-all duration-200 group-hover:opacity-100" :class="pt.revenue > 0 ? 'bg-gradient-to-t from-nexus-600/40 to-nexus-400/60 opacity-70' : 'bg-white/[0.02] opacity-30'" :style="{ height: barH(pt.revenue) + '%', minHeight: pt.revenue > 0 ? '3px' : '1px' }"></div>
          </div>
        </div>
        <div class="flex justify-between mt-2 px-1"><span v-for="(pt, i) in chartData.filter((_, i) => i % labelInt === 0)" :key="i" class="text-[9px] text-nexus-700">{{ pt.date }}</span></div>
      </div>

      <!-- Advanced Analytics -->
      <div class="relative mt-8">
        <!-- Upsell Overlay -->
        <div v-if="!hasAdvanced" class="absolute inset-0 z-10 glass-strong backdrop-blur-md rounded-2xl flex flex-col items-center justify-center p-6 text-center border border-white/10 shadow-2xl">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-accent-cyan to-accent-violet flex items-center justify-center text-white mb-5 shadow-lg shadow-accent-cyan/20"><svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg></div>
          <h3 class="text-xl font-bold text-nexus-100 mb-2">Advanced Analytics Locked</h3>
          <p class="text-sm text-nexus-400 max-w-md mb-6 leading-relaxed">Upgrade to the Pro or Ultimate plan to unlock deep insights into your top cities, sales channels, return rates, and most sold products.</p>
          <button @click="$inertia.visit('/subscription')" class="btn-primary py-2.5 px-6">Upgrade to Unlock</button>
        </div>

        <!-- Locked Content -->
        <div :class="['space-y-6 transition-all duration-500', !hasAdvanced ? 'opacity-20 pointer-events-none blur-[4px] select-none' : '']">
          <!-- Bottom grid -->
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 stagger-children">
            <div class="glass rounded-2xl overflow-hidden">
              <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Order Status</h3></div>
              <div class="p-5 space-y-2.5">
                <div v-for="(count, status) in statusBreakdown" :key="status" class="flex items-center justify-between"><div class="flex items-center gap-2"><StatusBadge :status="status" /></div><span class="text-sm font-medium text-nexus-300">{{ count }}</span></div>
                <div v-if="!Object.keys(statusBreakdown || {}).length" class="text-sm text-nexus-700 text-center py-4">No data</div>
              </div>
            </div>
            <div class="glass rounded-2xl overflow-hidden">
              <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Top Cities</h3></div>
              <div class="p-5 space-y-2.5">
                <div v-for="(city, i) in topCities" :key="city.city" class="flex items-center justify-between"><span class="text-sm text-nexus-300">{{ city.city }}</span><div class="text-right"><span class="text-sm font-medium text-nexus-200">{{ city.orders }}</span><span class="text-xs text-nexus-600 ml-1">orders</span></div></div>
                <div v-if="!topCities?.length" class="text-sm text-nexus-700 text-center py-4">No data</div>
              </div>
            </div>
            <div class="glass rounded-2xl overflow-hidden">
              <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Channels</h3></div>
              <div class="p-5 space-y-2.5">
                <div v-for="(count, platform) in channelBreakdown" :key="platform" class="flex items-center justify-between"><div class="flex items-center gap-2"><PlatformIcon :platform="platform" class="w-4 h-4" /><span class="text-sm text-nexus-300 capitalize">{{ platform }}</span></div><span class="text-sm font-medium text-nexus-200">{{ count }}</span></div>
                <div v-if="!Object.keys(channelBreakdown || {}).length" class="text-sm text-nexus-700 text-center py-4">No data</div>
              </div>
            </div>
            <div class="glass rounded-2xl overflow-hidden">
              <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Most Sold Products</h3></div>
              <div class="p-5 space-y-2.5">
                <div v-for="product in topProducts" :key="product.name" class="flex items-center justify-between">
                  <span class="text-sm text-nexus-300 truncate pr-2">{{ product.name }}</span>
                  <div class="text-right whitespace-nowrap"><span class="text-sm font-medium text-accent-emerald">{{ product.sold }}</span><span class="text-xs text-nexus-600 ml-1">sold</span></div>
                </div>
                <div v-if="!topProducts?.length" class="text-sm text-nexus-700 text-center py-4">No data</div>
              </div>
            </div>
          </div>

          <!-- Return rate -->
          <div class="glass gradient-border rounded-2xl p-6">
            <div class="flex items-center justify-between">
              <div><h3 class="text-sm font-semibold text-nexus-200">Return Rate</h3><p class="text-xs text-nexus-600 mt-0.5">{{ summary.returned_orders }} / {{ summary.total_orders }} orders</p></div>
              <div :class="['text-3xl font-bold', summary.return_rate > 15 ? 'text-accent-rose' : summary.return_rate > 5 ? 'text-accent-amber' : 'text-accent-emerald']">{{ summary.return_rate }}%</div>
            </div>
            <div class="mt-4 h-1.5 rounded-full bg-white/[0.04] overflow-hidden"><div class="h-full rounded-full transition-all duration-700" :class="summary.return_rate > 15 ? 'bg-gradient-to-r from-accent-rose to-accent-pink' : summary.return_rate > 5 ? 'bg-gradient-to-r from-accent-amber to-accent-rose' : 'bg-gradient-to-r from-accent-emerald to-accent-cyan'" :style="{ width: Math.min(summary.return_rate, 100) + '%' }"></div></div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({ chartData: Array, summary: Object, statusBreakdown: Object, topCities: Array, channelBreakdown: Object, topProducts: Array, days: { type: Number, default: 30 } });
const dayOpts = [{ label: '7d', value: 7 }, { label: '30d', value: 30 }, { label: '90d', value: 90 }];
const maxRev = computed(() => Math.max(...(props.chartData || []).map(p => p.revenue), 1));
const labelInt = computed(() => props.days <= 7 ? 1 : props.days <= 30 ? 7 : 15);
const hasAdvanced = computed(() => usePage().props.auth.merchant?.features?.includes('advanced_analytics'));

function barH(r) { return (r / maxRev.value) * 100; }
function setDays(d) { router.get('/analytics', { days: d }, { preserveState: true }); }
</script>
