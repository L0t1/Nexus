<template>
  <div class="min-h-screen mesh-gradient flex items-center justify-center p-6">
    <div class="max-w-xl w-full animate-fade-in">
      <!-- Logo + Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-nexus-500 via-nexus-600 to-accent-cyan glow-strong mb-5 animate-glow">
          <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gradient mb-2">Welcome to NEXUS</h1>
        <p class="text-nexus-400 text-sm">Let's set up your workspace in a few quick steps</p>
      </div>

      <!-- Progress -->
      <div class="flex items-center justify-center gap-2 mb-8">
        <div v-for="s in totalSteps" :key="s" :class="['h-1.5 rounded-full transition-all duration-500', s <= step ? 'w-10 bg-gradient-to-r from-nexus-500 to-accent-cyan' : 'w-6 bg-white/10']"></div>
      </div>

      <!-- Step 1: Business Details -->
      <Transition mode="out-in" enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-x-8" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 -translate-x-8">
        <div v-if="step === 1" key="step1" class="glass-glow rounded-2xl p-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-nexus-500/20 to-accent-cyan/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-nexus-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-nexus-100">Your Business</h2>
              <p class="text-xs text-nexus-500">Tell us a bit about your business</p>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Business Name <span class="text-accent-rose">*</span></label>
              <input v-model="form.business_name" type="text" class="input-premium" placeholder="e.g. My Store" />
              <p v-if="form.errors.business_name" class="text-xs text-accent-rose mt-1">{{ form.errors.business_name }}</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Phone Number</label>
              <input v-model="form.phone" type="tel" class="input-premium" placeholder="+212 6XX XXX XXX" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Country</label>
                <input v-model="form.country" type="text" class="input-premium" placeholder="Morocco" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">City</label>
                <input v-model="form.city" type="text" class="input-premium" placeholder="Casablanca" />
              </div>
            </div>
          </div>

          <button @click="nextStep" :disabled="!form.business_name" class="w-full mt-8 py-3 rounded-xl bg-gradient-to-r from-nexus-500 to-accent-cyan text-white font-semibold text-sm transition-all hover:shadow-lg hover:shadow-nexus-500/25 disabled:opacity-30 disabled:cursor-not-allowed">
            Continue →
          </button>
        </div>

        <!-- Step 2: What You'll Get -->
        <div v-else-if="step === 2" key="step2" class="glass-glow rounded-2xl p-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-emerald/20 to-accent-cyan/10 flex items-center justify-center">
              <svg class="w-5 h-5 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-nexus-100">You're All Set!</h2>
              <p class="text-xs text-nexus-500">Here's what you can do next</p>
            </div>
          </div>

          <div class="space-y-4">
            <div v-for="(item, i) in nextSteps" :key="i" class="flex items-start gap-4 p-3.5 rounded-xl bg-white/[0.02] border border-white/[0.05]">
              <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" :class="item.bg">
                <span class="text-lg">{{ item.icon }}</span>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-nexus-200">{{ item.title }}</h3>
                <p class="text-xs text-nexus-500 mt-0.5 leading-relaxed">{{ item.desc }}</p>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-8">
            <button @click="step = 1" class="px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-medium text-nexus-400 transition-all">← Back</button>
            <button @click="submit" :disabled="form.processing" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-accent-emerald to-accent-cyan text-white font-semibold text-sm transition-all hover:shadow-lg hover:shadow-accent-emerald/25 disabled:opacity-50">
              {{ form.processing ? 'Setting up...' : '🚀 Launch My Dashboard' }}
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const step = ref(1);
const totalSteps = 3;

const merchant = page.props.auth?.merchant;

const form = useForm({
  business_name: merchant?.business_name || '',
  phone: merchant?.phone || '',
  country: merchant?.country || '',
  city: merchant?.city || '',
});

const nextSteps = [
  { icon: '💬', title: 'Connect a Channel', desc: 'Go to Settings → Channels to link Facebook, Instagram, or WhatsApp.', bg: 'bg-accent-cyan/10' },
  { icon: '📦', title: 'Add Your Products', desc: 'Head to Products to build your catalog with prices and stock levels.', bg: 'bg-accent-violet/10' },
  { icon: '🤖', title: 'Let AI Help You', desc: 'Incoming messages will be parsed automatically to extract order details.', bg: 'bg-accent-emerald/10' },
];

function nextStep() {
  if (step.value < totalSteps) step.value++;
}

function submit() {
  form.post('/onboarding/complete');
}
</script>
