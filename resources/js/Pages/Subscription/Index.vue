<template>
  <AuthenticatedLayout :title="$t('pageTitles.subscription')">
    <div class="max-w-5xl mx-auto space-y-8 animate-fade-in">
      <!-- Trial Banner -->
      <div v-if="isOnTrial" class="glass-glow rounded-2xl p-5 border border-accent-cyan/20 bg-gradient-to-r from-accent-cyan/5 to-accent-violet/5">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-cyan/20 to-accent-violet/20 flex items-center justify-center">
              <svg class="w-5 h-5 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="text-sm font-bold text-nexus-100">🎉 {{ $t('subscription.trialActive') }}</div>
              <div class="text-xs text-nexus-400 mt-0.5">
                <span v-if="trialDaysLeft > 0">{{ trialDaysLeft }} {{ $t('subscription.daysLeft') }}</span>
                <span v-else class="text-accent-rose font-semibold">{{ $t('subscription.trialExpired') }}</span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div class="text-[10px] text-nexus-500 hidden sm:block">{{ $t('subscription.ends') }} {{ trialEndDate }}</div>
            <div class="h-1.5 w-24 bg-white/10 rounded-full overflow-hidden hidden sm:block">
              <div class="h-full rounded-full bg-gradient-to-r from-accent-cyan to-accent-violet transition-all" :style="{ width: trialProgress + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center space-y-3">
        <h2 class="text-3xl font-bold text-nexus-100">{{ $t('subscription.chooseTitle') }}</h2>
        <p class="text-nexus-400 text-sm max-w-lg mx-auto">{{ $t('subscription.chooseSubtitle') }}</p>
      </div>

      <!-- AI Usage Dashboard -->
      <div v-if="currentTier !== 'starter'" class="glass rounded-3xl p-6 border border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
          <div class="space-y-1">
            <h3 class="text-sm font-bold text-nexus-100 flex items-center gap-2">
              <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              {{ $t('subscription.aiUsage') }}
            </h3>
            <p class="text-[10px] text-nexus-500 uppercase tracking-widest font-bold">{{ currentTier }} {{ $t('subscription.planQuota') }}</p>
          </div>
          <div class="flex-1 max-w-md space-y-2">
            <div class="flex justify-between text-xs mb-1">
              <span class="text-nexus-400 font-medium">{{ $t('subscription.monthlyMessages') }}</span>
              <span class="text-nexus-200 font-bold">{{ merchant?.ai_monthly_messages_count }} / {{ aiLimit === null ? '∞' : aiLimit }}</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
              <div class="h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-accent-cyan via-accent-violet to-accent-rose" :style="{ width: aiUsageProgress + '%' }"></div>
            </div>
            <div class="flex justify-between items-center pt-1">
              <p class="text-[10px] text-nexus-500 italic">{{ $t('subscription.resetsMonthly') }}</p>
              <span v-if="aiLimit && merchant?.ai_monthly_messages_count > (aiLimit * 0.8)" class="text-[10px] text-accent-rose font-bold uppercase animate-pulse">{{ $t('subscription.runningLow') }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Plan Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 stagger-children">

        <!-- STARTER -->
        <div class="glass rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-nexus-500/5 border border-white/5">
          <div v-if="currentTier === 'starter' && !isOnTrial" class="absolute top-0 inset-x-0 h-1 bg-nexus-600"></div>
          <div class="mb-5">
            <h3 class="text-base font-bold text-nexus-200">Starter</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-nexus-100">€39</span>
              <span class="text-[10px] text-nexus-500 uppercase font-semibold">/{{ $t('subscription.mo') }}</span>
            </div>
            <p class="text-[10px] text-nexus-600 mt-2 uppercase tracking-wide font-bold">{{ $t('subscription.starterTag') }}</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-400">
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-emerald shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.s1') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-emerald shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.s2') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.s3') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.s4') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.s5') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.s6') }}</li>
            <li class="flex items-center gap-2 text-nexus-600 line-through opacity-50"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>{{ $t('subscription.noAI') }}</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider" :class="currentTier === 'starter' && !isOnTrial ? 'bg-white/[0.04] text-nexus-500 cursor-default border border-white/5' : 'bg-white/[0.04] text-nexus-300 hover:bg-white/[0.08] border border-white/10'">
            {{ currentTier === 'starter' && !isOnTrial ? $t('subscription.active') : $t('subscription.select') }}
          </button>
        </div>

        <!-- GROWTH (highlighted) -->
        <div class="glass-glow border border-nexus-500/30 rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 shadow-xl shadow-nexus-500/10">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-nexus-500 to-accent-emerald"></div>
          <div class="mb-5">
            <div class="flex items-center gap-2 mb-2.5">
              <span class="inline-block px-1.5 py-0.5 bg-accent-emerald/15 text-accent-emerald text-[9px] font-bold uppercase tracking-wider rounded-md">{{ $t('subscription.popular') }}</span>
            </div>
            <h3 class="text-base font-bold text-white">Growth</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-gradient-emerald">€89</span>
              <span class="text-[10px] text-nexus-400 uppercase font-semibold">/{{ $t('subscription.mo') }}</span>
            </div>
            <p class="text-[10px] text-nexus-400 mt-2 uppercase tracking-wide font-bold">{{ $t('subscription.growthTag') }}</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-200">
            <li class="text-[9px] text-accent-emerald font-bold mb-1 opacity-80 uppercase tracking-tighter">{{ $t('subscription.everythingInStarter') }}</li>
            <li class="flex items-center gap-2 font-bold text-accent-emerald"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.g1') }}</li>
            <li class="flex items-center gap-2 font-bold text-accent-emerald"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.g2') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-cyan shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg><span class="font-bold text-accent-cyan">{{ $t('subscription.g3') }}</span></li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.g4') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.g5') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.g6') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.g7') }}</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider shadow-lg" :class="currentTier === 'growth' ? 'bg-accent-emerald/20 text-accent-emerald cursor-default border border-accent-emerald/20' : 'bg-gradient-to-r from-nexus-500 to-accent-emerald text-white hover:opacity-90'">
            {{ currentTier === 'growth' ? $t('subscription.active') : $t('subscription.select') }}
          </button>
        </div>

        <!-- SCALE -->
        <div class="glass rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-xl border border-white/10">
          <div v-if="currentTier === 'scale'" class="absolute top-0 inset-x-0 h-1 bg-accent-violet"></div>
          <div class="mb-5">
            <h3 class="text-base font-bold text-nexus-200">Scale</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-nexus-100">€179</span>
              <span class="text-[10px] text-nexus-500 uppercase font-semibold">/{{ $t('subscription.mo') }}</span>
            </div>
            <p class="text-[10px] text-nexus-600 mt-2 uppercase tracking-wide font-bold">{{ $t('subscription.scaleTag') }}</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-400">
            <li class="text-[9px] text-accent-violet font-bold mb-1 opacity-80 uppercase tracking-tighter">{{ $t('subscription.everythingInGrowth') }}</li>
            <li class="flex items-center gap-2 font-bold text-accent-violet"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.sc1') }}</li>
            <li class="flex items-center gap-2 font-bold text-accent-violet"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.sc2') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-violet shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.sc3') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.sc4') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.sc5') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.sc6') }}</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider" :class="currentTier === 'scale' ? 'bg-accent-violet/20 text-accent-violet cursor-default border border-accent-violet/20' : 'bg-white/[0.04] text-nexus-200 hover:bg-white/[0.08] border border-white/10'">
            {{ currentTier === 'scale' ? $t('subscription.active') : $t('subscription.select') }}
          </button>
        </div>

        <!-- ULTIMATE -->
        <div class="glass-glow border border-accent-cyan/25 rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-accent-cyan/20">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-accent-cyan to-accent-violet"></div>
          <div class="mb-5">
            <h3 class="text-base font-bold text-nexus-200">Ultimate</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-nexus-100">{{ $t('subscription.custom') }}</span>
            </div>
            <p class="text-[10px] text-nexus-600 mt-2 uppercase tracking-wide font-bold">{{ $t('subscription.ultimateTag') }}</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-400">
            <li class="text-[9px] text-accent-cyan font-bold mb-1 opacity-80 uppercase tracking-tighter">{{ $t('subscription.everythingInScale') }}</li>
            <li class="flex items-center gap-2 font-bold text-accent-cyan"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.u1') }}</li>
            <li class="flex items-center gap-2 font-bold text-accent-cyan"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.u2') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.u3') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.u4') }}</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t('subscription.u5') }}</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider" :class="currentTier === 'ultimate' ? 'bg-white/[0.04] text-nexus-500 cursor-default border border-white/5' : 'bg-gradient-to-r from-accent-cyan/80 to-accent-violet text-white hover:opacity-90'">
            {{ currentTier === 'ultimate' ? $t('subscription.active') : $t('subscription.contactSales') }}
          </button>
        </div>
      </div>

      <!-- One-Time Add-ons -->
      <div class="space-y-4">
        <h3 class="text-sm font-bold text-nexus-200 px-2 uppercase tracking-widest">{{ $t('subscription.addons') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="glass rounded-3xl p-5 border border-accent-cyan/10 bg-gradient-to-br from-accent-cyan/5 to-transparent flex items-center justify-between gap-6 transition-all hover:border-accent-cyan/20">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-accent-cyan/10 flex items-center justify-center border border-accent-cyan/20">
                <svg class="w-6 h-6 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              </div>
              <div>
                <div class="text-base font-bold text-nexus-100">{{ $t('subscription.aiPack') }}</div>
                <div class="text-[11px] text-nexus-500 leading-relaxed">{{ $t('subscription.aiPackDesc') }}</div>
              </div>
            </div>
            <div class="text-right space-y-2 shrink-0">
              <div class="text-lg font-bold text-nexus-100">€19</div>
              <button class="px-4 py-1.5 bg-accent-cyan/10 hover:bg-accent-cyan/20 text-accent-cyan rounded-xl text-[10px] font-bold transition-all border border-accent-cyan/20 uppercase tracking-wider">
                {{ $t('subscription.buyPack') }}
              </button>
            </div>
          </div>
          <div v-if="merchant?.ai_extra_credits > 0" class="glass rounded-3xl p-5 border border-accent-violet/10 bg-gradient-to-br from-accent-violet/5 to-transparent flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-accent-violet/10 flex items-center justify-center border border-accent-violet/20">
                <svg class="w-6 h-6 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <div>
                <div class="text-base font-bold text-nexus-100">{{ $t('subscription.extraCredits') }}</div>
                <div class="text-[11px] text-nexus-500">{{ $t('subscription.extraCreditsDesc') }}</div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-accent-violet">{{ merchant?.ai_extra_credits }}</div>
              <div class="text-[9px] text-nexus-500 uppercase font-bold tracking-widest">{{ $t('subscription.available') }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center text-xs text-nexus-500">
        {{ $t('subscription.vat') }}
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

const aiLimit = computed(() => {
  const limits = { 'starter': 0, 'growth': 300, 'scale': 1500, 'ultimate': null };
  return limits[currentTier.value] ?? 0;
});

const aiUsageProgress = computed(() => {
  if (aiLimit.value === null) return 0;
  if (aiLimit.value === 0) return 0;
  return Math.min(100, (merchant.value?.ai_monthly_messages_count / aiLimit.value) * 100);
});
</script>
