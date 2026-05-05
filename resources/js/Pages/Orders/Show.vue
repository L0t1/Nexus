<template>
  <AuthenticatedLayout title="Order Details">
    <div class="animate-fade-in space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$inertia.visit('/orders')" class="p-2 rounded-xl hover:bg-surface-hover text-nexus-500 hover:text-nexus-300 transition-all"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg></button>
          <div>
            <h2 class="text-xl font-bold font-mono text-nexus-100 tracking-tight">{{ order.order_number }}</h2>
            <p class="text-xs text-nexus-500 mt-0.5">{{ formatDate(order.created_at) }}</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <StatusBadge :status="order.fulfillment_status" />
          <select @change="updateStatus($event.target.value)" class="input-premium text-xs py-1.5 px-3 w-auto cursor-pointer">
            <option value="">Change status...</option>
            <option v-for="s in statusOptions" :key="s" :value="s">{{ s.replace('_', ' ') }}</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main -->
        <div class="lg:col-span-2 space-y-5 stagger-children">
          <!-- Items -->
          <div class="glass-glow rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Items</h3></div>
            <div class="p-6">
              <div v-for="item in (order.items || [])" :key="item.name" class="flex items-center justify-between py-2.5">
                <div>
                  <div class="text-sm font-medium text-nexus-200">{{ item.name }}</div>
                  <div class="text-xs text-nexus-600">Qty: {{ item.qty || 1 }}</div>
                </div>
                <div class="text-sm font-semibold text-nexus-300">€{{ ((item.qty || 1) * (item.price || 0)).toFixed(2) }}</div>
              </div>
              <div class="border-t border-white/[0.04] mt-3 pt-3 space-y-1.5">
                <div class="flex justify-between text-xs text-nexus-500"><span>Subtotal</span><span>€{{ Number(order.subtotal || 0).toFixed(2) }}</span></div>
                <div class="flex justify-between text-xs text-nexus-500"><span>Shipping</span><span>€{{ Number(order.shipping_cost || 0).toFixed(2) }}</span></div>
                <div class="flex justify-between text-base font-bold text-nexus-100 pt-1"><span>Total</span><span class="text-gradient">€{{ Number(order.total || 0).toFixed(2) }}</span></div>
              </div>
            </div>
          </div>

          <!-- Shipping -->
          <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Shipping Details</h3></div>
            <div class="p-6 grid grid-cols-2 gap-5">
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">City</div><div class="text-sm text-nexus-200">{{ order.shipping_city || '—' }}</div></div>
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">Neighborhood</div><div class="text-sm text-nexus-200">{{ order.shipping_neighborhood || '—' }}</div></div>
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">Street</div><div class="text-sm text-nexus-200">{{ order.shipping_street || '—' }}</div></div>
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">Notes</div><div class="text-sm text-nexus-200">{{ order.shipping_notes || '—' }}</div></div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5 stagger-children">
          <!-- Customer -->
          <div class="glass-glow rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Customer</h3></div>
            <div class="p-5">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-nexus-500/25 to-accent-cyan/20 flex items-center justify-center text-lg font-bold text-nexus-200">{{ order.customer_name?.[0] }}</div>
                <div>
                  <div class="text-sm font-medium text-nexus-100">{{ order.customer_name }}</div>
                  <div class="text-xs text-nexus-500">{{ order.customer_phone }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment -->
          <div class="glass rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Payment</h3></div>
            <div class="p-5 space-y-3">
              <div class="flex justify-between items-center"><span class="text-xs text-nexus-500">Method</span><span class="text-sm font-medium text-nexus-200 uppercase">{{ order.payment_method || 'COD' }}</span></div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-nexus-500">Status</span>
                <div class="flex items-center gap-2">
                  <StatusBadge :status="order.payment_status || 'pending'" />
                  <select @change="updatePaymentStatus($event.target.value)" class="input-premium text-[10px] py-1 px-1.5 w-auto cursor-pointer border-none bg-transparent hover:bg-white/5">
                    <option value="">Edit</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="glass rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Timeline</h3></div>
            <div class="p-5 space-y-3">
              <div class="flex items-center gap-3 text-xs">
                <div class="w-2 h-2 rounded-full bg-nexus-400"></div>
                <span class="text-nexus-400">Created {{ formatDate(order.created_at) }}</span>
              </div>
              <div v-if="order.dispatched_at" class="flex items-center gap-3 text-xs">
                <div class="w-2 h-2 rounded-full bg-accent-cyan"></div>
                <span class="text-nexus-400">Dispatched {{ formatDate(order.dispatched_at) }}</span>
              </div>
              <div v-if="order.delivered_at" class="flex items-center gap-3 text-xs">
                <div class="w-2 h-2 rounded-full bg-accent-emerald"></div>
                <span class="text-nexus-400">Delivered {{ formatDate(order.delivered_at) }}</span>
              </div>
              <div v-if="order.tracking_number" class="mt-3 pt-3 border-t border-white/[0.04]">
                <div class="text-[10px] text-nexus-600 uppercase tracking-wider mb-1">Tracking</div>
                <div class="text-xs font-mono text-accent-cyan">{{ order.tracking_number }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({ order: { type: Object, required: true } });

const statusOptions = ['confirmed', 'processing', 'dispatched', 'in_transit', 'delivered', 'returned', 'cancelled'];

function updateStatus(status) {
  if (!status) return;
  router.patch(`/orders/${props.order.id}/status`, { fulfillment_status: status }, { preserveState: true });
}

function updatePaymentStatus(status) {
  if (!status) return;
  router.patch(`/orders/${props.order.id}/status`, { payment_status: status }, { preserveState: true });
}

function formatDate(d) {
  return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
}
</script>
