<template>
  <AuthenticatedLayout :title="$t('settings.title')">
    <div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
      <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('settings.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('settings.subtitle') }}</p></div>

      <!-- Business Profile -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.businessProfile') }}</h3></div>
        <form @submit.prevent="saveProfile" class="p-6 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.businessName') }}</label><input v-model="profileForm.business_name" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.phone') }}</label><input v-model="profileForm.phone" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.country') }}</label><input v-model="profileForm.country" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.city') }}</label><input v-model="profileForm.city" class="input-premium" /></div>
          </div>
          <div class="pt-2"><button type="submit" :disabled="profileForm.processing" class="btn-primary">{{ $t('settings.saveProfile') }}</button></div>
        </form>
      </div>

      <!-- Account -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.account') }}</h3></div>
        <form @submit.prevent="saveAccount" class="p-6 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.name') }}</label><input v-model="accountForm.name" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.email') }}</label><input v-model="accountForm.email" type="email" class="input-premium" /></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.newPassword') }}</label><input v-model="accountForm.password" type="password" :placeholder="$t('settings.passwordPlaceholder')" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('settings.confirmPassword') }}</label><input v-model="accountForm.password_confirmation" type="password" class="input-premium" /></div>
          </div>
          <div class="pt-2"><button type="submit" :disabled="accountForm.processing" class="btn-primary">{{ $t('settings.updateAccount') }}</button></div>
        </form>
      </div>

      <!-- Team Management -->
      <div class="glass rounded-2xl overflow-hidden relative">
        <div v-if="!hasMultiAgent" class="absolute inset-0 z-10 glass-strong backdrop-blur-md flex flex-col items-center justify-center p-6 text-center border border-white/10 shadow-2xl">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-accent-cyan to-accent-violet flex items-center justify-center text-white mb-3"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg></div>
          <h3 class="text-lg font-bold text-nexus-100 mb-1">{{ $t('settings.teamLocked') }}</h3>
          <p class="text-xs text-nexus-400 max-w-sm mb-4">{{ $t('settings.teamLockedDesc') }}</p>
          <button @click="$inertia.visit('/subscription')" class="btn-primary py-2 px-4 text-xs">{{ $t('settings.unlockUpgrade') }}</button>
        </div>

        <div :class="['transition-all duration-500', !hasMultiAgent ? 'opacity-20 pointer-events-none blur-[2px] select-none' : '']">
          <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
            <div><h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.teamManagement') }}</h3><p class="text-xs text-nexus-500 mt-0.5">{{ $t('settings.inviteAgents') }}</p></div>
          </div>
          <div class="p-6">
            <form @submit.prevent="inviteAgent" class="flex gap-3 mb-6">
              <input v-model="agentForm.name" :placeholder="$t('settings.namePlaceholder')" class="input-premium flex-1" required />
              <input v-model="agentForm.email" type="email" :placeholder="$t('settings.emailPlaceholder')" class="input-premium flex-1" required />
              <input v-model="agentForm.password" type="password" :placeholder="$t('settings.passwordFieldPlaceholder')" class="input-premium flex-1" required minlength="8" />
              <button type="submit" :disabled="agentForm.processing" class="btn-primary whitespace-nowrap">{{ $t('settings.inviteAgent') }}</button>
            </form>

            <div class="space-y-3">
              <div v-for="agent in agents" :key="agent.id" class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04]">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-xs font-bold text-nexus-300">{{ agent.name.charAt(0).toUpperCase() }}</div>
                  <div>
                    <div class="text-sm font-medium text-nexus-200">{{ agent.name }}</div>
                    <div class="text-xs text-nexus-500">{{ agent.email }}</div>
                  </div>
                </div>
                <button @click="removeAgent(agent)" class="text-xs text-accent-rose hover:bg-accent-rose/10 px-3 py-1.5 rounded-lg transition-colors">{{ $t('settings.removeAgent') }}</button>
              </div>
              <div v-if="!agents?.length" class="text-sm text-nexus-600 text-center py-4">{{ $t('settings.noAgents') }}</div>
            </div>
          </div>
        </div>
      </div>

      <ConfirmModal 
        :show="!!agentToDelete" 
        :title="$t('settings.removeAgent')" 
        :message="$t('settings.removeAgentConfirm') + ' ' + agentToDelete?.name + '? ' + $t('settings.removeAgentWarn')" 
        @close="agentToDelete = null" 
        @confirm="executeRemoveAgent" 
      />

      <!-- Channels -->
      <div class="glass gradient-border rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
          <div>
            <h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.channelIntegration') }}</h3>
            <p class="text-xs text-nexus-500 mt-1">{{ $t('settings.channelDesc') }}</p>
          </div>
          <div v-if="!hasAllChannels" class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md" :class="channels.length > 0 ? 'bg-accent-amber/10 text-accent-amber' : 'bg-white/5 text-nexus-500'">
            {{ channels.length }}/1 {{ $t('settings.channel') }}
          </div>
          <div v-else class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md bg-accent-emerald/10 text-accent-emerald">
            {{ $t('settings.unlimitedChannels') }}
          </div>
        </div>
        
        <div class="p-6 space-y-6">
          <!-- Active Channels List -->
          <div v-if="channels.length > 0" class="space-y-3">
            <h4 class="text-[10px] font-bold text-nexus-500 uppercase tracking-widest px-1">Active Connections</h4>
            <div v-for="channel in channels" :key="channel.id" class="flex items-center justify-between p-4 rounded-xl bg-white/[0.02] border border-white/[0.05] hover:bg-white/[0.04] transition-all group">
              <div class="flex items-center gap-3">
                <PlatformIcon :platform="channel.platform" class="w-5 h-5" />
                <div>
                  <div class="text-sm font-semibold text-nexus-200">{{ channel.label }}</div>
                  <div class="text-[10px] text-nexus-500 mt-0.5 uppercase tracking-tighter">{{ channel.platform }} · ID: {{ channel.external_id }}</div>
                </div>
              </div>
              <button @click="disconnectChannel(channel)" class="text-xs text-nexus-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all px-2 py-1 rounded-lg">
                {{ $t('common.disconnect') }}
              </button>
            </div>
          </div>

          <div v-else class="text-center py-6 border-2 border-dashed border-white/[0.05] rounded-2xl">
            <p class="text-xs text-nexus-500 italic">No channels connected yet. Connect your first page below.</p>
          </div>

          <!-- Connection Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Meta (FB/IG) -->
            <div class="relative">
              <div v-if="limitReached" class="absolute inset-0 z-10 glass-strong backdrop-blur-md rounded-xl flex flex-col items-center justify-center p-4 text-center border border-white/10">
                <button @click="$inertia.visit('/subscription')" class="text-[10px] text-accent-cyan font-bold hover:underline">UPGRADE TO ADD MORE</button>
              </div>
              <a href="/auth/meta" :class="['flex flex-col items-center justify-center p-5 rounded-2xl border border-white/[0.08] bg-white/[0.03] hover:bg-white/[0.06] transition-all group', limitReached ? 'opacity-30 grayscale' : '']">
                <div class="flex -space-x-1 mb-3">
                  <PlatformIcon platform="facebook" class="w-6 h-6 group-hover:scale-110 transition-transform" />
                  <PlatformIcon platform="instagram" class="w-6 h-6 group-hover:scale-110 transition-transform" />
                </div>
                <div class="text-xs font-bold text-nexus-200">Facebook / Instagram</div>
                <div class="text-[10px] text-nexus-500 mt-1">Connect your Pages</div>
              </a>
            </div>

            <!-- WhatsApp -->
            <div class="relative">
              <div v-if="limitReached" class="absolute inset-0 z-10 glass-strong backdrop-blur-md rounded-xl flex flex-col items-center justify-center p-4 text-center border border-white/10">
                <button @click="$inertia.visit('/subscription')" class="text-[10px] text-accent-violet font-bold hover:underline">UPGRADE TO ADD MORE</button>
              </div>
              <button @click="launchWhatsAppSignup" :disabled="waConnecting" :class="['w-full flex flex-col items-center justify-center p-5 rounded-2xl border border-white/[0.08] bg-white/[0.03] hover:bg-white/[0.06] transition-all group', limitReached ? 'opacity-30 grayscale' : '']">
                <PlatformIcon platform="whatsapp" class="w-6 h-6 mb-3 group-hover:scale-110 transition-transform" />
                <div class="text-xs font-bold text-nexus-200">WhatsApp Business</div>
                <div class="text-[10px] text-nexus-500 mt-1">{{ waConnecting ? 'Connecting...' : 'Connect your Number' }}</div>
              </button>
            </div>
          </div>
          
          <div v-if="waError" class="text-center text-xs text-red-400 mt-2">{{ waError }}</div>
        </div>
      </div>

      <!-- Appearance -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.appearance') }}</h3></div>
        <div class="p-6">
          <p class="text-xs text-nexus-500 mb-3">{{ $t('settings.appearanceDesc') }}</p>
          <div class="flex gap-3">
            <button @click="setTheme('dark')" :class="['flex-1 p-4 rounded-xl border transition-all text-center cursor-pointer', currentTheme === 'dark' ? 'border-nexus-500 bg-nexus-500/10' : 'border-white/[0.06] hover:border-white/[0.12]']">
              <div class="text-2xl mb-1">🌙</div>
              <div class="text-sm font-medium" :class="currentTheme === 'dark' ? 'text-nexus-200' : 'text-nexus-500'">{{ $t('theme.dark') }}</div>
            </button>
            <button @click="setTheme('light')" :class="['flex-1 p-4 rounded-xl border transition-all text-center cursor-pointer', currentTheme === 'light' ? 'border-nexus-500 bg-nexus-500/10' : 'border-white/[0.06] hover:border-white/[0.12]']">
              <div class="text-2xl mb-1">☀️</div>
              <div class="text-sm font-medium" :class="currentTheme === 'light' ? 'text-nexus-200' : 'text-nexus-500'">{{ $t('theme.light') }}</div>
            </button>
          </div>
        </div>
      </div>

      <!-- Language -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.language') }}</h3></div>
        <div class="p-6">
          <p class="text-xs text-nexus-500 mb-3">{{ $t('settings.languageDesc') }}</p>
          <div class="flex gap-3">
            <button v-for="loc in availableLocales" :key="loc.code" @click="setLocale(loc.code)" :class="['flex-1 p-4 rounded-xl border transition-all text-center cursor-pointer', locale === loc.code ? 'border-nexus-500 bg-nexus-500/10' : 'border-white/[0.06] hover:border-white/[0.12]']">
              <div class="text-2xl mb-1">{{ loc.code === 'en' ? '🇬🇧' : '🇦🇱' }}</div>
              <div class="text-sm font-medium" :class="locale === loc.code ? 'text-nexus-200' : 'text-nexus-500'">{{ loc.name }}</div>
            </button>
          </div>
        </div>
      </div>

      <!-- Subscription -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">{{ $t('settings.subscription') }}</h3></div>
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div><div class="text-xs text-nexus-500">{{ $t('settings.currentPlan') }}</div><div class="text-lg font-bold text-gradient capitalize mt-0.5">{{ merchant?.subscription_tier || 'Starter' }}</div></div>
            <button @click="$inertia.visit('/subscription')" class="btn-secondary text-xs">{{ $t('common.upgrade') }} →</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { useTheme } from '@/composables/useTheme.js';
