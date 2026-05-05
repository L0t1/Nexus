<template>
  <div class="min-h-screen mesh-gradient flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-nexus-600/8 blur-[100px] animate-float"></div>
    <div class="absolute bottom-1/4 -right-32 w-80 h-80 rounded-full bg-accent-cyan/6 blur-[100px] animate-float" style="animation-delay: 1.5s"></div>

    <div class="w-full max-w-[420px] animate-fade-in-scale">
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-nexus-500 via-nexus-600 to-accent-cyan flex items-center justify-center mx-auto mb-4 shadow-2xl shadow-nexus-600/30 animate-glow">
          <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
        <h1 class="text-3xl font-bold text-gradient tracking-tight">NEXUS</h1>
        <p class="text-nexus-500 text-sm mt-2">Create your merchant account</p>
      </div>

      <div class="glass-strong gradient-border rounded-2xl p-8">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Full Name</label>
            <input v-model="form.name" type="text" required autofocus placeholder="Your name" class="input-premium" />
            <p v-if="form.errors.name" class="text-accent-rose text-xs mt-1.5">{{ form.errors.name }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Business Name</label>
            <input v-model="form.business_name" type="text" required placeholder="Your store name" class="input-premium" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Email</label>
            <input v-model="form.email" type="email" required placeholder="you@business.com" class="input-premium" />
            <p v-if="form.errors.email" class="text-accent-rose text-xs mt-1.5">{{ form.errors.email }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Password</label>
            <input v-model="form.password" type="password" required placeholder="••••••••" class="input-premium" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" required placeholder="••••••••" class="input-premium" />
          </div>
          <button type="submit" :disabled="form.processing" class="btn-primary w-full py-3 mt-2">
            {{ form.processing ? 'Creating account...' : 'Create Account' }}
          </button>
        </form>
        <div class="mt-6 pt-5 border-t border-white/[0.04] text-center">
          <p class="text-xs text-nexus-600">Already have an account? <a href="/login" @click.prevent="$inertia.visit('/login')" class="text-nexus-400 hover:text-nexus-300 font-medium transition-colors">Sign in</a></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
const form = useForm({ name: '', business_name: '', email: '', password: '', password_confirmation: '' });
function submit() { form.post('/register'); }
</script>
