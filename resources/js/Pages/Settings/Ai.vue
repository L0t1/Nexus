<template>
  <AuthenticatedLayout :title="$t('pageTitles.aiAssistant')">
    <div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
      <div>
        <h2 class="text-2xl font-bold text-nexus-100">{{ $t('aiAssistant.title') }}</h2>
        <p class="text-nexus-500 text-sm mt-1">{{ $t('aiAssistant.subtitle') }}</p>
      </div>

      <!-- Upgrade gate (Starter plan) -->
      <div v-if="!hasAiChat" class="glass-glow rounded-2xl p-8 text-center border border-accent-violet/20">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-accent-violet to-accent-cyan flex items-center justify-center text-white text-2xl mx-auto mb-4">🤖</div>
        <h3 class="text-lg font-bold text-nexus-100 mb-2">{{ $t('aiAssistant.proFeature') }}</h3>
        <p class="text-sm text-nexus-400 max-w-md mx-auto mb-5">{{ $t('aiAssistant.proDesc') }}</p>
        <button @click="$inertia.visit('/subscription')" class="btn-primary">{{ $t('aiAssistant.upgradeBtn') }}</button>
      </div>

      <template v-else>
        <!-- ── Toggle & Usage ──────────────────────────────────── -->
        <div class="glass-glow rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
            <div>
              <h3 class="text-sm font-semibold text-nexus-200">{{ $t('aiAssistant.autoReply') }}</h3>
              <p class="text-xs text-nexus-500 mt-0.5">{{ $t('aiAssistant.autoReplyDesc') }}</p>
            </div>
            <!-- Toggle switch -->
            <button @click="toggleAi" :disabled="toggling"
              :class="['relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none', aiEnabled ? 'bg-gradient-to-r from-accent-emerald to-accent-cyan' : 'bg-white/10']">
              <span :class="['pointer-events-none inline-block h-6 w-6 rounded-full bg-white shadow-lg transform transition-transform duration-200 ease-in-out', aiEnabled ? 'translate-x-5' : 'translate-x-0']" />
            </button>
          </div>

          <!-- Usage meter -->
          <div class="px-6 py-5">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-nexus-400 uppercase tracking-wider">{{ $t('aiAssistant.monthlyUsage') }}</span>
              <span class="text-xs text-nexus-300 font-medium">
                <template v-if="usage.limit === null">{{ usage.used }} {{ $t('aiAssistant.sent') }} · <span class="text-accent-cyan">{{ $t('aiAssistant.unlimited') }}</span></template>
                <template v-else>{{ usage.used }} / {{ usage.limit }} {{ $t('aiAssistant.replies') }}</template>
              </span>
            </div>
            <div v-if="usage.limit !== null" class="w-full h-2 rounded-full bg-white/5 overflow-hidden">
              <div class="h-full rounded-full transition-all duration-500"
                :class="usagePercent >= 100 ? 'bg-accent-rose' : usagePercent >= 80 ? 'bg-accent-amber' : 'bg-gradient-to-r from-accent-emerald to-accent-cyan'"
                :style="{ width: Math.min(usagePercent, 100) + '%' }" />
            </div>
            <div v-if="usage.limit !== null" class="flex justify-between mt-1.5">
              <span class="text-[10px] text-nexus-600">{{ $t('aiAssistant.resets') }}</span>
              <span :class="['text-[10px] font-medium', usagePercent >= 100 ? 'text-accent-rose' : usagePercent >= 80 ? 'text-accent-amber' : 'text-nexus-500']">
                {{ usagePercent >= 100 ? $t('aiAssistant.limitReached') : usage.remaining + ' ' + $t('aiAssistant.remaining') }}
              </span>
            </div>
          </div>
        </div>

        <!-- ── Business Info Form ──────────────────────────────── -->
        <div class="glass rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/[0.04]">
            <h3 class="text-sm font-semibold text-nexus-200">{{ $t('aiAssistant.businessKnowledge') }}</h3>
            <p class="text-xs text-nexus-500 mt-0.5">{{ $t('aiAssistant.knowledgeDesc') }}</p>
          </div>
          <form @submit.prevent="saveSettings" class="p-6 space-y-4">
            <!-- Description -->
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('aiAssistant.businessDesc') }}</label>
              <textarea v-model="form.description" rows="3" :placeholder="$t('aiAssistant.descPlaceholder')" class="input-premium py-2 text-sm w-full" />
            </div>

            <!-- Language -->
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('aiAssistant.replyLang') }}</label>
              <select v-model="form.language" class="input-premium cursor-pointer w-full">
                <option value="auto">{{ $t('aiAssistant.langAuto') }}</option>
                <option value="sq">{{ $t('aiAssistant.langSq') }}</option>
                <option value="en">{{ $t('aiAssistant.langEn') }}</option>
              </select>
            </div>

            <!-- Greeting -->
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('aiAssistant.greeting') }}</label>
              <input v-model="form.greeting" type="text" :placeholder="$t('aiAssistant.greetPlaceholder')" class="input-premium w-full" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Return Policy -->
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('aiAssistant.returnPolicy') }}</label>
                <textarea v-model="form.return_policy" rows="3" :placeholder="$t('aiAssistant.returnPlaceholder')" class="input-premium py-2 text-sm w-full" />
              </div>
              <!-- Shipping Info -->
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('aiAssistant.shippingInfo') }}</label>
                <textarea v-model="form.shipping_info" rows="3" :placeholder="$t('aiAssistant.shipPlaceholder')" class="input-premium py-2 text-sm w-full" />
              </div>
            </div>

            <!-- Business Hours -->
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('aiAssistant.hours') }}</label>
              <input v-model="form.business_hours" type="text" :placeholder="$t('aiAssistant.hoursPlaceholder')" class="input-premium w-full" />
            </div>

            <div class="pt-2">
              <button type="submit" :disabled="infoForm.processing" class="btn-primary">{{ $t('aiAssistant.saveInfo') }}</button>
            </div>
          </form>
        </div>

        <!-- ── FAQ Editor ──────────────────────────────────────── -->
        <div class="glass rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
            <div>
              <h3 class="text-sm font-semibold text-nexus-200">{{ $t('aiAssistant.faqs') }}</h3>
              <p class="text-xs text-nexus-500 mt-0.5">{{ $t('aiAssistant.faqsDesc') }}</p>
            </div>
            <button type="button" @click="addFaq" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
              {{ $t('aiAssistant.addFaq') }}
            </button>
          </div>
          <div class="p-6 space-y-3">
            <div v-if="!form.faqs.length" class="text-sm text-nexus-600 text-center py-6">{{ $t('aiAssistant.noFaqs') }}</div>
            <div v-for="(faq, index) in form.faqs" :key="index" class="p-4 rounded-xl bg-white/[0.02] border border-white/[0.05] space-y-2">
              <div class="flex items-start gap-2">
                <div class="flex-1 space-y-2">
                  <input v-model="faq.q" type="text" :placeholder="$t('aiAssistant.qPlaceholder')" class="input-premium text-sm w-full" />
                  <textarea v-model="faq.a" rows="2" :placeholder="$t('aiAssistant.aPlaceholder')" class="input-premium py-2 text-sm w-full" />
                </div>
                <button @click="removeFaq(index)" class="mt-1 p-1.5 rounded-lg text-nexus-600 hover:text-accent-rose hover:bg-accent-rose/10 transition-colors flex-shrink-0">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
              </div>
            </div>
            <div v-if="form.faqs.length" class="pt-2">
              <button type="button" @click="saveFaqs" :disabled="infoForm.processing" class="btn-primary">{{ $t('aiAssistant.saveFaqs') }}</button>
            </div>
          </div>
        </div>

        <!-- ── Products note ───────────────────────────────────── -->
        <div class="glass rounded-2xl p-5 flex items-start gap-4 border border-accent-cyan/10">
          <div class="w-9 h-9 rounded-xl bg-accent-cyan/10 flex items-center justify-center text-accent-cyan flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
          </div>
          <div>
            <div class="text-sm font-semibold text-nexus-200 mb-0.5">{{ $t('aiAssistant.syncTitle') }}</div>
            <p class="text-xs text-nexus-500 leading-relaxed">{{ $t('aiAssistant.syncDesc1') }} <button @click="$inertia.visit('/products')" class="text-accent-cyan hover:underline">{{ $t('aiAssistant.productsPage') }}</button> {{ $t('aiAssistant.syncDesc2') }}</p>
          </div>
        </div>

        <!-- ── Auto-Order note ─────────────────────────────────── -->
        <div class="glass rounded-2xl p-5 flex items-start gap-4 border border-accent-emerald/10">
          <div class="w-9 h-9 rounded-xl bg-accent-emerald/10 flex items-center justify-center text-accent-emerald flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
          <div>
            <div class="text-sm font-semibold text-nexus-200 mb-0.5">{{ $t('aiAssistant.autoOrderTitle') }}</div>
            <p class="text-xs text-nexus-500 leading-relaxed">{{ $t('aiAssistant.autoOrderDesc1') }} <span class="text-nexus-300 font-medium">{{ $t('aiAssistant.draftOrder') }}</span> {{ $t('aiAssistant.autoOrderDesc2') }} <button @click="$inertia.visit('/orders')" class="text-accent-emerald hover:underline">{{ $t('aiAssistant.orders') }}</button> {{ $t('aiAssistant.autoOrderDesc3') }}</p>
          </div>
        </div>
      </template>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const merchant = computed(() => page.props.auth?.merchant);