import { useI18n } from '@/i18n/index.js';

const { theme: currentTheme, setTheme } = useTheme();
const { locale, setLocale, getAvailableLocales } = useI18n();
const availableLocales = getAvailableLocales();

const page = usePage();
const auth = computed(() => page.props.auth);
const merchant = computed(() => page.props.merchant);
const channels = computed(() => merchant.value?.channels || []);
const agents = computed(() => page.props.agents || []);
const hasMultiAgent = computed(() => merchant.value?.features?.includes('priority_inbox'));

const waConnecting = ref(false);
const waError = ref('');

// Channel gating: Starter = 1 channel, Pro/Ultimate = all
const hasAllChannels = computed(() => merchant.value?.features?.includes('all_channels'));
const limitReached = computed(() => !hasAllChannels.value && channels.value.length >= 1);

const agentToDelete = ref(null);

const profileForm = useForm({ business_name: merchant.value?.business_name || '', phone: merchant.value?.phone || '', country: merchant.value?.country || '', city: merchant.value?.city || '' });
const accountForm = useForm({ name: auth.value?.user?.name || '', email: auth.value?.user?.email || '', password: '', password_confirmation: '' });
const agentForm = useForm({ name: '', email: '', password: '' });

function saveProfile() { profileForm.put('/settings/profile', { preserveScroll: true }); }
function saveAccount() { accountForm.put('/settings/account', { preserveScroll: true }); }

