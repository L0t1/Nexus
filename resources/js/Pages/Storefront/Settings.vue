<template>
  <AuthenticatedLayout title="Storefront">
    <div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
      <div><h2 class="text-2xl font-bold text-nexus-100">Storefront</h2><p class="text-nexus-500 text-sm mt-1">Your public product catalog page</p></div>

      <div class="glass-glow rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-semibold text-nexus-200">Your Store URL</h3>
            <p class="text-xs text-nexus-500 mt-0.5">Share this link with customers on social media</p>
          </div>
          <span class="text-xs text-nexus-500">{{ productCount }} active products</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="flex-1 px-4 py-2.5 rounded-xl bg-nexus-900/50 border border-white/[0.04] font-mono text-sm text-accent-cyan truncate">{{ storefrontUrl }}</div>
          <button @click="copy" :class="['btn-primary text-xs px-4 py-2.5 transition-all', copied ? 'bg-accent-emerald' : '']">{{ copied ? '✓ Copied' : 'Copy' }}</button>
          <a :href="storefrontUrl" target="_blank" class="btn-secondary text-xs px-4 py-2.5">Open ↗</a>
        </div>
      </div>

      <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-nexus-200 mb-2">How it works</h3>
        <div class="space-y-3 text-sm text-nexus-400">
          <div class="flex items-start gap-3"><span class="w-6 h-6 rounded-lg bg-accent-cyan/10 text-accent-cyan text-xs font-bold flex items-center justify-center shrink-0">1</span>Customers visit your storefront link</div>
          <div class="flex items-start gap-3"><span class="w-6 h-6 rounded-lg bg-accent-emerald/10 text-accent-emerald text-xs font-bold flex items-center justify-center shrink-0">2</span>They browse products, see prices and images</div>
          <div class="flex items-start gap-3"><span class="w-6 h-6 rounded-lg bg-accent-violet/10 text-accent-violet text-xs font-bold flex items-center justify-center shrink-0">3</span>Click "Order Now" → opens WhatsApp with pre-filled message</div>
          <div class="flex items-start gap-3"><span class="w-6 h-6 rounded-lg bg-accent-amber/10 text-accent-amber text-xs font-bold flex items-center justify-center shrink-0">4</span>The lead arrives in your Nexus inbox — create the order instantly</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineProps({ storefrontUrl: String, productCount: Number, slug: String });
const copied = ref(false);
function copy() { navigator.clipboard.writeText(document.querySelector('.font-mono')?.textContent); copied.value = true; setTimeout(() => copied.value = false, 2000); }
</script>
