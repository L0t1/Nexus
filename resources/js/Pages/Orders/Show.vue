<template>
  <AuthenticatedLayout :title="$t('orderDetail.title')">
    <div class="animate-fade-in space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$inertia.visit('/orders')" class="p-2 rounded-xl hover:bg-surface-hover text-nexus-500 hover:text-nexus-300 transition-all"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg></button>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-xl font-bold font-mono text-nexus-100 tracking-tight">{{ order.order_number }}</h2>
              <div v-if="order.is_ai_generated" class="px-2 py-0.5 rounded-full bg-accent-violet/10 border border-accent-violet/20 text-[10px] font-bold text-accent-violet uppercase tracking-wider flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12" /></svg>
                {{ $t('orderDetail.aiGenerated') }}
              </div>
            </div>
            <p class="text-xs text-nexus-500 mt-0.5">{{ formatDate(order.created_at) }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button v-if="order.conversation_id" @click="$inertia.visit(`/inbox/${order.conversation_id}`)" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 border-nexus-500/20 text-nexus-300 hover:bg-nexus-500/10">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
            {{ $t('orderDetail.viewChat') }}
          </button>
          <a v-if="order.invoice" :href="`/invoices/${order.invoice.id}`" target="_blank" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 border-accent-cyan/20 text-accent-cyan hover:bg-accent-cyan/10">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>
            {{ $t('orderDetail.printInvoice') }}
          </a>
          <button v-else @click="generateInvoice" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            {{ $t('orderDetail.generateInvoice') }}
          </button>
          <button @click="sendSurvey" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            CSAT
          </button>
          <button @click="showReturnModal = true" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 text-accent-rose hover:bg-accent-rose/10">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
            {{ $t('orderDetail.return') }}
          </button>
          <button v-if="$page.props.auth?.user?.roles?.includes('admin')" @click="showDeleteModal = true" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 border-red-500/20">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            {{ $t('orderDetail.delete') }}
          </button>
          <StatusBadge :status="order.fulfillment_status" />
          <div :class="['relative', showStatusMenu ? 'z-50' : '']">
            <button @click="showStatusMenu = !showStatusMenu" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
              {{ $t('orderDetail.changeStatus') }} <svg class="w-3.5 h-3.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
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
            <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('orderDetail.items') }}</h3></div>
            <div class="p-6">
              <div v-for="item in (order.items || [])" :key="item.name" class="flex items-center justify-between py-2.5">
                <div>
                  <div class="text-sm font-medium text-nexus-200">{{ item.name }}</div>
                  <div class="text-xs text-nexus-600">{{ $t('orderDetail.qty') }}: {{ item.qty || 1 }}</div>
                </div>
                <div class="text-sm font-semibold text-nexus-300">€{{ ((item.qty || 1) * (item.price || 0)).toFixed(2) }}</div>
              </div>
              <div class="border-t border-white/[0.04] mt-3 pt-3 space-y-1.5">
                <div class="flex justify-between text-xs text-nexus-500"><span>{{ $t('orderDetail.subtotal') }}</span><span>€{{ Number(order.subtotal || 0).toFixed(2) }}</span></div>
                <div class="flex justify-between text-xs text-nexus-500"><span>{{ $t('orderDetail.shipping') }}</span><span>€{{ Number(order.shipping_cost || 0).toFixed(2) }}</span></div>
                <div class="flex justify-between text-base font-bold text-nexus-100 pt-1"><span>{{ $t('orderDetail.total') }}</span><span class="text-gradient">€{{ Number(order.total || 0).toFixed(2) }}</span></div>
              </div>
            </div>
          </div>

          <!-- Shipping -->
          <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
              <h3 class="text-sm font-semibold text-nexus-200">{{ $t('orderDetail.shippingDetails') }}</h3>
              <button @click="copyShippingInfo" class="btn-secondary text-[10px] py-1.5 px-3 flex items-center gap-1.5">
                <svg v-if="copied" class="w-3.5 h-3.5 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                {{ copied ? $t('orderDetail.copied') : $t('orderDetail.copyForCourier') }}
              </button>
            </div>
            <div class="p-6 grid grid-cols-2 gap-5">
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">{{ $t('orderDetail.city') }}</div><div class="text-sm text-nexus-200">{{ order.shipping_city || '—' }}</div></div>
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">{{ $t('orderDetail.neighborhood') }}</div><div class="text-sm text-nexus-200">{{ order.shipping_neighborhood || '—' }}</div></div>
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">{{ $t('orderDetail.street') }}</div><div class="text-sm text-nexus-200">{{ order.shipping_street || '—' }}</div></div>
              <div><div class="text-[10px] font-semibold text-nexus-600 uppercase tracking-wider mb-1">{{ $t('orderDetail.notes') }}</div><div class="text-sm text-nexus-200">{{ order.shipping_notes || '—' }}</div></div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5 stagger-children">
          <!-- Customer -->
          <div class="glass-glow rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('orderDetail.customer') }}</h3></div>
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
            <div class="px-5 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('orderDetail.payment') }}</h3></div>
            <div class="p-5 space-y-3">
              <div class="flex justify-between items-center"><span class="text-xs text-nexus-500">{{ $t('orderDetail.method') }}</span><span class="text-sm font-medium text-nexus-200 uppercase">{{ $t('paymentMethods.' + (order.payment_method || 'cod')) }}</span></div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-nexus-500">{{ $t('orderDetail.status') }}</span>
                <div :class="['flex items-center gap-2 relative', showPaymentMenu ? 'z-50' : '']">
                  <StatusBadge :status="order.payment_status || 'pending'" />
                  <button @click="showPaymentMenu = !showPaymentMenu" class="btn-secondary text-[10px] py-1 px-2 flex items-center gap-1">
                    ✎ {{ $t('orderDetail.edit') }}
                  </button>
                  <div v-if="showPaymentMenu" @click="showPaymentMenu = false" class="fixed inset-0 z-40"></div>
                  <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0 scale-95">
                    <div v-if="showPaymentMenu" class="absolute right-0 top-full mt-2 w-40 glass-strong rounded-xl overflow-hidden shadow-2xl z-50 py-1 border border-white/10">
                      <button @click="updatePaymentStatus('pending')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">{{ $t('orderDetail.pending') }}</button>
                      <button @click="updatePaymentStatus('collected')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">{{ $t('orderDetail.collectedPaid') }}</button>
                      <button @click="updatePaymentStatus('returned')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">{{ $t('orderDetail.returnedFailed') }}</button>
                      <button @click="updatePaymentStatus('refunded')" class="w-full text-left px-4 py-2 text-xs hover:bg-white/5 text-nexus-200 transition-colors">{{ $t('orderDetail.refunded') }}</button>
                    </div>
                  </Transition>
                </div>
              </div>
            </div>
          </div>

          <!-- Audit Timeline -->
          <div class="glass rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/[0.04] flex items-center gap-2">
              <svg class="w-4 h-4 text-nexus-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              <h3 class="text-sm font-semibold text-nexus-200">{{ $t('orderDetail.activityTimeline') }}</h3>
            </div>
            <div class="p-5">
              <div class="relative">
                <!-- Vertical line -->
                <div class="absolute left-[7px] top-2 bottom-2 w-px bg-gradient-to-b from-nexus-500/30 via-nexus-600/20 to-transparent"></div>
                
                <div v-for="(event, i) in auditEvents" :key="event.id || i" class="relative pl-7 pb-4 last:pb-0">
                  <!-- Dot -->
                  <div :class="['absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full border-2 transition-all', eventDotClass(event)]"></div>
                  
                  <div class="text-sm text-nexus-200 leading-snug">{{ event.description }}</div>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] text-nexus-600">{{ formatDate(event.created_at) }}</span>
                    <span v-if="event.user" class="text-[10px] text-nexus-500 flex items-center gap-1">
                      <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" /></svg>
                      {{ event.user.name }}
                    </span>
                    <span v-else-if="event.is_system" class="text-[10px] text-accent-violet/60 flex items-center gap-1">
                      <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12" /></svg>
                      {{ $t('orderDetail.system') }}
                    </span>
                  </div>
                </div>

                <!-- Fallback for orders without events -->
                <div v-if="!auditEvents.length" class="space-y-3">
                  <div class="relative pl-7">
                    <div class="absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full border-2 border-nexus-500/40 bg-nexus-500/10"></div>
                    <div class="text-sm text-nexus-400">{{ $t('orderDetail.created') }} {{ formatDate(order.created_at) }}</div>
                  </div>
                  <div v-if="order.dispatched_at" class="relative pl-7">
                    <div class="absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full border-2 border-accent-cyan/40 bg-accent-cyan/10"></div>
                    <div class="text-sm text-nexus-400">{{ $t('orderDetail.dispatched') }} {{ formatDate(order.dispatched_at) }}</div>
                  </div>
                  <div v-if="order.delivered_at" class="relative pl-7">
                    <div class="absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full border-2 border-accent-emerald/40 bg-accent-emerald/10"></div>
                    <div class="text-sm text-nexus-400">{{ $t('orderDetail.delivered') }} {{ formatDate(order.delivered_at) }}</div>
                  </div>
                </div>

                <div v-if="order.tracking_number" class="mt-3 pt-3 border-t border-white/[0.04]">
                  <div class="text-[10px] text-nexus-600 uppercase tracking-wider mb-1">{{ $t('orderDetail.tracking') }}</div>
                  <div class="text-xs font-mono text-accent-cyan">{{ order.tracking_number }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <ConfirmModal 
        :show="showDeleteModal" 
        :title="$t('orderDetail.deleteTitle')" 
        :message="$t('orderDetail.deleteMessage')" 
        @close="showDeleteModal = false" 
        @confirm="confirmDelete" 
      />

      <!-- Return Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showReturnModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ $t('orderDetail.requestReturn') }}</h3>
            <form @submit.prevent="submitReturn" class="space-y-4">
              <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('orderDetail.reason') }}</label>
                <select v-model="returnForm.reason" class="input-premium"><option value="defective">{{ $t('orderDetail.defective') }}</option><option value="wrong_item">{{ $t('orderDetail.wrongItem') }}</option><option value="not_as_described">{{ $t('orderDetail.notAsDescribed') }}</option><option value="changed_mind">{{ $t('orderDetail.changedMind') }}</option><option value="late_delivery">{{ $t('orderDetail.lateDelivery') }}</option><option value="other">{{ $t('orderDetail.other') }}</option></select>
              </div>
              <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('orderDetail.description') }}</label><textarea v-model="returnForm.description" rows="3" class="input-premium resize-none" :placeholder="$t('orderDetail.detailsPlaceholder')"></textarea></div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showReturnModal = false" class="btn-secondary text-sm px-4 py-2">{{ $t('common.cancel') }}</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">{{ $t('orderDetail.submitReturn') }}</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({ order: { type: Object, required: true } });

