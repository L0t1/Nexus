<template>
  <AuthenticatedLayout title="Marketing Broadcasts">
    <div class="space-y-6 animate-fade-in max-w-4xl mx-auto">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">Marketing Broadcasts</h2>
          <p class="text-nexus-500 text-sm mt-1">Send promotional messages to targeted customer segments</p>
        </div>
      </div>

      <div class="glass-glow rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-accent-cyan/10 blur-3xl rounded-full translate-x-1/3 -translate-y-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent-violet/10 blur-3xl rounded-full -translate-x-1/3 translate-y-1/3 pointer-events-none"></div>

        <form @submit.prevent="submit" class="relative z-10 space-y-8">
          
          <!-- Message Section -->
          <div>
            <label class="block text-sm font-semibold text-nexus-200 mb-2">Message Content <span class="text-accent-rose">*</span></label>
            <p class="text-xs text-nexus-500 mb-3">Write the promotional text you want to send. Keep it engaging!</p>
            <textarea v-model="form.message" rows="5" placeholder="Hi there! We are running a special discount this weekend..." 
              class="w-full px-4 py-3 rounded-2xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-accent-cyan/50 focus:bg-white/[0.05] transition-all resize-none shadow-inner"></textarea>
            <div v-if="form.errors.message" class="text-accent-rose text-[10px] mt-1">{{ form.errors.message }}</div>
          </div>

          <!-- Targeting Section -->
          <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/[0.04]">
            <h3 class="text-sm font-semibold text-nexus-200 mb-5 flex items-center gap-2">
              <svg class="w-4 h-4 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
              Target Audience
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- City -->
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">City</label>
                <select v-model="form.city" class="w-full px-4 py-3 rounded-xl bg-nexus-900 border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-accent-violet/50 transition-colors cursor-pointer appearance-none">
                  <option value="">All Cities</option>
                  <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                </select>
              </div>

              <!-- Gender -->
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Gender</label>
                <select v-model="form.gender" class="w-full px-4 py-3 rounded-xl bg-nexus-900 border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-accent-violet/50 transition-colors cursor-pointer appearance-none">
                  <option value="">All Genders</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>

              <!-- Platform -->
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Platform</label>
                <select v-model="form.platform" class="w-full px-4 py-3 rounded-xl bg-nexus-900 border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-accent-violet/50 transition-colors cursor-pointer appearance-none">
                  <option value="">All Platforms</option>
                  <option value="whatsapp">WhatsApp</option>
                  <option value="instagram">Instagram</option>
                  <option value="facebook">Facebook</option>
                </select>
              </div>

              <!-- Minimum Orders -->
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-2 uppercase tracking-wider">Loyalty (Min. Orders)</label>
                <input v-model.number="form.min_orders" type="number" min="0" placeholder="0 = Everyone" 
                  class="w-full px-4 py-3 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-accent-violet/50 focus:bg-white/[0.05] transition-colors" />
                <p class="text-[10px] text-nexus-600 mt-1">E.g., enter 2 to only send to returning customers.</p>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-4 flex items-center justify-between border-t border-white/[0.06]">
            <p class="text-xs text-nexus-500">The broadcast will be queued and sent gradually.</p>
            <button type="submit" :disabled="form.processing || !form.message.trim()" 
              class="px-8 py-3 bg-gradient-to-r from-accent-violet to-accent-cyan text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-accent-cyan/20 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:hover:scale-100 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2">
              <svg v-if="!form.processing" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
              <svg v-else class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              {{ form.processing ? 'Queuing Broadcast...' : 'Send Broadcast' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  cities: { type: Array, default: () => [] }
});

const form = useForm({
  message: '',
  city: '',
  gender: '',
  platform: '',
  min_orders: 0,
});

function submit() {
  form.post('/broadcasts', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('message');
    }
  });
}
</script>
