<template>
  <AuthenticatedLayout :title="$t('pageTitles.followUps')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">{{ $t('followUps.title') }}</h2>
          <p class="text-nexus-500 text-sm mt-1">{{ $t('followUps.subtitle') }}</p>
        </div>
        <button @click="showCreateModal = true" class="btn-primary flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
          {{ $t('followUps.new') }}
        </button>
      </div>

      <!-- Follow-Up Sequences -->
      <div class="space-y-4">
        <div v-for="seq in sequences" :key="seq.id" class="glass-glow rounded-2xl p-5 hover:border-white/10 transition-all">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
              <div :class="['w-10 h-10 rounded-xl flex items-center justify-center text-lg', seq.is_active ? 'bg-accent-emerald/15 text-accent-emerald' : 'bg-nexus-500/10 text-nexus-600']">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" /></svg>
              </div>
              <div>
                <div class="text-sm font-semibold text-nexus-100">{{ seq.name }}</div>
                <div class="flex items-center gap-3 mt-0.5">
                  <span class="text-[10px] text-nexus-500">{{ seq.delay_hours }}{{ $t('followUps.delay') }}</span>
                  <span class="text-[10px] text-nexus-500">{{ $t('followUps.trigger') }}: {{ seq.trigger_condition === 'no_order' ? $t('followUps.noOrder') : $t('followUps.noReply') }}</span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button @click="toggleSequence(seq)" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', seq.is_active ? 'bg-accent-emerald' : 'bg-nexus-700']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm', seq.is_active ? 'translate-x-6' : 'translate-x-1']" />
              </button>
              <button @click="deleteSequence(seq.id)" class="p-1.5 rounded-lg hover:bg-red-500/10 text-nexus-600 hover:text-red-400 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          </div>
          
          <!-- Stats -->
          <div class="grid grid-cols-3 gap-3 mt-3 pt-3 border-t border-white/[0.04]">
            <div class="text-center">
              <div class="text-lg font-bold text-nexus-200">{{ seq.sent_count || 0 }}</div>
              <div class="text-[10px] text-nexus-600 uppercase tracking-wider">{{ $t('followUps.sent') }}</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-accent-emerald">{{ seq.converted_count || 0 }}</div>
              <div class="text-[10px] text-nexus-600 uppercase tracking-wider">{{ $t('followUps.converted') }}</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-accent-cyan">{{ seq.conversion_rate || '0' }}%</div>
              <div class="text-[10px] text-nexus-600 uppercase tracking-wider">{{ $t('followUps.rate') }}</div>
            </div>
          </div>

          <div class="mt-3 p-3 rounded-xl bg-nexus-900/40 text-xs text-nexus-400 whitespace-pre-wrap">{{ seq.message_template }}</div>
        </div>

        <div v-if="!sequences.length" class="p-12 text-center glass-glow rounded-2xl">
          <div class="w-16 h-16 rounded-2xl bg-accent-emerald/5 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-accent-emerald/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3" /></svg>
          </div>
          <p class="text-nexus-500 text-sm">{{ $t('followUps.noSequences') }}</p>
        </div>
      </div>

      <!-- Pending Scheduled Messages -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04] flex items-center gap-2">
          <svg class="w-4 h-4 text-accent-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <h3 class="text-sm font-semibold text-nexus-200">{{ $t('followUps.scheduledTitle') }}</h3>
          <span class="text-[10px] text-nexus-600 ml-auto">{{ scheduledMessages.length }} {{ $t('followUps.pending') }}</span>
        </div>
        <div class="divide-y divide-white/[0.03]">
          <div v-for="msg in scheduledMessages" :key="msg.id" class="px-6 py-3.5 flex items-center justify-between hover:bg-white/[0.02] transition-colors">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-medium text-nexus-200">{{ msg.conversation?.contact?.name || $t('chat.unknown') }}</span>
                <span class="text-[10px] text-nexus-600">{{ msg.conversation?.contact?.phone }}</span>
              </div>
              <div class="text-xs text-nexus-400 truncate">{{ msg.body }}</div>
            </div>
            <div class="flex items-center gap-3 ml-4">
              <div class="text-right">
                <div class="text-xs font-medium text-accent-amber">{{ formatDate(msg.scheduled_at) }}</div>
                <div v-if="msg.creator" class="text-[10px] text-nexus-600">by {{ msg.creator.name }}</div>
              </div>
              <button @click="cancelScheduled(msg.id)" class="p-1.5 rounded-lg hover:bg-red-500/10 text-nexus-600 hover:text-red-400 transition-all" title="Cancel">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
          </div>
          <div v-if="!scheduledMessages.length" class="px-6 py-8 text-center text-sm text-nexus-600">
            {{ $t('followUps.noScheduled') }}
          </div>
        </div>
      </div>

      <!-- Create Sequence Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showCreateModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-lg border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ $t('followUps.modalTitle') }}</h3>
            <form @submit.prevent="createSequence" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('followUps.name') }}</label>
                <input v-model="seqForm.name" type="text" :placeholder="$t('followUps.namePlaceholder')" class="input-premium" required />
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('followUps.delayLabel') }}</label>
                <input v-model.number="seqForm.delay_hours" type="number" min="1" max="168" class="input-premium" required />
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('followUps.trigger') }}</label>
                <select v-model="seqForm.trigger_condition" class="input-premium cursor-pointer">
                  <option value="no_order">{{ $t('followUps.triggerNoOrder') }}</option>
                  <option value="no_reply">{{ $t('followUps.triggerNoReply') }}</option>
                </select>
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('followUps.messageLabel') }}</label>
                <textarea v-model="seqForm.message_template" rows="4" class="input-premium py-2.5 text-sm resize-none" :placeholder="$t('followUps.messagePlaceholder')" required></textarea>
                <div class="text-[10px] text-nexus-600 mt-1">{{ $t('followUps.availableVars') }}: {customer_name}, {product_name}</div>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showCreateModal = false" class="btn-secondary text-sm px-4 py-2">{{ $t('common.cancel') }}</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">{{ $t('followUps.create') }}</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  sequences: { type: Array, default: () => [] },
  scheduledMessages: { type: Array, default: () => [] },
});

const showCreateModal = ref(false);
const seqForm = reactive({
  name: '',
  delay_hours: 24,
  trigger_condition: 'no_order',
  message_template: '',
});

function createSequence() {
  router.post('/follow-ups', { ...seqForm }, {
    onSuccess: () => {
      showCreateModal.value = false;
      seqForm.name = '';
      seqForm.delay_hours = 24;
      seqForm.trigger_condition = 'no_order';
      seqForm.message_template = '';
    },
  });
}

function toggleSequence(seq) {
  router.post(`/follow-ups/${seq.id}/toggle`);
}

function deleteSequence(id) {
  router.delete(`/follow-ups/${id}`);
}

function cancelScheduled(id) {
  router.delete(`/scheduled-messages/${id}`);
}

function formatDate(d) {
  return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }) : '';
}
</script>