const statusOptions = ['confirmed', 'processing', 'dispatched', 'in_transit', 'delivered', 'returned', 'cancelled'];
const showStatusMenu = ref(false);
const showPaymentMenu = ref(false);
const showDeleteModal = ref(false);
const showReturnModal = ref(false);
const copied = ref(false);
const returnForm = reactive({ reason: 'other', description: '' });

const auditEvents = computed(() => props.order.events || []);

function eventDotClass(event) {
  const type = event.type;
  if (type === 'created') return 'border-nexus-400/50 bg-nexus-400/15';
  if (type === 'status_change') {
    const to = event.metadata?.to;
    if (to === 'delivered') return 'border-accent-emerald/50 bg-accent-emerald/15';
    if (to === 'dispatched' || to === 'in_transit') return 'border-accent-cyan/50 bg-accent-cyan/15';
    if (to === 'returned' || to === 'cancelled') return 'border-accent-rose/50 bg-accent-rose/15';
    return 'border-accent-amber/50 bg-accent-amber/15';
  }
  if (type === 'payment_change') return 'border-accent-violet/50 bg-accent-violet/15';
  if (type === 'dispatched') return 'border-accent-cyan/50 bg-accent-cyan/15';
  return 'border-nexus-500/30 bg-nexus-500/10';
}

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

function generateInvoice() {
  router.post(`/orders/${props.order.id}/invoice`);
}

function sendSurvey() {
  router.post(`/orders/${props.order.id}/survey`);
}

function submitReturn() {
  router.post(`/orders/${props.order.id}/return`, { ...returnForm }, {
    onSuccess: () => { showReturnModal.value = false; returnForm.reason = 'other'; returnForm.description = ''; }
  });
}
</script>
