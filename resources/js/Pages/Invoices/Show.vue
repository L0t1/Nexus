<template>
  <AuthenticatedLayout :title="$t('pageTitles.invoice')">
    <div class="max-w-3xl mx-auto animate-fade-in">
      <!-- Print button -->
      <div class="flex items-center justify-between mb-6 no-print">
        <button @click="$inertia.visit('/invoices')" class="btn-secondary text-xs flex items-center gap-1.5">← {{ $t('invoices.allInvoices') }}</button>
        <div class="flex gap-2">
          <div class="flex flex-col items-end gap-2">
            <button @click="printInvoice" class="btn-primary text-sm flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>
              {{ $t('invoices.print') }}
            </button>
            <span class="text-[10px] text-nexus-600 italic">{{ $t('invoices.printHint') }}</span>
          </div>
        </div>
      </div>

      <!-- Invoice Document -->
      <div id="invoice-doc" class="bg-white rounded-2xl shadow-2xl p-10 text-gray-900">
        <!-- Header -->
        <div class="flex justify-between items-start mb-10">
          <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $t('invoices.invoiceTitle') }}</h1>
            <div class="text-sm text-gray-500 mt-1 font-mono">{{ invoice.invoice_number }}</div>
          </div>
          <div class="text-right">
            <div class="text-lg font-bold text-gray-900">{{ invoice.merchant_details?.name }}</div>
            <div class="text-sm text-gray-500">{{ invoice.merchant_details?.phone }}</div>
            <div class="text-sm text-gray-500">{{ invoice.merchant_details?.email }}</div>
            <div class="text-sm text-gray-500">{{ invoice.merchant_details?.city }}, {{ invoice.merchant_details?.country }}</div>
          </div>
        </div>

        <!-- Info row -->
        <div class="grid grid-cols-3 gap-6 mb-8 text-sm">
          <div>
            <div class="font-semibold text-gray-400 uppercase text-[10px] tracking-wider mb-1">{{ $t('invoices.billTo') }}</div>
            <div class="font-semibold text-gray-900">{{ invoice.customer_details?.name }}</div>
            <div class="text-gray-500">{{ invoice.customer_details?.phone }}</div>
            <div class="text-gray-500">{{ invoice.customer_details?.address }}</div>
          </div>
          <div>
            <div class="font-semibold text-gray-400 uppercase text-[10px] tracking-wider mb-1">{{ $t('invoices.order') }}</div>
            <div class="font-mono font-semibold text-gray-900">{{ invoice.order?.order_number }}</div>
          </div>
          <div class="text-right">
            <div class="font-semibold text-gray-400 uppercase text-[10px] tracking-wider mb-1">{{ $t('invoices.dates') }}</div>
            <div class="text-gray-700">{{ $t('invoices.issued') }}: {{ formatDate(invoice.issued_at) }}</div>
            <div class="text-gray-500">{{ $t('invoices.due') }}: {{ formatDate(invoice.due_at) }}</div>
          </div>
        </div>

        <!-- Items Table -->
        <table class="w-full mb-6">
          <thead>
            <tr class="border-b-2 border-gray-200">
              <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase">{{ $t('invoices.item') }}</th>
              <th class="text-center py-3 text-xs font-semibold text-gray-500 uppercase">{{ $t('invoices.qty') }}</th>
              <th class="text-right py-3 text-xs font-semibold text-gray-500 uppercase">{{ $t('invoices.price') }}</th>
              <th class="text-right py-3 text-xs font-semibold text-gray-500 uppercase">{{ $t('invoices.total') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in invoice.order?.items || []" :key="item.name" class="border-b border-gray-100">
              <td class="py-3 text-sm font-medium text-gray-900">{{ item.name }}</td>
              <td class="py-3 text-sm text-center text-gray-600">{{ item.qty }}</td>
              <td class="py-3 text-sm text-right text-gray-600">{{ currency }}{{ Number(item.price).toFixed(2) }}</td>
              <td class="py-3 text-sm text-right font-medium text-gray-900">{{ currency }}{{ ((item.qty || 1) * (item.price || 0)).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end">
          <div class="w-64 space-y-2">
            <div class="flex justify-between text-sm text-gray-600"><span>{{ $t('invoices.subtotal') }}</span><span>{{ currency }}{{ Number(invoice.subtotal).toFixed(2) }}</span></div>
            <div v-if="Number(invoice.discount_amount) > 0" class="flex justify-between text-sm text-green-600"><span>{{ $t('invoices.discount') }}</span><span>-{{ currency }}{{ Number(invoice.discount_amount).toFixed(2) }}</span></div>
            <div v-if="Number(invoice.tax_amount) > 0" class="flex justify-between text-sm text-gray-600"><span>{{ $t('invoices.tax') }}</span><span>{{ currency }}{{ Number(invoice.tax_amount).toFixed(2) }}</span></div>
            <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t-2 border-gray-900"><span>{{ $t('invoices.total') }}</span><span>{{ currency }}{{ Number(invoice.total).toFixed(2) }}</span></div>
          </div>
        </div>

        <!-- Notes -->
        <div v-if="invoice.notes" class="mt-8 p-4 bg-gray-50 rounded-lg">
          <div class="text-xs font-semibold text-gray-400 uppercase mb-1">{{ $t('invoices.notes') }}</div>
          <div class="text-sm text-gray-600">{{ invoice.notes }}</div>
        </div>

        <!-- Footer -->
        <div class="mt-10 pt-6 border-t border-gray-200 text-center text-xs text-gray-400">
          {{ $t('invoices.generatedBy') }} · {{ invoice.merchant_details?.name }}
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ invoice: { type: Object, required: true } });

const currency = computed(() => {
  const sym = { EUR: '€', USD: '$', GBP: '£', MAD: 'MAD ', TND: 'TND ', DZD: 'DZD ' };
  return sym[props.invoice.currency] || props.invoice.currency + ' ';
});

function formatDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'; }
function printInvoice() { window.print(); }
</script>

<style>
@media print {
  .no-print { display: none !important; }
  nav, aside, header, footer, .fixed, .sticky { display: none !important; }
  body, html { background: white !important; margin: 0 !important; padding: 0 !important; -webkit-print-color-adjust: exact; }
  .mesh-gradient { background: white !important; }
  /* Reset AuthenticatedLayout margins */
  .lg\:ml-\[240px\], .lg\:ml-\[72px\], .ml-\[240px\], .ml-\[72px\] { margin-left: 0 !important; margin: 0 !important; }
  #invoice-doc { box-shadow: none !important; border: none !important; border-radius: 0 !important; width: 100% !important; margin: 0 !important; padding: 2cm !important; }
  .animate-fade-in { animation: none !important; transform: none !important; }
  .max-w-3xl { max-width: none !important; }
}
</style>
