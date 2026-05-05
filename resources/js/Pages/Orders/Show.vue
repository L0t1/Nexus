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
          <button v-if="$page.props.auth?.user?.roles?.includes('admin')" @click="showDeleteModal = true" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 border-red-500/20">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            Delete
          </button>
          <StatusBadge :status="order.fulfillment_status" />
          <div :class="['relative', showStatusMenu ? 'z-50' : '']">
            <button @click="showStatusMenu = !showStatusMenu" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
              Change status <svg class="w-3.5 h-3.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-if="showStatusMenu" @click="showStatusMenu = false" class="fixed inset-0 z-40"></div>
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0 scale-95">
              <div v-if="showStatusMenu" class="absolute right-0 top-full mt-2 w-48 glass-strong rounded-xl overflow-hidden shadow-2xl z-50 py-1 border border-white/10">
                <button v-for="s in statusOptions" :key="s" @click="updateStatus(s)" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 capitalize transition-colors">{{ s.replace('_', ' ') }}</button>
              </div>
            </Transition>
          </div>
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
            <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
              <h3 class="text-sm font-semibold text-nexus-200">Shipping Details</h3>
              <button @click="copyShippingInfo" class="btn-secondary text-[10px] py-1.5 px-3 flex items-center gap-1.5">
                <svg v-if="copied" class="w-3.5 h-3.5 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                {{ copied ? 'Copied!' : 'Copy for Courier' }}
              </button>
            </div>
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
          <div :class="['glass rounded-2xl transition-all', showPaymentMenu ? 'relative z-50' : '']">
            <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Payment</h3></div>
            <div class="p-5 space-y-3">
              <div class="flex justify-between items-center"><span class="text-xs text-nexus-500">Method</span><span class="text-sm font-medium text-nexus-200 uppercase">{{ order.payment_method || 'COD' }}</span></div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-nexus-500">Status</span>
                <div :class="['flex items-center gap-2 relative', showPaymentMenu ? 'z-50' : '']">
                  <StatusBadge :status="order.payment_status || 'pending'" />
                  <button @click="showPaymentMenu = !showPaymentMenu" class="btn-secondary text-[10px] py-1 px-2 flex items-center gap-1">
                    ✎ Edit
                  </button>
                  <div v-if="showPaymentMenu" @click="showPaymentMenu = false" class="fixed inset-0 z-40"></div>
                  <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0 scale-95">
                    <div v-if="showPaymentMenu" class="absolute right-0 top-full mt-2 w-40 glass-strong rounded-xl overflow-hidden shadow-2xl z-50 py-1 border border-white/10">
                      <button @click="updatePaymentStatus('pending')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">Pending</button>
                      <button @click="updatePaymentStatus('collected')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">Collected / Paid</button>
                      <button @click="updatePaymentStatus('returned')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">Returned / Failed</button>
                      <button @click="updatePaymentStatus('refunded')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">Refunded</button>
                    </div>
                  </Transition>
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

      <ConfirmModal 
        :show="showDeleteModal" 
        title="Delete Order?" 
        message="Are you sure you want to permanently delete this order? This action cannot be undone." 
        @close="showDeleteModal = false" 
        @confirm="confirmDelete" 
      />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({ order: { type: Object, required: true } });

const statusOptions = ['confirmed', 'processing', 'dispatched', 'in_transit', 'delivered', 'returned', 'cancelled'];
const showStatusMenu = ref(false);
const showPaymentMenu = ref(false);
const showDeleteModal = ref(false);
const copied = ref(false);

function copyShippingInfo() {
  const o = props.order;
  const address = [o.shipping_street, o.shipping_neighborhood, o.shipping_city].filter(Boolean).join(', ');
  const items = (o.items || []).map(i => `${i.qty}x ${i.name}`).join(', ');
  
  const text = `*Order: ${o.order_number}*
*Name:* ${o.customer_name}
*Phone:* ${o.customer_phone}
*Address:* ${address}
*Items:* ${items}
*Total:* €${Number(o.total || 0).toFixed(2)}
*Notes:* ${o.shipping_notes || 'None'}`;

  navigator.clipboard.writeText(text);
  copied.value = true;
  setTimeout(() => copied.value = false, 2000);
}

function confirmDelete() {
  showDeleteModal.value = false;
  router.delete(`/orders/${props.order.id}`);
}

function updateStatus(status) {
  if (!status) return;
  showStatusMenu.value = false;
  router.patch(`/orders/${props.order.id}/status`, { fulfillment_status: status }, { preserveState: true });
}

function updatePaymentStatus(status) {
  if (!status) return;
  showPaymentMenu.value = false;
  router.patch(`/orders/${props.order.id}/status`, { payment_status: status }, { preserveState: true });
}

function formatDate(d) {
  return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
}
</script>
