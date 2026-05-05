<template>
  <AuthenticatedLayout title="Contacts">
    <div class="space-y-5 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">Contacts</h2><p class="text-nexus-500 text-sm mt-1">Your customer database</p></div>
      </div>
      <div class="relative max-w-md group">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-nexus-600 group-focus-within:text-nexus-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        <input v-model="search" type="text" placeholder="Search contacts..." class="input-premium pl-10" />
      </div>

      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-premium min-w-[800px]">
          <thead><tr><th>Contact</th><th>Platform</th><th>City</th><th>Orders</th><th class="text-right">Total Spent</th><th class="text-right">Last Contact</th></tr></thead>
          <tbody>
            <tr v-for="c in contacts?.data" :key="c.id" class="cursor-pointer group" @click="$inertia.visit(`/contacts/${c.id}`)">
              <td>
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent-violet/20 to-accent-pink/20 flex items-center justify-center text-xs font-bold text-nexus-300">{{ c.name?.[0]?.toUpperCase() }}</div>
                  <div><div class="text-sm text-nexus-200 group-hover:text-nexus-100 transition-colors">{{ c.name }}</div><div class="text-[10px] text-nexus-600">{{ c.phone }}</div></div>
                </div>
              </td>
              <td><PlatformIcon :platform="c.platform" class="w-4 h-4" /></td>
              <td class="text-sm text-nexus-400">{{ c.city || '—' }}</td>
              <td><span class="text-sm font-medium text-nexus-200">{{ c.total_orders || 0 }}</span></td>
              <td class="text-right text-sm font-medium text-nexus-200">€{{ Number(c.total_spent || 0).toFixed(2) }}</td>
              <td class="text-right text-xs text-nexus-600">{{ formatDate(c.last_contacted_at) }}</td>
            </tr>
          </tbody>
          </table>
        </div>
        <div v-if="!contacts?.data?.length" class="p-16 text-center">
          <div class="w-16 h-16 rounded-2xl bg-nexus-500/5 flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg></div>
          <p class="text-nexus-600 text-sm">No contacts found</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';

const props = defineProps({ contacts: { type: Object, default: () => ({ data: [] }) }, filters: Object });
const search = ref(props.filters?.search || '');
let timeout = null;
watch(search, val => { clearTimeout(timeout); timeout = setTimeout(() => { router.get('/contacts', { search: val }, { preserveState: true }); }, 300); });
function formatDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }) : '—'; }
</script>