function inviteAgent() {
  agentForm.post('/settings/agents', {
    preserveScroll: true,
    onSuccess: () => agentForm.reset(),
  });
}

function removeAgent(agent) {
  agentToDelete.value = agent;
}

function executeRemoveAgent() {
  if (agentToDelete.value) {
    router.delete(`/settings/agents/${agentToDelete.value.id}`, { 
      preserveScroll: true,
      onSuccess: () => agentToDelete.value = null
    });
  }
}

function disconnectChannel(channel) {
  const url = channel.platform === 'whatsapp' ? '/auth/whatsapp' : '/auth/meta';
  router.delete(url, {
    data: { channel_id: channel.id },
    preserveScroll: true
  });
}

// ─── WhatsApp Embedded Signup ─────────────────────────────────────────────────

onMounted(() => {
  // Load Facebook JS SDK
  if (!document.getElementById('facebook-jssdk')) {
    const script = document.createElement('script');
    script.id = 'facebook-jssdk';
    script.src = 'https://connect.facebook.net/en_US/sdk.js';
    script.async = true;
    script.defer = true;
    document.body.appendChild(script);
  }

  window.fbAsyncInit = function () {
    FB.init({
      appId: page.props.metaAppId,
      autoLogAppEvents: true,
      xfbml: true,
      version: 'v19.0',
    });
  };

  // Listen for the Embedded Signup message from the Meta popup
  window.addEventListener('message', handleWhatsAppMessage);
});

