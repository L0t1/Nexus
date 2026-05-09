<template>
  <AuthenticatedLayout :title="$t('pageTitles.waCatalog')">
    <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('waCatalog.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('waCatalog.subtitle') }}</p></div>
        <button @click="syncCatalog" :disabled="syncing || !isConnected" :class="['btn-primary flex items-center gap-2 text-sm', syncing ? 'opacity-50' : '']">
          <svg :class="['w-4 h-4', syncing ? 'animate-spin' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" /></svg>
          {{ syncing ? $t('waCatalog.syncing') : $t('waCatalog.syncNow') }}
        </button>
      </div>

      <!-- Connection Status -->
      <div :class="['glass-glow rounded-2xl p-5', isConnected ? 'border-accent-emerald/20' : 'border-accent-amber/20']">
        <div class="flex items-center gap-3">
          <div :class="['w-3 h-3 rounded-full', isConnected ? 'bg-accent-emerald animate-pulse' : 'bg-accent-amber']"></div>
          <div>
            <h3 class="text-sm font-semibold" :class="isConnected ? 'text-accent-emerald' : 'text-accent-amber'">{{ isConnected ? $t('waCatalog.connected') : $t('waCatalog.notConnected') }}</h3>
            <p class="text-xs text-nexus-500">{{ isConnected ? `${$t('waCatalog.catalogId')}: ${catalogId}` : $t('waCatalog.setCatalogId') }}</p>
          </div>
        </div>
        <div v-if="!isConnected" class="mt-4">
          <form @submit.prevent="saveCatalog" class="flex gap-2">
            <input v-model="newCatalogId" type="text" class="input-premium flex-1 text-sm" :placeholder="$t('waCatalog.idPlaceholder')" />
            <button type="submit" class="btn-primary text-sm px-4">{{ $t('waCatalog.save') }}</button>
          </form>
        </div>
        <div v-if="lastSyncAt" class="mt-2 text-xs text-nexus-600">{{ $t('waCatalog.lastSynced') }}: {{ formatDate(lastSyncAt) }}</div>
      </div>

      <!-- Sync Result -->
      <div v-if="syncResult" :class="['glass rounded-2xl p-5', syncResult.success ? 'border-accent-emerald/20' : 'border-accent-rose/20']">
        <h3 :class="['text-sm font-semibold', syncResult.success ? 'text-accent-emerald' : 'text-accent-rose']">{{ syncResult.success ? $t('waCatalog.syncComplete') : $t('waCatalog.syncFailed') }}</h3>
        <p class="text-sm text-nexus-300 mt-1">{{ syncResult.synced }} / {{ syncResult.total }} {{ $t('waCatalog.productsSynced') }}</p>
        <div v-if="syncResult.errors?.length" class="mt-2 space-y-1">
          <div v-for="err in syncResult.errors" :key="err" class="text-xs text-accent-rose">• {{ err }}</div>
        </div>
      </div>

      <!-- Product List -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('waCatalog.productsToSync') }} ({{ products.length }})</h3></div>
        <div class="divide-y divide-white/[0.03]">
          <div v-for="p in products" :key="p.id" class="px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-nexus-800 overflow-hidden">
                <img v-if="p.image_url" :src="p.image_url" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-nexus-600 text-sm">📦</div>
              </div>
              <div>
                <div class="text-sm text-nexus-200">{{ p.name }}</div>
                <div class="text-xs text-nexus-500">{{ p.sku || $t('waCatalog.noSku') }} · {{ $t('waCatalog.stock') }}: {{ p.stock }}</div>
              </div>
            </div>
            <span class="text-sm font-medium text-nexus-300">€{{ Number(p.price).toFixed(2) }}</span>
          </div>
          <div v-if="!products.length" class="px-6 py-12 text-center text-sm text-nexus-600">{{ $t('waCatalog.noActiveProducts') }}</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({ products: Array, isConnected: Boolean, catalogId: String, lastSyncAt: String });
const syncing = ref(false);
const syncResult = ref(null);
const newCatalogId = ref('');
async function syncCatalog() {
  syncing.value = true;
  try {
    const res = await fetch('/whatsapp-catalog/sync', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content, 'Accept': 'application/json', 'Content-Type': 'application/json' } });
    syncResult.value = await res.json();
  } catch (e) { syncResult.value = { success: false, error: e.message }; }
  syncing.value = false;
}
function saveCatalog() { router.post('/whatsapp-catalog/settings', { catalog_id: newCatalogId.value }); }
function formatDate(d) { return d ? new Date(d).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : ''; }
</script>