const hasAiChat = computed(() => merchant.value?.features?.includes('ai_chat'));

const props = defineProps({
  ai_enabled:  { type: Boolean, default: false },
  ai_settings: { type: Object,  default: () => ({}) },
  usage:       { type: Object,  default: () => ({ used: 0, limit: null, remaining: null }) },
});

const aiEnabled = ref(props.ai_enabled);
const toggling  = ref(false);

const usagePercent = computed(() => {
  if (!props.usage.limit) return 0;
  return Math.round((props.usage.used / props.usage.limit) * 100);
});

// ── Info form ──────────────────────────────────────────────────────────────
const form = ref({
  description:    props.ai_settings.description    ?? '',
  language:       props.ai_settings.language       ?? 'auto',
  greeting:       props.ai_settings.greeting       ?? '',
  return_policy:  props.ai_settings.return_policy  ?? '',
  shipping_info:  props.ai_settings.shipping_info  ?? '',
  business_hours: props.ai_settings.business_hours ?? '',
  faqs:           props.ai_settings.faqs           ?? [],
});

const infoForm = useForm({});

function saveSettings() {
  infoForm.transform(() => ({ ...form.value })).put('/settings/ai', {
    preserveScroll: true,
  });
}

function saveFaqs() {
  saveSettings();
}

// ── FAQ helpers ────────────────────────────────────────────────────────────
function addFaq() {
  form.value.faqs.push({ q: '', a: '' });
}

function removeFaq(index) {
  form.value.faqs.splice(index, 1);
}

// ── Toggle ─────────────────────────────────────────────────────────────────
function toggleAi() {
  toggling.value = true;
  router.post('/settings/ai/toggle', {}, {
    preserveScroll: true,
    onSuccess: () => {
      aiEnabled.value = !aiEnabled.value;
      toggling.value  = false;
    },
    onError: () => { toggling.value = false; },
  });
}
</script>
