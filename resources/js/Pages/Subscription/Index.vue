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
              <span class="text-3xl font-bold text-nexus-100">€49</span>
              <span class="text-xs text-nexus-500">/mo</span>
            </div>
            <p class="text-xs text-nexus-600 mt-2">Perfect for new businesses.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-400">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>1 Integration Channel</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unified Inbox & Manual Orders</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>CRM Profiles & Basic Tags</li>
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
              <span class="text-3xl font-bold text-gradient">€99</span>
              <span class="text-xs text-nexus-400">/mo</span>
            </div>
            <p class="text-xs text-nexus-400 mt-2">Advanced command center for scaling.</p>
          </div>
          <ul class="space-y-3 mb-8 flex-1 text-sm text-nexus-200 relative">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unlimited Channels & Orders</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Storefront & WA Sync</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Invoices, Coupons & CSAT</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Team Logs & Broadcasts</li>
          </ul>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all relative" :class="currentTier === 'pro' ? 'bg-nexus-500/20 text-nexus-200 cursor-default' : 'bg-gradient-to-r from-nexus-500 to-accent-violet text-white hover:opacity-90 hover:shadow-lg'">
            {{ isOnTrial ? '✓ Currently on Trial' : (currentTier === 'pro' ? 'Current Plan' : 'Select Pro') }}
          </button>
        </div>

        <!-- Ultimate Plan -->
        <div class="glass-glow border border-accent-cyan/25 rounded-3xl p-6 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-accent-cyan/20 shadow-lg shadow-accent-cyan/5">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-accent-cyan to-accent-violet"></div>
          <div class="absolute -top-3 -right-3 w-16 h-16 bg-accent-cyan/15 blur-2xl rounded-full"></div>
          <div class="mb-5 relative">
            <div class="flex items-center gap-2 mb-3">
              <span class="inline-block px-2 py-1 bg-accent-cyan/15 text-accent-cyan text-[10px] font-bold uppercase tracking-wider rounded-lg">Includes AI</span>
            </div>
            <h3 class="text-lg font-bold text-nexus-200">Ultimate</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-nexus-100">Custom</span>
            </div>
            <p class="text-xs text-nexus-600 mt-2">Bespoke architecture for global operations.</p>
          </div>
          <ul class="space-y-3 mb-5 flex-1 text-sm text-nexus-400 relative">
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-emerald shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Everything in Pro</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-cyan shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>✨ Smart Order (AI Auto-fills)</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-cyan shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>Full AI Order Extraction</li>
            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-accent-violet shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014-5.395c-.318.55-.117 1.26.461 1.527l.38.657c.301.522.96.709 1.511.463a20.845 20.845 0 00-4.282-1.44m0 0a18.03 18.03 0 014.59-.59c1.586 0 3.124.205 4.59.59" /></svg>Demand Forecasting & AI Auto-Reply</li>
          </ul>
          <!-- AI callout inside card -->
          <div class="mb-5 rounded-2xl bg-gradient-to-br from-accent-cyan/10 to-accent-violet/10 border border-accent-cyan/20 p-3 relative">
            <div class="flex items-start gap-2">
              <svg class="w-4 h-4 text-accent-cyan shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" /></svg>
              <div>
                <div class="text-[11px] font-bold text-accent-cyan">AI Assistant — Ultimate only</div>
                <div class="text-[10px] text-nexus-400 mt-0.5 leading-relaxed">Auto-replies, order detection, and draft order creation. Your inbox runs itself, 24/7.</div>
              </div>
            </div>
          </div>
          <button class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all" :class="currentTier === 'ultimate' ? 'bg-white/[0.04] text-nexus-400 cursor-default' : 'bg-gradient-to-r from-accent-cyan/80 to-accent-violet text-white hover:opacity-90 hover:shadow-lg'">
            {{ currentTier === 'ultimate' ? 'Current Plan' : 'Contact Sales' }}
          </button>
        </div>
      </div>
      
      <!-- AI Feature Spotlight -->
      <div class="glass rounded-3xl p-6 border border-accent-cyan/15 bg-gradient-to-r from-accent-cyan/5 via-transparent to-accent-violet/5">
        <div class="flex items-start gap-5 flex-wrap sm:flex-nowrap">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-accent-cyan/20 to-accent-violet/20 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" /></svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-base font-bold text-nexus-100">AI Assistant</h3>
              <span class="px-2 py-0.5 rounded-lg bg-accent-cyan/15 text-accent-cyan text-[10px] font-bold uppercase tracking-wider">Ultimate only</span>
            </div>
            <p class="text-sm text-nexus-400 mt-1">Auto-replies, ✨ Smart Order (AI auto-fills orders from chat messages), and draft order creation. Your inbox runs itself, 24/7.</p>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-accent-cyan shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                <div>
                  <div class="text-xs font-semibold text-nexus-200">AI Auto-Replies</div>
                  <div class="text-[11px] text-nexus-500 mt-0.5">Answers customer questions instantly using your business info and product catalogue</div>
                </div>
              </div>
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-accent-violet shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                <div>
                  <div class="text-xs font-semibold text-nexus-200">Order Detection</div>
                  <div class="text-[11px] text-nexus-500 mt-0.5">Extracts customer name, phone, city and product intent from natural conversation</div>
                </div>
              </div>
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-accent-amber shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                <div>
                  <div class="text-xs font-semibold text-nexus-200">Auto-Order Creation</div>
                  <div class="text-[11px] text-nexus-500 mt-0.5">Creates a draft order automatically when all details are collected — ready to review and dispatch</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center text-xs text-nexus-500">
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