function launchWhatsAppSignup() {
  waError.value = '';
  waConnecting.value = true;

  FB.login(
    (response) => {
      if (response.authResponse?.code) {
        sendWhatsAppCode(response.authResponse.code);
      } else {
        waConnecting.value = false;
        waError.value = 'Authorization was cancelled.';
      }
    },
    {
      config_id: page.props.metaWhatsappConfigId,
      response_type: 'code',
      override_default_response_type: true,
      extras: { setup: {} },
    }
  );
}

function handleWhatsAppMessage(event) {
  if (event.origin !== 'https://www.facebook.com' && event.origin !== 'https://web.facebook.com') return;
  try {
    const data = JSON.parse(event.data);
    if (data.type === 'WA_EMBEDDED_SIGNUP' && data.event === 'FINISH') {
      if (data.data?.phone_number_id) {
        sendWhatsAppCode(null, data.data.phone_number_id, data.data.waba_id);
      }
    }
  } catch {}
}

function sendWhatsAppCode(code, phoneNumberId = null, wabaId = null) {
  router.post('/auth/whatsapp/callback',
    { code, phone_number_id: phoneNumberId, waba_id: wabaId },
    {
      preserveScroll: true,
      onSuccess: () => { waConnecting.value = false; },
      onError: (errors) => {
        waConnecting.value = false;
        waError.value = errors.error || 'Failed to connect WhatsApp.';
      },
    }
  );
}
</script>
