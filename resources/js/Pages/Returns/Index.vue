<template>
  <AuthenticatedLayout :title="$t('pageTitles.returns')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('returns.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('returns.subtitle') }}</p></div>
      </div>

      <!-- Status tabs -->
      <div class="flex gap-1.5 p-1 rounded-xl bg-white/[0.02] border border-white/[0.04] w-full sm:w-fit overflow-x-auto hide-scrollbar">
        <button v-for="f in statusFilters" :key="f.value" @click="setFilter(f.value)" :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap', activeFilter === f.value ? 'bg-nexus-500/15 text-nexus-300' : 'text-nexus-600 hover:text-nexus-400']">
          {{ $t('returns.' + (f.value || 'all')) }} <span v-if="statusCounts[f.value]" class="ml-1 text-nexus-600">({{ statusCounts[f.value] }})</span>
        </button>
      </div>

      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-premium min-w-[700px]">
            <thead><tr><th>{{ $t('returns.returnNo') }}</th><th>{{ $t('returns.order') }}</th><th>{{ $t('returns.customer') }}</th><th>{{ $t('returns.reason') }}</th><th>{{ $t('returns.status') }}</th><th class="text-right">{{ $t('returns.refund') }}</th><th class="text-right">{{ $t('returns.actions') }}</th></tr></thead>
            <tbody>
              <tr v-for="r in returns?.data" :key="r.id" class="group">
                <td><span class="font-mono text-xs text-accent-rose">{{ r.return_number }}</span></td>
                <td><span class="font-mono text-xs text-nexus-400 cursor-pointer hover:text-nexus-200" @click="$inertia.visit(`/orders/${r.order?.id}`)">{{ r.order?.order_number }}</span></td>
                <td class="text-sm text-nexus-300">{{ r.order?.customer_name }}</td>
                <td><span class="text-xs px-2 py-0.5 rounded-full bg-nexus-500/10 text-nexus-400 capitalize">{{ r.reason?.replace('_', ' ') }}</span></td>
                <td><span :class="['text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full font-semibold', statusClass(r.status)]">{{ r.status }}</span></td>
                <td class="text-right text-sm text-nexus-300">{{ r.refund_amount ? '€' + Number(r.refund_amount).toFixed(2) : '—' }}</td>
                <td class="text-right">
                  <div class="flex gap-1 justify-end">
                    <button v-if="r.status === 'requested'" @click="updateReturn(r.id, 'approved')" class="btn-secondary text-[10px] py-1 px-2 text-accent-emerald">{{ $t('returns.approve') }}</button>
                    <button v-if="r.status === 'requested'" @click="updateReturn(r.id, 'rejected')" class="btn-secondary text-[10px] py-1 px-2 text-accent-rose">{{ $t('returns.reject') }}</button>
                    <button v-if="r.status === 'approved'" @click="updateReturn(r.id, 'received')" class="btn-secondary text-[10px] py-1 px-2">{{ $t('returns.markReceived') }}</button>
                    <button v-if="r.status === 'received'" @click="openRefund(r)" class="btn-primary text-[10px] py-1 px-2">{{ $t('returns.processRefund') }}</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="!returns?.data?.length" class="p-16 text-center"><p class="text-nexus-600 text-sm">{{ $t('returns.noReturns') }}</p></div>
      </div>

      <!-- Refund Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showRefundModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showRefundModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ $t('returns.processRefund') }}</h3>
            <form @submit.prevent="processRefund" novalidate class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('returns.refundAmount') }} (€)</label>
                <input v-model.number="refundForm.refund_amount" type="number" step="0.01" min="0.01" :class="['input-premium', refundForm.errors.refund_amount ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
                <p v-if="refundForm.errors.refund_amount" class="mt-1 text-xs text-accent-rose">{{ refundForm.errors.refund_amount }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('returns.refundMethod') }}</label>
                <select v-model="refundForm.refund_method" class="input-premium">
                  <option value="original">{{ $t('returns.originalMethod') }}</option>
                  <option value="store_credit">{{ $t('returns.storeCredit') }}</option>
                  <option value="bank_transfer">{{ $t('returns.bankTransfer') }}</option>
                  <option value="cash">{{ $t('returns.cash') }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('returns.notes') }}</label>
                <textarea v-model="refundForm.admin_notes" rows="2" class="input-premium resize-none"></textarea>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showRefundModal = false" class="btn-secondary text-sm px-4 py-2">{{ $t('common.cancel') }}</button>
                <button type="submit" :disabled="refundForm.processing" class="btn-primary text-sm px-4 py-2">
                  {{ refundForm.processing ? $t('common.processing') || '...' : $t('returns.refundBtn') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({ returns: Object, statusCounts: Object, filters: Object });
const activeFilter = ref(props.filters?.status || '');
const statusFilters = [{ label: 'All', value: '' }, { label: 'Requested', value: 'requested' }, { label: 'Approved', value: 'approved' }, { label: 'Received', value: 'received' }, { label: 'Refunded', value: 'refunded' }];
const showRefundModal = ref(false);
const refundReturnId = ref(null);
const refundForm = useForm({ refund_amount: 0, refund_method: 'original', admin_notes: '', status: 'refunded' });
function setFilter(v) { activeFilter.value = v; router.get('/returns', { status: v }, { preserveState: true }); }
function updateReturn(id, status) { router.patch(`/returns/${id}/status`, { status }); }
function openRefund(r) { refundReturnId.value = r.id; refundForm.refund_amount = Number(r.order?.total || 0); showRefundModal.value = true; }
function processRefund() { refundForm.patch(`/returns/${refundReturnId.value}/status`, { onSuccess: () => showRefundModal.value = false }); }
function statusClass(s) { return { requested: 'bg-accent-amber/10 text-accent-amber', approved: 'bg-accent-cyan/10 text-accent-cyan', rejected: 'bg-accent-rose/10 text-accent-rose', received: 'bg-accent-violet/10 text-accent-violet', refunded: 'bg-accent-emerald/10 text-accent-emerald', exchanged: 'bg-nexus-500/10 text-nexus-400' }[s] || ''; }
</script>
