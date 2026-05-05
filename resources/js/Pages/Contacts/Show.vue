<template>
  <AuthenticatedLayout title="Contact Details">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center gap-4">
        <button @click="$inertia.visit('/contacts')" class="p-2 rounded-lg hover:bg-surface-glass text-nexus-400 hover:text-nexus-200 transition-all">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-nexus-400 to-accent-cyan flex items-center justify-center text-lg font-bold text-white">{{ contact.name?.[0]?.toUpperCase() || '?' }}</div>
        <div>
          <h2 class="text-xl font-bold text-nexus-100">{{ contact.name || 'Unknown Contact' }}</h2>
          <div class="flex items-center gap-3 text-sm text-nexus-500">
            <PlatformIcon :platform="contact.platform" class="w-4 h-4" />
            <span>{{ contact.phone }}</span>
            <span v-if="contact.city">· {{ contact.city }}</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Edit form -->
        <div class="lg:col-span-2">
          <form @submit.prevent="save" class="glass rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-nexus-200 pb-3 border-b border-border-subtle">Contact Information</h3>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-xs font-medium text-nexus-400 mb-1">Name</label><input v-model="form.name" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all" /></div>
              <div><label class="block text-xs font-medium text-nexus-400 mb-1">Phone</label><input v-model="form.phone" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all" /></div>
              <div><label class="block text-xs font-medium text-nexus-400 mb-1">Email</label><input v-model="form.email" type="email" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all" /></div>
              <div><label class="block text-xs font-medium text-nexus-400 mb-1">City</label><input v-model="form.city" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all" /></div>
              <div><label class="block text-xs font-medium text-nexus-400 mb-1">Neighborhood</label><input v-model="form.neighborhood" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all" /></div>
              <div><label class="block text-xs font-medium text-nexus-400 mb-1">Street</label><input v-model="form.street_address" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all" /></div>
              <div class="col-span-2"><label class="block text-xs font-medium text-nexus-400 mb-1">Notes</label><textarea v-model="form.notes" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-nexus-950/60 border border-border-subtle text-sm text-nexus-200 focus:outline-none focus:border-nexus-500 transition-all resize-none"></textarea></div>
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 rounded-lg bg-nexus-600 hover:bg-nexus-500 text-white text-sm font-medium transition-all disabled:opacity-50">Save Changes</button>
          </form>
        </div>

        <!-- Stats sidebar -->
        <div class="space-y-6">
          <div class="glass rounded-xl p-5">
            <h3 class="text-sm font-semibold text-nexus-200 mb-3">Stats</h3>
            <div class="space-y-3">
              <div class="flex justify-between text-sm"><span class="text-nexus-500">Total Orders</span><span class="font-medium text-nexus-200">{{ contact.orders_count || 0 }}</span></div>
              <div class="flex justify-between text-sm"><span class="text-nexus-500">Total Spent</span><span class="font-medium text-nexus-200">€{{ Number(contact.total_spent || 0).toFixed(2) }}</span></div>
              <div class="flex justify-between text-sm"><span class="text-nexus-500">Conversations</span><span class="font-medium text-nexus-200">{{ contact.conversations_count || 0 }}</span></div>
            </div>
          </div>

          <div class="glass rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-border-subtle"><h3 class="text-sm font-semibold text-nexus-200">Recent Orders</h3></div>
            <div class="divide-y divide-border-subtle">
              <div v-for="o in contact.orders" :key="o.id" @click="$inertia.visit(`/orders/${o.id}`)" class="px-5 py-2.5 flex justify-between items-center hover:bg-surface-glass cursor-pointer transition-all">
                <div class="text-xs text-nexus-300">{{ o.order_number }}</div>
                <div class="text-xs font-medium text-nexus-200">€{{ Number(o.total).toFixed(2) }}</div>
              </div>
              <div v-if="!contact.orders?.length" class="px-5 py-6 text-center text-xs text-nexus-600">No orders</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';

const props = defineProps({ contact: { type: Object, required: true } });

const form = useForm({
  name: props.contact.name || '',
  phone: props.contact.phone || '',
  email: props.contact.email || '',
  city: props.contact.city || '',
  neighborhood: props.contact.neighborhood || '',
  street_address: props.contact.street_address || '',
  notes: props.contact.notes || '',
});

function save() { form.put(`/contacts/${props.contact.id}`); }
</script>
