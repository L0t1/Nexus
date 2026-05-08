<template>
  <AuthenticatedLayout title="Invoices">
    <div class="space-y-5 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">Invoices</h2><p class="text-nexus-500 text-sm mt-1">All generated invoices</p></div>
      </div>
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-premium">
            <thead><tr><th>Invoice</th><th>Order</th><th>Customer</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
              <tr v-for="inv in invoices?.data" :key="inv.id" class="cursor-pointer group" @click="$inertia.visit(`/invoices/${inv.id}`)">
                <td><span class="font-mono text-xs text-accent-cyan">{{ inv.invoice_number }}</span></td>
                <td><span class="font-mono text-xs text-nexus-400">{{ inv.order?.order_number }}</span></td>
                <td class="text-sm text-nexus-300">{{ inv.order?.customer_name }}</td>
                <td class="text-xs text-nexus-500">{{ formatDate(inv.issued_at) }}</td>
                <td class="text-right text-sm font-medium text-nexus-200">€{{ Number(inv.total).toFixed(2) }}</td>
                <td class="text-right"><button @click.stop="$inertia.visit(`/invoices/${inv.id}`)" class="btn-secondary text-[10px] py-1 px-2">View</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="!invoices?.data?.length" class="p-16 text-center"><p class="text-nexus-600 text-sm">No invoices generated yet. Generate one from an order detail page.</p></div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineProps({ invoices: { type: Object, default: () => ({ data: [] }) } });
function formatDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }
</script>
