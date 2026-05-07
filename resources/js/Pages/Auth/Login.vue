<template>
  <div class="min-h-screen mesh-gradient flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient orbs -->
    <div class="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-nexus-600/8 blur-[100px] animate-float"></div>
    <div class="absolute bottom-1/4 -right-32 w-80 h-80 rounded-full bg-accent-cyan/6 blur-[100px] animate-float" style="animation-delay: 1.5s"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-nexus-500/3 blur-[120px]"></div>

    <div class="w-full max-w-[420px] animate-fade-in-scale">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-nexus-500 via-nexus-600 to-accent-cyan flex items-center justify-center mx-auto mb-4 shadow-2xl shadow-nexus-600/30 animate-glow">
          <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
        <h1 class="text-3xl font-bold text-[#4cd7f6] tracking-tight">Nexus CRM</h1>
        <p class="text-nexus-500 text-sm mt-2">Sign in to your dashboard</p>
      </div>

      <!-- Card -->
      <div class="glass-strong gradient-border rounded-2xl p-8">
        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Email</label>
            <input v-model="form.email" type="email" required autofocus placeholder="admin@nexus.test" class="input-premium" />
            <p v-if="form.errors.email" class="text-accent-rose text-xs mt-1.5">{{ form.errors.email }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Password</label>
            <input v-model="form.password" type="password" required placeholder="••••••••" class="input-premium" />
          </div>
          <button type="submit" :disabled="form.processing" class="btn-primary w-full flex items-center justify-center gap-2">
            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span>{{ form.processing ? 'Signing in...' : 'Sign in' }}</span>
          </button>
        </form>

        <div class="mt-6 pt-5 border-t border-white/[0.04] text-center">
          <p class="text-xs text-nexus-600">Don't have an account? <a href="/register" @click.prevent="$inertia.visit('/register')" class="text-nexus-400 hover:text-nexus-300 font-medium transition-colors">Create one</a></p>
        </div>
      </div>

      <p class="text-center text-[10px] text-nexus-700 mt-6">Powered by NEXUS · Chat-to-Cart Platform</p>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '' });

function submit() {
  form.post('/login', { onFinish: () => form.reset('password') });
}
</script>
