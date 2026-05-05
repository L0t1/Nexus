<template>
  <div class="min-h-screen mesh-gradient flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient orbs -->
    <div class="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-nexus-600/8 blur-[100px] animate-float"></div>
    <div class="absolute bottom-1/4 -right-32 w-80 h-80 rounded-full bg-accent-cyan/6 blur-[100px] animate-float" style="animation-delay: 1.5s"></div>

    <div class="w-full max-w-[420px] animate-fade-in-scale">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-nexus-500 via-nexus-600 to-accent-cyan flex items-center justify-center mx-auto mb-4 shadow-2xl shadow-nexus-600/30 animate-glow">
          <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gradient tracking-tight">Verify Email</h1>
        <p class="text-nexus-500 text-sm mt-2">One last step before accessing your dashboard</p>
      </div>

      <!-- Card -->
      <div class="glass-strong gradient-border rounded-2xl p-8 space-y-5">
        <div v-if="status === 'verification-link-sent'" class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm text-center">
          A new verification link has been sent to your email address.
        </div>

        <p class="text-nexus-400 text-sm text-center leading-relaxed">
          Thanks for registering! Before getting started, please verify your email address by clicking on the link we just emailed to you.
        </p>

        <p class="text-nexus-600 text-xs text-center">
          Didn't receive the email? Check your spam folder, or request a new one below.
        </p>

        <form @submit.prevent="resend">
          <button
            type="submit"
            :disabled="form.processing"
            class="btn-primary w-full flex items-center justify-center gap-2 py-3"
          >
            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>{{ form.processing ? 'Sending...' : 'Resend Verification Email' }}</span>
          </button>
        </form>

        <div class="pt-2 border-t border-white/[0.04] text-center">
          <button
            @click="logout"
            class="text-xs text-nexus-600 hover:text-nexus-500 transition-colors"
          >
            Log out
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';

defineProps({ status: String });

const form = useForm({});

function resend() {
  form.post('/email/verification-notification');
}

function logout() {
  router.post('/logout');
}
</script>
