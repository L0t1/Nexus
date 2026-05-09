<template>
  <AuthenticatedLayout :title="$t('pageTitles.subscription')">
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

      <!-- AI Usage Dashboard (Only if they have AI) -->
      <div v-if="currentTier !== 'starter'" class="glass rounded-3xl p-6 border border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
          <div class="space-y-1">
            <h3 class="text-sm font-bold text-nexus-100 flex items-center gap-2">
              <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              AI Usage Summary
            </h3>
            <p class="text-[10px] text-nexus-500 uppercase tracking-widest font-bold">{{ currentTier }} Plan Quota</p>
          </div>
          
          <div class="flex-1 max-w-md space-y-2">
            <div class="flex justify-between text-xs mb-1">
              <span class="text-nexus-400 font-medium">Monthly Messages</span>
              <span class="text-nexus-200 font-bold">{{ merchant?.ai_monthly_messages_count }} / {{ aiLimit === null ? '∞' : aiLimit }}</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
              <div 
                class="h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-accent-cyan via-accent-violet to-accent-rose"
                :style="{ width: aiUsageProgress + '%' }"
              ></div>
            </div>
            <div class="flex justify-between items-center pt-1">
              <p class="text-[10px] text-nexus-500 italic">Resets on the 1st of every month</p>
              <span v-if="aiLimit && merchant?.ai_monthly_messages_count > (aiLimit * 0.8)" class="text-[10px] text-accent-rose font-bold uppercase animate-pulse">Running Low!</span>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 stagger-children">
        <!-- Starter Plan -->
        <div class="glass rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-nexus-500/5 border border-white/5">
          <div v-if="currentTier === 'starter' && !isOnTrial" class="absolute top-0 inset-x-0 h-1 bg-nexus-600"></div>
          <div class="mb-5">
            <h3 class="text-base font-bold text-nexus-200">Starter</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-nexus-100">€39</span>
              <span class="text-[10px] text-nexus-500 uppercase font-semibold">/mo</span>
            </div>
            <p class="text-[10px] text-nexus-600 mt-2 uppercase tracking-wide font-bold">New Businesses</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-400">
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-emerald shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>1 Channel</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-emerald shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>100 Orders / mo</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unified Inbox</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Basic Analytics</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider" :class="currentTier === 'starter' && !isOnTrial ? 'bg-white/[0.04] text-nexus-500 cursor-default border border-white/5' : 'bg-white/[0.04] text-nexus-300 hover:bg-white/[0.08] border border-white/10'">
            {{ currentTier === 'starter' && !isOnTrial ? 'Active' : 'Select' }}
          </button>
        </div>

        <!-- Growth Plan -->
        <div class="glass-glow border border-nexus-500/30 rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 shadow-xl shadow-nexus-500/10">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-nexus-500 to-accent-emerald"></div>
          <div class="mb-5">
            <div class="flex items-center gap-2 mb-2.5">
              <span class="inline-block px-1.5 py-0.5 bg-accent-emerald/15 text-accent-emerald text-[9px] font-bold uppercase tracking-wider rounded-md">Most Popular</span>
            </div>
            <h3 class="text-base font-bold text-white">Growth</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-gradient-emerald">€89</span>
              <span class="text-[10px] text-nexus-400 uppercase font-semibold">/mo</span>
            </div>
            <p class="text-[10px] text-nexus-400 mt-2 uppercase tracking-wide font-bold">Scaling Shops</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-200">
            <li class="text-[9px] text-accent-emerald font-bold mb-1 opacity-80 uppercase tracking-tighter">Everything in Starter, plus:</li>
            <li class="flex items-center gap-2 font-bold text-accent-emerald"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>3 Connected Channels</li>
            <li class="flex items-center gap-2 font-bold text-accent-emerald"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>500 Orders / mo</li>
            <li class="flex items-start gap-2 py-1">
              <svg class="w-3.5 h-3.5 text-accent-cyan shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
              <div>
                <span class="font-bold text-accent-cyan italic underline decoration-accent-cyan/30">300 AI Extractions</span>
                <p class="text-[9px] text-nexus-400 mt-0.5 leading-tight">AI auto-fills customer name, phone & city from messages to your order drafts.</p>
              </div>
            </li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Invoices & Coupons</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Advanced Analytics</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider shadow-lg" :class="currentTier === 'growth' ? 'bg-accent-emerald/20 text-accent-emerald cursor-default border border-accent-emerald/20' : 'bg-gradient-to-r from-nexus-500 to-accent-emerald text-white hover:opacity-90'">
            {{ currentTier === 'growth' ? 'Active' : 'Select' }}
          </button>
        </div>

        <!-- Scale Plan -->
        <div class="glass rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-xl border border-white/10">
          <div v-if="currentTier === 'scale'" class="absolute top-0 inset-x-0 h-1 bg-accent-violet"></div>
          <div class="mb-5">
            <h3 class="text-base font-bold text-nexus-200">Scale</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-nexus-100">€179</span>
              <span class="text-[10px] text-nexus-500 uppercase font-semibold">/mo</span>
            </div>
            <p class="text-[10px] text-nexus-600 mt-2 uppercase tracking-wide font-bold">Automation Hub</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-400">
            <li class="text-[9px] text-accent-violet font-bold mb-1 opacity-80 uppercase tracking-tighter">Everything in Growth, plus:</li>
            <li class="flex items-center gap-2 font-bold text-accent-violet"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unlimited Channels</li>
            <li class="flex items-start gap-2 py-1">
              <svg class="w-4 h-4 text-accent-violet shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
              <div>
                <span class="font-bold text-accent-violet italic underline decoration-accent-violet/30">1,500 AI Messages</span>
                <p class="text-[9px] text-nexus-400 mt-0.5 leading-tight">A full AI Agent that chats with customers, answers product FAQs & takes orders.</p>
              </div>
            </li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-accent-violet shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>WhatsApp Sync & Store</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>CSAT & Broadcasts</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider" :class="currentTier === 'scale' ? 'bg-accent-violet/20 text-accent-violet cursor-default border border-accent-violet/20' : 'bg-white/[0.04] text-nexus-200 hover:bg-white/[0.08] border border-white/10'">
            {{ currentTier === 'scale' ? 'Active' : 'Select' }}
          </button>
        </div>

        <!-- Ultimate Plan -->
        <div class="glass-glow border border-accent-cyan/25 rounded-3xl p-5 flex flex-col relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-accent-cyan/20">
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-accent-cyan to-accent-violet"></div>
          <div class="mb-5">
            <h3 class="text-base font-bold text-nexus-200">Ultimate</h3>
            <div class="mt-2 flex items-baseline gap-1">
              <span class="text-2xl font-bold text-nexus-100">Custom</span>
            </div>
            <p class="text-[10px] text-nexus-600 mt-2 uppercase tracking-wide font-bold">Enterprise</p>
          </div>
          <ul class="space-y-2 mb-6 flex-1 text-[11px] text-nexus-400">
            <li class="text-[9px] text-accent-cyan font-bold mb-1 opacity-80 uppercase tracking-tighter">Everything in Scale, plus:</li>
            <li class="flex items-center gap-2 font-bold text-accent-cyan"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Unlimited AI Messages</li>
            <li class="flex items-center gap-2 font-bold text-accent-cyan"><svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Priority Support</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Custom Branding</li>
            <li class="flex items-center gap-2"><svg class="w-3 h-3 text-nexus-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Custom Logic Dev</li>
          </ul>
          <button class="w-full py-2 rounded-xl font-bold text-[11px] transition-all uppercase tracking-wider" :class="currentTier === 'ultimate' ? 'bg-white/[0.04] text-nexus-500 cursor-default border border-white/5' : 'bg-gradient-to-r from-accent-cyan/80 to-accent-violet text-white hover:opacity-90'">
            {{ currentTier === 'ultimate' ? 'Active' : 'Contact Sales' }}
          </button>
        </div>
      </div>
      <!-- One-Time Add-ons -->
      <div class="space-y-4">
        <h3 class="text-sm font-bold text-nexus-200 px-2 uppercase tracking-widest">One-Time Add-ons</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="glass rounded-3xl p-5 border border-accent-cyan/10 bg-gradient-to-br from-accent-cyan/5 to-transparent flex items-center justify-between gap-6 transition-all hover:border-accent-cyan/20">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-accent-cyan/10 flex items-center justify-center border border-accent-cyan/20">
                <svg class="w-6 h-6 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              </div>
              <div>
                <div class="text-base font-bold text-nexus-100">500 AI Messages</div>
                <div class="text-[11px] text-nexus-500 leading-relaxed">Non-expiring. Automatically used after your monthly quota ends.</div>
              </div>
            </div>
            <div class="text-right space-y-2">
              <div class="text-lg font-bold text-nexus-100">€19</div>
              <button class="px-4 py-1.5 bg-accent-cyan/10 hover:bg-accent-cyan/20 text-accent-cyan rounded-xl text-[10px] font-bold transition-all border border-accent-cyan/20 uppercase tracking-wider">
                Buy Pack
              </button>
            </div>
          </div>

          <div v-if="merchant?.ai_extra_credits > 0" class="glass rounded-3xl p-5 border border-accent-violet/10 bg-gradient-to-br from-accent-violet/5 to-transparent flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-accent-violet/10 flex items-center justify-center border border-accent-violet/20">
                <svg class="w-6 h-6 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <div>
                <div class="text-base font-bold text-nexus-100">Extra Credits</div>
                <div class="text-[11px] text-nexus-500">Your non-expiring backup balance.</div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-accent-violet">{{ merchant?.ai_extra_credits }}</div>
              <div class="text-[9px] text-nexus-500 uppercase font-bold tracking-widest">Available</div>
            </div>
          </div>
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
              <h3 class="text-base font-bold text-nexus-100">AI Automation Suite</h3>
              <span class="px-2 py-0.5 rounded-lg bg-accent-cyan/15 text-accent-cyan text-[10px] font-bold uppercase tracking-wider border border-accent-cyan/20">Growth & Scale</span>
            </div>
            <p class="text-sm text-nexus-400 mt-1 leading-relaxed italic">Turn your inbox into a self-managing machine. Our AI works while you sleep.</p>
            
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
              <!-- Feature 1 -->
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <div class="w-8 h-8 rounded-xl bg-accent-cyan/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                  </div>
                  <span class="text-[9px] font-bold text-accent-violet px-1.5 py-0.5 bg-accent-violet/10 rounded uppercase">Scale Plan</span>
                </div>
                <div class="text-xs font-bold text-nexus-100">AI Auto-Replies</div>
                <p class="text-[10px] text-nexus-500 leading-relaxed">Instantly answer product questions and handle FAQs automatically via WhatsApp, FB & IG.</p>
              </div>

              <!-- Feature 2 -->
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <div class="w-8 h-8 rounded-xl bg-accent-cyan/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                  </div>
                  <span class="text-[9px] font-bold text-accent-emerald px-1.5 py-0.5 bg-accent-emerald/10 rounded uppercase">Growth Plan</span>
                </div>
                <div class="text-xs font-bold text-nexus-100">Smart Extraction</div>
                <p class="text-[10px] text-nexus-500 leading-relaxed">Automatically detects name, phone, and city in messages to speed up order creation.</p>
              </div>

              <!-- Feature 3 -->
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <div class="w-8 h-8 rounded-xl bg-accent-cyan/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375" /></svg>
                  </div>
                  <span class="text-[9px] font-bold text-accent-violet px-1.5 py-0.5 bg-accent-violet/10 rounded uppercase">Scale Plan</span>
                </div>
                <div class="text-xs font-bold text-nexus-100">Auto-Order Creator</div>
                <p class="text-[10px] text-nexus-500 leading-relaxed">AI creates a full draft order automatically when all customer details are collected.</p>
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

// AI Usage Logic
const aiLimit = computed(() => {
  const limits = {
    'starter': 0,
    'growth': 300,
    'scale': 1500,
    'ultimate': null
  };
  return limits[currentTier.value] ?? 0;
});

const aiUsageProgress = computed(() => {
  if (aiLimit.value === null) return 0; // Unlimited
  if (aiLimit.value === 0) return 0;
  return Math.min(100, (merchant.value?.ai_monthly_messages_count / aiLimit.value) * 100);
});
</script>
