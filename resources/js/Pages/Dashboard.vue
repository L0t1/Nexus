<template>
  <AuthenticatedLayout :title="$t('nav.dashboard')">
    <div class="space-y-6">
      <!-- Welcome -->
      <div class="animate-fade-in">
        <h2 class="text-2xl font-bold text-nexus-100">{{ $t('dashboard.welcomeBack') }}<span class="text-gradient">, {{ $page.props.auth?.name?.split(' ')[0] }}</span></h2>
        <p class="text-nexus-500 text-sm mt-1">{{ $t('dashboard.subtitle') }}</p>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 stagger-children">
        <div class="glass stat-card stat-card-indigo rounded-2xl p-5 cursor-pointer hover:scale-[1.02] transition-all relative group" @click="$inertia.visit('/orders')">
          <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4 text-nexus-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
          </div>
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-nexus-500/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-nexus-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600">{{ $t('common.today') }}</span>
          </div>
          <div class="text-3xl font-bold text-nexus-100 tracking-tight">{{ stats.orders_today ?? 0 }}</div>
          <div class="text-xs text-nexus-500 mt-1">{{ $t('dashboard.ordersToday') }}</div>
        </div>

        <div class="glass stat-card stat-card-emerald rounded-2xl p-5 cursor-pointer hover:scale-[1.02] transition-all relative group" @click="$inertia.visit('/orders')">
          <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4 text-accent-emerald/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
          </div>
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-accent-emerald/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600">{{ $t('dashboard.revenue') }}</span>
          </div>
          <div class="text-3xl font-bold text-nexus-100 tracking-tight">€{{ Number(stats.revenue_today ?? 0).toFixed(0) }}</div>
          <div class="text-xs text-accent-emerald mt-1">{{ $t('dashboard.todaysEarnings') }}</div>
        </div>

        <div class="glass stat-card stat-card-amber rounded-2xl p-5 cursor-pointer hover:scale-[1.02] transition-all relative group" @click="$inertia.visit('/orders?status=confirmed')">
          <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4 text-accent-amber/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
          </div>
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-accent-amber/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-accent-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600">{{ $t('dashboard.pending') }}</span>
          </div>
          <div class="text-3xl font-bold text-nexus-100 tracking-tight">{{ stats.pending_orders ?? 0 }}</div>
          <div class="text-xs text-accent-amber mt-1">{{ $t('dashboard.awaitingAction') }}</div>
        </div>

        <div class="glass stat-card stat-card-cyan rounded-2xl p-5 cursor-pointer hover:scale-[1.02] transition-all relative group" @click="$inertia.visit('/orders?status=dispatched')">
          <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4 text-accent-cyan/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
          </div>
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-accent-cyan/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.209-.379 1.404-.937M8.25 18.75H18M2.25 14.25h1.5m14.579 0c.195-.558.783-.937 1.404-.937h1.125" /></svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-nexus-600">{{ $t('dashboard.shipping') }}</span>
          </div>
          <div class="text-3xl font-bold text-nexus-100 tracking-tight">{{ stats.in_transit ?? 0 }}</div>
          <div class="text-xs text-accent-cyan mt-1">{{ $t('dashboard.inTransit') }}</div>
        </div>
      </div>

      <!-- Main grid -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="xl:col-span-2 glass-glow rounded-2xl overflow-hidden animate-fade-in" style="animation-delay: 0.2s">
          <div class="px-6 py-4 flex items-center justify-between border-b border-white/[0.04]">
            <div class="flex items-center gap-2.5">
              <div class="w-1.5 h-1.5 rounded-full bg-nexus-400 dot-pulse"></div>
              <h3 class="text-sm font-semibold text-nexus-200">{{ $t('dashboard.recentOrders') }}</h3>
            </div>
            <a href="/orders" @click.prevent="$inertia.visit('/orders')" class="text-xs text-nexus-500 hover:text-nexus-300 transition-colors">{{ $t('common.viewAll') }}</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full table-premium min-w-[600px]">
            <thead>
              <tr>
                <th>{{ $t('orders.order') }}</th>
                <th>{{ $t('orders.customer') }}</th>
                <th>{{ $t('orders.status') }}</th>
                <th class="text-right">{{ $t('orders.total') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id" class="cursor-pointer" @click="$inertia.visit(`/orders/${order.id}`)">
                <td>
                  <span class="font-mono text-xs font-medium text-nexus-400">{{ order.order_number }}</span>
                </td>
                <td>
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-nexus-500/20 to-accent-violet/20 flex items-center justify-center text-[10px] font-bold text-nexus-300">{{ order.customer_name?.[0] }}</div>
                    <div>
                      <div class="text-sm text-nexus-200">{{ order.customer_name }}</div>
                      <div class="text-[10px] text-nexus-600">{{ order.shipping_city }}</div>
                    </div>
                  </div>
                </td>
                <td><StatusBadge :status="order.fulfillment_status" /></td>
                <td class="text-right font-medium text-nexus-200">€{{ Number(order.total).toFixed(2) }}</td>
              </tr>
            </tbody>
            </table>
          </div>
          <div v-if="!recentOrders?.length" class="p-12 text-center text-nexus-600 text-sm">{{ $t('dashboard.noOrders') }}</div>
        </div>

        <!-- Active Chats -->
        <div class="glass-glow rounded-2xl overflow-hidden animate-fade-in" style="animation-delay: 0.3s">
          <div class="px-5 py-4 flex items-center justify-between border-b border-white/[0.04]">
            <div class="flex items-center gap-2.5">
              <div class="w-1.5 h-1.5 rounded-full bg-accent-cyan dot-pulse"></div>
              <h3 class="text-sm font-semibold text-nexus-200">{{ $t('dashboard.activeChats') }}</h3>
            </div>
            <a href="/inbox" @click.prevent="$inertia.visit('/inbox')" class="text-xs text-nexus-500 hover:text-nexus-300 transition-colors">{{ $t('common.viewAll') }}</a>
          </div>
          <div class="divide-y divide-white/[0.03]">
            <div v-for="conv in activeChats" :key="conv.id" @click="$inertia.visit(`/inbox/${conv.id}`)" class="px-5 py-3.5 flex items-center gap-3 hover:bg-surface-hover cursor-pointer transition-all group">
              <div class="relative flex-shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-nexus-400/20 to-accent-cyan/20 flex items-center justify-center text-xs font-bold text-nexus-300">{{ conv.contact?.name?.[0]?.toUpperCase() }}</div>
                <div v-if="conv.is_unread" class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-accent-cyan dot-pulse"></div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-0.5">
                  <span :class="['text-sm truncate', conv.is_unread ? 'font-semibold text-nexus-100' : 'text-nexus-300']">{{ conv.contact?.name }}</span>
                  <PlatformIcon :platform="conv.platform" class="w-3.5 h-3.5 opacity-40 group-hover:opacity-70 transition-opacity" />
                </div>
                <p class="text-xs text-nexus-600 truncate">{{ conv.last_message_preview }}</p>
              </div>
            </div>
          </div>
          <div v-if="!activeChats?.length" class="p-12 text-center text-nexus-600 text-sm">{{ $t('dashboard.noChats') }}</div>
        </div>
      </div>

      <!-- COD Widget -->
      <div v-if="stats.cod_pending > 0" class="glass gradient-border rounded-2xl p-6 animate-fade-in" style="animation-delay: 0.4s">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-semibold text-nexus-200">{{ $t('dashboard.codToCollect') }}</h3>
            <p class="text-xs text-nexus-500 mt-0.5">{{ stats.cod_pending }} {{ $t('dashboard.codDescription') }}</p>
          </div>
          <div class="text-2xl font-bold text-gradient-warm">€{{ Number(stats.cod_amount ?? 0).toFixed(2) }}</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';

const page = usePage();
const stats = computed(() => page.props.stats || {});
const recentOrders = computed(() => page.props.recentOrders || []);
const activeChats = computed(() => page.props.activeChats || []);
</script>
