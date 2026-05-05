<template>
  <AuthenticatedLayout title="Subscription">
    <div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
      <!-- Trial Banner -->
      <div v-if="isOnTrial" class="glass-glow rounded-2xl p-5 border border-accent-cyan/20 bg-gradient-to-r from-accent-cyan/5 to-accent-violet/5">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-cyan/20 to-accent-violet/20 flex items-center justify-center">
              <svg class="w-5 h-5 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="text-sm font-bold text-nexus-100">🎉 Pro Trial Active</div>
              <div class="text-xs text-nexus-400 mt-0.5">
                <span v-if="trialDaysLeft > 0">{{ trialDaysLeft }} days remaining</span>
                <span v-else class="text-accent-rose font-semibold">Trial expired — you'll be moved to Starter</span>
                · Enjoy unlimited orders, all channels & advanced analytics for free.
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div class="text-[10px] text-nexus-500 hidden sm:block">Ends {{ trialEndDate }}</div>
            <div class="h-1.5 w-24 bg-white/10 rounded-full overflow-hidden hidden sm:block">
              <div class="h-full rounded-full bg-gradient-to-r from-accent-cyan to-accent-violet transition-all" :style="{ width: trialProgress + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center space-y-3">
        <h2 class="text-3xl font-bold text-nexus-100">Choose Your Plan</h2>
        <p class="text-nexus-400 text-sm max-w-lg mx-auto">Scale your CRM as your business grows. Change your plan at any time.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 stagger-children">
        <!-- Starter Plan -->
        <div class="glass rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-nexus-500/10">
          <div v-if="currentTier === 'starter' && !isOnTrial" class="absolute top-0 inset-x-0 h-1 bg-nexus-500"></div>
          <div class="mb-5">
            <h3 class="text-lg font-bold text-nexus-200">Starter</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-nexus-100">€29</span>
              <span class="text-xs text-nexus-500">/mo</span>
            </div>
            <p class="text-xs text-nexus-600 mt-2">Perfect for new businesses.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-400">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Up to 500 orders/mo</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>1 Channel Integration</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Basic Analytics</li>
          </ul>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all" :class="currentTier === 'starter' && !isOnTrial ? 'bg-white/[0.04] text-nexus-400 cursor-default' : 'bg-white/[0.04] text-nexus-200 hover:bg-white/[0.08]'">
            {{ currentTier === 'starter' && !isOnTrial ? 'Current Plan' : 'Select Starter' }}
          </button>
        </div>

        <!-- Pro Plan -->
        <div class="glass-glow border border-nexus-500/30 rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 transform md:-translate-y-4 hover:shadow-2xl hover:shadow-accent-violet/20 shadow-xl shadow-accent-violet/10">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-nexus-500 to-accent-violet"></div>
          <div class="absolute -top-3 -right-3 w-16 h-16 bg-accent-violet/20 blur-2xl rounded-full"></div>
          <div class="mb-5 relative">
            <div class="flex items-center gap-2 mb-3">
              <span class="inline-block px-2 py-1 bg-accent-violet/15 text-accent-violet text-[10px] font-bold uppercase tracking-wider rounded-lg">Most Popular</span>
              <span v-if="isOnTrial" class="inline-block px-2 py-1 bg-accent-cyan/15 text-accent-cyan text-[10px] font-bold uppercase tracking-wider rounded-lg animate-pulse-soft">Free Trial</span>
            </div>
            <h3 class="text-lg font-bold text-white">Pro</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-gradient">€49</span>
              <span class="text-xs text-nexus-400">/mo</span>
            </div>
            <p class="text-xs text-nexus-400 mt-2">Everything you need to scale.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-200 relative">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unlimited orders</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>All Channel Integrations</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Product & Stock Management</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Advanced Analytics</li>
          </ul>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all relative" :class="currentTier === 'pro' ? 'bg-nexus-500/20 text-nexus-200 cursor-default' : 'bg-gradient-to-r from-nexus-500 to-accent-violet text-white hover:opacity-90 hover:shadow-lg'">
            {{ isOnTrial ? '✓ Currently on Trial' : (currentTier === 'pro' ? 'Current Plan' : 'Select Pro') }}
          </button>
        </div>

        <!-- Ultimate Plan -->
        <div class="glass rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-accent-cyan/10">
          <div v-if="currentTier === 'ultimate'" class="absolute top-0 inset-x-0 h-1 bg-accent-cyan"></div>
          <div class="mb-5">
            <h3 class="text-lg font-bold text-nexus-200">Ultimate</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-nexus-100">€99</span>
              <span class="text-xs text-nexus-500">/mo</span>
            </div>
            <p class="text-xs text-nexus-600 mt-2">Maximum power and AI assistance.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-400">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>All Pro Features</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-cyan shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>AI Chat Extraction</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-amber shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>Priority Inbox Sorting</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-violet shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014-5.395c-.318.55-.117 1.26.461 1.527l.38.657c.301.522.96.709 1.511.463a20.845 20.845 0 00-4.282-1.44m0 0a18.03 18.03 0 014.59-.59c1.586 0 3.124.205 4.59.59" /></svg>Marketing Broadcasts</li>
          </ul>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all" :class="currentTier === 'ultimate' ? 'bg-white/[0.04] text-nexus-400 cursor-default' : 'bg-white/[0.04] text-nexus-200 hover:bg-white/[0.08]'">
            {{ currentTier === 'ultimate' ? 'Current Plan' : 'Select Ultimate' }}
          </button>
        </div>
      </div>
      
      <div class="mt-8 text-center text-xs text-nexus-500">
        Prices exclude VAT where applicable. All plans are billed monthly.
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const merchant = computed(() => page.props.auth?.merchant);
const currentTier = computed(() => merchant.value?.subscription_tier || 'starter');

const expiresAt = computed(() => merchant.value?.subscription_expires_at ? new Date(merchant.value.subscription_expires_at) : null);
const isOnTrial = computed(() => expiresAt.value && currentTier.value === 'pro');
const trialDaysLeft = computed(() => {
  if (!expiresAt.value) return 0;
  const diff = Math.ceil((expiresAt.value - new Date()) / (1000 * 60 * 60 * 24));
  return Math.max(0, diff);
});
const trialProgress = computed(() => {
  if (!expiresAt.value) return 0;
  return Math.max(0, Math.min(100, (trialDaysLeft.value / 30) * 100));
});
const trialEndDate = computed(() => {
  if (!expiresAt.value) return '';
  return expiresAt.value.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
});
</script>
