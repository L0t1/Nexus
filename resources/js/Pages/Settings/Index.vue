<template>
  <AuthenticatedLayout title="Settings">
    <div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
      <div><h2 class="text-2xl font-bold text-nexus-100">Settings</h2><p class="text-nexus-500 text-sm mt-1">Manage your account & integrations</p></div>

      <!-- Business Profile -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Business Profile</h3></div>
        <form @submit.prevent="saveProfile" class="p-6 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Business Name</label><input v-model="profileForm.business_name" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Phone</label><input v-model="profileForm.phone" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Country</label><input v-model="profileForm.country" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">City</label><input v-model="profileForm.city" class="input-premium" /></div>
          </div>
          <div class="pt-2"><button type="submit" :disabled="profileForm.processing" class="btn-primary">Save Profile</button></div>
        </form>
      </div>

      <!-- Account -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Account</h3></div>
        <form @submit.prevent="saveAccount" class="p-6 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Name</label><input v-model="accountForm.name" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Email</label><input v-model="accountForm.email" type="email" class="input-premium" /></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">New Password</label><input v-model="accountForm.password" type="password" placeholder="Leave blank to keep" class="input-premium" /></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Confirm</label><input v-model="accountForm.password_confirmation" type="password" class="input-premium" /></div>
          </div>
          <div class="pt-2"><button type="submit" :disabled="accountForm.processing" class="btn-primary">Update Account</button></div>
        </form>
      </div>

      <!-- Channels -->
      <div class="glass gradient-border rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]">
          <h3 class="text-sm font-semibold text-nexus-200">Channel Integration</h3>
          <p class="text-xs text-nexus-500 mt-1">Connect your messaging channels so customer messages appear in your inbox.</p>
        </div>
        <div class="p-6 space-y-5">

          <!-- Facebook + Instagram (OAuth) -->
          <div class="flex items-center justify-between p-4 rounded-xl bg-white/[0.02] border border-white/[0.05]">
            <div class="flex items-center gap-3">
              <div class="flex -space-x-1">
                <PlatformIcon platform="facebook" class="w-5 h-5" />
                <PlatformIcon platform="instagram" class="w-5 h-5" />
              </div>
              <div>
                <div class="text-sm font-semibold text-nexus-200">Facebook & Instagram</div>
                <div v-if="facebookConnected" class="text-xs text-emerald-400 mt-0.5">✓ Connected — Page ID {{ creds.facebook_page_id }}<span v-if="creds.instagram_page_id"> · Instagram linked</span></div>
                <div v-else class="text-xs text-nexus-500 mt-0.5">Not connected</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button v-if="facebookConnected" @click="disconnectForm.delete('/auth/meta')" :disabled="disconnectForm.processing" class="text-xs text-red-400 hover:text-red-300 transition-colors">Disconnect</button>
              <a href="/auth/meta" class="btn-primary text-xs py-1.5 px-3 inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                {{ facebookConnected ? 'Reconnect' : 'Connect with Facebook' }}
              </a>
            </div>
          </div>

          <!-- WhatsApp (Embedded Signup) -->
          <div class="rounded-xl bg-white/[0.02] border border-white/[0.05] overflow-hidden">
            <div class="flex items-center justify-between p-4">
              <div class="flex items-center gap-3">
                <PlatformIcon platform="whatsapp" class="w-5 h-5" />
                <div>
                  <div class="text-sm font-semibold text-nexus-200">WhatsApp</div>
                  <div v-if="whatsappConnected" class="text-xs text-emerald-400 mt-0.5">✓ Connected — Phone ID {{ creds.whatsapp_phone_id }}</div>
                  <div v-else class="text-xs text-nexus-500 mt-0.5">Not connected</div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button v-if="whatsappConnected" @click="disconnectWaForm.delete('/auth/whatsapp')" :disabled="disconnectWaForm.processing" class="text-xs text-red-400 hover:text-red-300 transition-colors">Disconnect</button>
                <button @click="launchWhatsAppSignup" :disabled="waConnecting" class="btn-primary text-xs py-1.5 px-3 inline-flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                  {{ whatsappConnected ? 'Reconnect' : 'Connect WhatsApp' }}
                </button>
              </div>
            </div>
            <div v-if="waError" class="px-4 pb-3 text-xs text-red-400">{{ waError }}</div>
          </div>

        </div>
      </div>

      <!-- Subscription -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04]"><h3 class="text-sm font-semibold text-nexus-200">Subscription</h3></div>
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div><div class="text-xs text-nexus-500">Current Plan</div><div class="text-lg font-bold text-gradient capitalize mt-0.5">{{ merchant?.subscription_tier || 'Starter' }}</div></div>
            <button @click="$inertia.visit('/subscription')" class="btn-secondary text-xs">Upgrade →</button>
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

const page = usePage();
const auth = computed(() => page.props.auth);
const merchant = computed(() => page.props.merchant);

const creds = computed(() => merchant.value?.meta_credentials || {});
const facebookConnected = computed(() => !!creds.value.facebook_page_id);
const whatsappConnected = computed(() => !!creds.value.whatsapp_phone_id);
const waConnecting = ref(false);
const waError = ref('');

const profileForm = useForm({ business_name: merchant.value?.business_name || '', phone: merchant.value?.phone || '', country: merchant.value?.country || '', city: merchant.value?.city || '' });
const accountForm = useForm({ name: auth.value?.name || '', email: auth.value?.email || '', password: '', password_confirmation: '' });
const channelsForm = useForm({
  whatsapp_phone_id: creds.value.whatsapp_phone_id || '',
  whatsapp_token:    creds.value.whatsapp_token || '',
});
const disconnectForm = useForm({});
const disconnectWaForm = useForm({});

function saveProfile() { profileForm.put('/settings/profile', { preserveScroll: true }); }
function saveAccount() { accountForm.put('/settings/account', { preserveScroll: true }); }

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
