<template>
  <div class="min-h-screen bg-[#050508] bg-[radial-gradient(circle_at_50%_0%,_#1e1b4b_0%,_#050508_50%)] flex items-center justify-center p-4 selection:bg-accent-violet/30">
    <div class="w-full max-w-md animate-fade-in">
      <div v-if="survey.status === 'completed' || $page.props.flash?.success" class="glass-strong rounded-3xl p-10 text-center border border-white/10 shadow-2xl relative overflow-hidden">
        <!-- Glow effect -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-accent-emerald/10 blur-3xl rounded-full"></div>
        
        <div class="w-20 h-20 rounded-2xl bg-accent-emerald/10 border border-accent-emerald/20 flex items-center justify-center mx-auto mb-6 relative z-10">
          <svg class="w-10 h-10 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        </div>
        <h2 class="text-2xl font-bold text-nexus-100 mb-3 relative z-10">{{ $t('csatFeedback.thankYou') }}</h2>
        <p class="text-nexus-400 text-sm leading-relaxed relative z-10">{{ $t('csatFeedback.thankYouMessage') }}</p>
      </div>

      <div v-else class="glass-glow rounded-3xl p-8 border border-white/10 shadow-2xl">
        <div class="text-center mb-10">
          <div class="inline-block px-3 py-1 rounded-full bg-accent-violet/10 border border-accent-violet/20 text-[10px] font-bold text-accent-violet uppercase tracking-widest mb-4">Feedback Survey</div>
          <h2 class="text-2xl font-bold text-nexus-100">{{ $t('csatFeedback.howWasExperience') }}</h2>
          <p class="text-xs text-nexus-500 mt-2 font-medium opacity-60">{{ survey.merchant?.business_name }} — Order {{ survey.order?.order_number }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
          <div class="space-y-4">
            <div class="flex justify-center gap-2">
              <button v-for="s in 5" :key="s" type="button" 
                @click="form.rating = s" 
                @mouseenter="hoverRating = s"
                @mouseleave="hoverRating = 0"
                :class="['w-14 h-14 rounded-2xl text-2xl transition-all duration-300 flex items-center justify-center border', 
                  (hoverRating || form.rating) >= s 
                    ? 'bg-accent-amber/20 border-accent-amber/40 text-accent-amber shadow-[0_0_20px_rgba(245,158,11,0.2)] scale-110' 
                    : 'bg-white/5 border-white/5 text-nexus-600 hover:bg-white/10 hover:border-white/10']"
              >
                <svg class="w-8 h-8" :fill="(hoverRating || form.rating) >= s ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
              </button>
            </div>
            <div class="text-center text-xs font-bold uppercase tracking-widest transition-all duration-300" :class="form.rating ? 'text-accent-amber opacity-100' : 'text-nexus-600 opacity-40'">
              {{ getRatingLabel(hoverRating || form.rating) }}
            </div>
          </div>

          <div class="space-y-4">
            <textarea v-model="form.feedback" rows="4" 
              class="w-full px-5 py-4 rounded-2xl bg-white/[0.03] border border-white/10 text-sm text-nexus-200 focus:outline-none focus:ring-2 focus:ring-accent-violet/30 focus:border-accent-violet/50 resize-none transition-all placeholder:text-nexus-700" 
              :placeholder="$t('csatFeedback.placeholder')"></textarea>
            
            <button type="submit" :disabled="!form.rating || processing" 
              class="w-full py-4 rounded-2xl bg-gradient-to-r from-accent-violet to-accent-indigo text-white text-sm font-bold hover:shadow-[0_0_25px_rgba(139,92,246,0.3)] hover:scale-[1.02] active:scale-95 disabled:opacity-20 disabled:scale-100 disabled:hover:shadow-none transition-all duration-300 flex items-center justify-center gap-2">
              <span v-if="processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              {{ processing ? $t('csatFeedback.submitting') : $t('csatFeedback.submit') }}
            </button>
          </div>
        </form>
      </div>
      
      <p class="text-center mt-8 text-[10px] text-nexus-700 font-mono tracking-widest uppercase">Powered by NEXUS CRM</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/i18n/index.js';

const props = defineProps({ survey: Object });
const { t } = useI18n();
const processing = ref(false);
const hoverRating = ref(0);
const form = reactive({ rating: 0, feedback: '' });

const ratingLabels = { 
  1: 'veryPoor', 
  2: 'poor', 
  3: 'average', 
  4: 'good', 
  5: 'excellent' 
};

function getRatingLabel(rating) {
  if (!rating) return t('common.rateExperience') || 'Select a rating';
  return t(`csatFeedback.${ratingLabels[rating]}`);
}

function submit() { 
  processing.value = true;
  router.post(`/feedback/${props.survey.token}`, form, {
    onFinish: () => processing.value = false
  }); 
}
</script>
