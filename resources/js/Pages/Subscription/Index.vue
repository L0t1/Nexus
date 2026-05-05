<template>
  <AuthenticatedLayout title="Subscription">
    <div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
      <div class="text-center space-y-3">
        <h2 class="text-3xl font-bold text-nexus-100">Choose Your Plan</h2>
        <p class="text-nexus-400 text-sm max-w-lg mx-auto">Scale your CRM as your business grows. Change your plan at any time.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 stagger-children">
        <!-- Starter Plan -->
        <div class="glass rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-nexus-500/10">
          <div v-if="currentTier === 'starter'" class="absolute top-0 inset-x-0 h-1 bg-nexus-500"></div>
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
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all" :class="currentTier === 'starter' ? 'bg-white/[0.04] text-nexus-400 cursor-default' : 'bg-white/[0.04] text-nexus-200 hover:bg-white/[0.08]'">
            {{ currentTier === 'starter' ? 'Current Plan' : 'Select Starter' }}
          </button>
        </div>

        <!-- Growth Plan -->
        <div class="glass-glow border border-nexus-500/30 rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 transform md:-translate-y-4 hover:shadow-2xl hover:shadow-accent-violet/20 shadow-xl shadow-accent-violet/10">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-nexus-500 to-accent-violet"></div>
          <div class="absolute -top-3 -right-3 w-16 h-16 bg-accent-violet/20 blur-2xl rounded-full"></div>
          <div class="mb-5 relative">
            <span class="inline-block px-2 py-1 bg-accent-violet/15 text-accent-violet text-[10px] font-bold uppercase tracking-wider rounded-lg mb-3">Most Popular</span>
            <h3 class="text-lg font-bold text-white">Growth</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-gradient">€79</span>
              <span class="text-xs text-nexus-400">/mo</span>
            </div>
            <p class="text-xs text-nexus-400 mt-2">Everything you need to scale.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-200 relative">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unlimited orders</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>All Channel Integrations</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Advanced AI Extraction</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Priority Support</li>
          </ul>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all relative" :class="currentTier === 'growth' ? 'bg-nexus-500/20 text-nexus-200 cursor-default' : 'bg-gradient-to-r from-nexus-500 to-accent-violet text-white hover:opacity-90 hover:shadow-lg'">
            {{ currentTier === 'growth' ? 'Current Plan' : 'Select Growth' }}
          </button>
        </div>

        <!-- Enterprise Plan -->
        <div class="glass rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-accent-cyan/10">
          <div v-if="currentTier === 'enterprise'" class="absolute top-0 inset-x-0 h-1 bg-accent-cyan"></div>
          <div class="mb-5">
            <h3 class="text-lg font-bold text-nexus-200">Enterprise</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-nexus-100">€199</span>
              <span class="text-xs text-nexus-500">/mo</span>
            </div>
            <p class="text-xs text-nexus-600 mt-2">Custom solutions for big teams.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-400">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Custom Webhooks</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Dedicated Account Manager</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Custom Integrations</li>
          </ul>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all" :class="currentTier === 'enterprise' ? 'bg-white/[0.04] text-nexus-400 cursor-default' : 'bg-white/[0.04] text-nexus-200 hover:bg-white/[0.08]'">
            {{ currentTier === 'enterprise' ? 'Current Plan' : 'Contact Sales' }}
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
const merchant = computed(() => page.props.merchant);
const currentTier = computed(() => merchant.value?.subscription_tier || 'starter');
</script>
