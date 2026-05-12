<template>
  <Head :title="`${merchant.business_name} — Shop`" />
  <div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 text-white">
    <!-- Header -->
    <header class="border-b border-white/5 backdrop-blur-xl bg-white/[0.02]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-emerald-500/20 flex items-center justify-center text-sm font-bold text-cyan-300 shrink-0">{{ merchant.business_name?.[0] }}</div>
          <div class="min-w-0">
            <h1 class="text-lg font-bold tracking-tight truncate">{{ merchant.business_name }}</h1>
            <p class="text-xs text-gray-400">{{ merchant.city }}, {{ merchant.country }}</p>
          </div>
        </div>
        <a :href="whatsappLink('')" target="_blank" class="px-3 sm:px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white text-xs sm:text-sm font-medium flex items-center gap-2 transition-all shrink-0">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.75.75 0 00.917.918l4.458-1.495A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.4 0-4.637-.78-6.44-2.09l-.127-.1-3.534 1.185 1.185-3.534-.1-.127A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
          <span class="hidden sm:inline">Chat with us</span>
        </a>
      </div>
    </header>

    <!-- Products Grid -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
      <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold">Our Products</h2>
        <p class="text-sm text-gray-400 mt-1">{{ products.length }} items available</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="p in products" :key="p.id" class="group rounded-2xl border border-white/[0.06] bg-white/[0.02] overflow-hidden hover:border-white/10 hover:bg-white/[0.04] transition-all duration-300">
          <div class="aspect-square bg-gradient-to-br from-gray-800 to-gray-900 relative overflow-hidden">
            <img v-if="p.image_url" :src="p.image_url" :alt="p.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            <div v-else class="w-full h-full flex items-center justify-center text-4xl text-gray-700">📦</div>
            <div v-if="p.stock <= 5" class="absolute top-3 right-3 px-2 py-0.5 rounded-full bg-red-500/90 text-white text-[10px] font-bold uppercase">Low Stock</div>
          </div>
          <div class="p-5">
            <h3 class="text-base font-semibold text-white group-hover:text-cyan-300 transition-colors">{{ p.name }}</h3>
            <p v-if="p.description" class="text-xs text-gray-400 mt-1 line-clamp-2">{{ p.description }}</p>
            <div class="flex items-center justify-between mt-4">
              <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-emerald-400">{{ currency }}{{ Number(p.price).toFixed(2) }}</span>
              <a :href="whatsappLink(p.name)" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-500/15 text-emerald-400 text-xs font-semibold hover:bg-emerald-500 hover:text-white transition-all">
                Order Now →
              </a>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!products.length" class="text-center py-20">
        <p class="text-gray-500">No products available at this time.</p>
      </div>
    </main>

    <footer class="border-t border-white/5 py-6 text-center text-xs text-gray-500">
      Powered by <span class="text-gray-400 font-medium">Nexus CRM</span>
    </footer>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({ merchant: Object, products: Array, slug: String });
const currencyMap = { EUR: '€', USD: '$', GBP: '£', MAD: 'MAD ', TND: 'TND ', DZD: 'DZD ' };
const currency = computed(() => currencyMap[props.merchant.default_currency] || props.merchant.default_currency + ' ');
function whatsappLink(productName) {
  const phone = (props.merchant.phone || '').replace(/[^\d+]/g, '');
  const msg = productName ? `Hi! I'm interested in "${productName}" from your shop.` : `Hi! I'd like to browse your products.`;
  return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
}
</script>
