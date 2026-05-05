<template>
  <AuthenticatedLayout title="Orders">
    <div class="space-y-5 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">Orders</h2>
          <p class="text-nexus-500 text-sm mt-1">Manage your order pipeline</p>
        </div>
        <button @click="$inertia.visit('/inbox')" class="btn-primary flex items-center gap-2 text-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg> New Order</button>
      </div>

      <!-- Status tabs -->
      <div class="flex gap-1.5 p-1 rounded-xl bg-white/[0.02] border border-white/[0.04] w-full sm:w-fit overflow-x-auto hide-scrollbar">
        <button v-for="f in statusFilters" :key="f.value" @click="setFilter(f.value)" :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap', activeFilter === f.value ? 'bg-nexus-500/15 text-nexus-300 shadow-sm' : 'text-nexus-600 hover:text-nexus-400']">{{ f.label }}</button>
      </div>

      <!-- Table -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-premium min-w-[800px]">
          <thead>
            <tr>
              <th>Order</th>
              <th>Customer</th>
              <th>City</th>
              <th>Status</th>
              <th>Payment</th>
              <th class="text-right">Total</th>
              <th class="text-right">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders?.data" :key="order.id" class="cursor-pointer group" @click="$inertia.visit(`/orders/${order.id}`)">
              <td><span class="font-mono text-xs font-medium text-nexus-400 group-hover:text-nexus-300 transition-colors">{{ order.order_number }}</span></td>
              <td>
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-nexus-500/15 to-accent-violet/15 flex items-center justify-center text-[10px] font-bold text-nexus-400">{{ order.customer_name?.[0] }}</div>
                  <div>
                    <div class="text-sm text-nexus-200 group-hover:text-nexus-100 transition-colors">{{ order.customer_name }}</div>
                    <div class="text-[10px] text-nexus-600">{{ order.customer_phone }}</div>
                  </div>
                </div>
              </td>
              <td class="text-sm text-nexus-400">{{ order.shipping_city || '—' }}</td>
              <td><StatusBadge :status="order.fulfillment_status" /></td>
              <td><StatusBadge :status="order.payment_status" /></td>
              <td class="text-right font-medium text-nexus-200">€{{ Number(order.total).toFixed(2) }}</td>
              <td class="text-right text-xs text-nexus-600">{{ formatDate(order.created_at) }}</td>
            </tr>
          </tbody>
          </table>
        </div>
        <div v-if="!orders?.data?.length" class="p-16 text-center">
          <div class="w-16 h-16 rounded-2xl bg-nexus-500/5 flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg></div>
          <p class="text-nexus-600 text-sm">No orders found</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
  orders: { type: Object, default: () => ({ data: [] }) },
  filters: { type: Object, default: () => ({}) },
});

const activeFilter = ref(props.filters.status || '');
const statusFilters = [
  { label: 'All', value: '' },
  { label: 'Draft', value: 'draft' },
  { label: 'Confirmed', value: 'confirmed' },
  { label: 'Dispatched', value: 'dispatched' },
  { label: 'Delivered', value: 'delivered' },
  { label: 'Returned', value: 'returned' },
];

function setFilter(v) { activeFilter.value = v; router.get('/orders', { status: v }, { preserveState: true }); }
function formatDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }
</script>
