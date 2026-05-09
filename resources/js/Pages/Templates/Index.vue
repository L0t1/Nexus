<template>
  <AuthenticatedLayout :title="$t('pageTitles.templates')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">{{ $t('templates.title') }}</h2>
          <p class="text-nexus-500 text-sm mt-1">{{ $t('templates.subtitle') }}</p>
        </div>
        <button @click="showModal = true; editingTemplate = null; resetForm()" class="btn-primary flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
          {{ $t('templates.new') }}
        </button>
      </div>

      <!-- Template hint -->
      <div class="glass rounded-2xl px-5 py-4 flex items-start gap-3 border border-accent-cyan/10">
        <div class="w-8 h-8 rounded-lg bg-accent-cyan/10 flex items-center justify-center shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12" /></svg>
        </div>
        <div>
          <div class="text-sm font-medium text-nexus-200">{{ $t('templates.howItWorks') }}</div>
          <div class="text-xs text-nexus-500 mt-1">{{ $t('templates.guide') }}</div>
        </div>
      </div>

      <!-- Templates grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="t in templates" :key="t.id" class="glass-glow rounded-2xl p-5 group hover:border-white/10 transition-all">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <span class="font-mono text-xs px-2 py-1 rounded-lg bg-accent-cyan/10 text-accent-cyan">{{ t.shortcut }}</span>
              <span class="text-sm font-semibold text-nexus-100">{{ t.name }}</span>
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="startEdit(t)" class="p-1.5 rounded-lg hover:bg-white/5 text-nexus-500 hover:text-nexus-300 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
              </button>
              <button @click="deleteTemplate(t.id)" class="p-1.5 rounded-lg hover:bg-red-500/10 text-nexus-500 hover:text-red-400 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          </div>
          <div class="text-sm text-nexus-300 leading-relaxed whitespace-pre-wrap">{{ t.body }}</div>
          <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/[0.04]">
            <span v-if="t.category" class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-nexus-500/10 text-nexus-400">{{ t.category }}</span>
            <span class="text-[10px] text-nexus-600">{{ $t('templates.used') }} {{ t.usage_count || 0 }} {{ $t('templates.times') }}</span>
          </div>
        </div>
      </div>

      <div v-if="!templates.length" class="p-16 text-center glass-glow rounded-2xl">
        <div class="w-16 h-16 rounded-2xl bg-nexus-500/5 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
        </div>
        <p class="text-nexus-600 text-sm">{{ $t('templates.noTemplates') }}</p>
      </div>

      <!-- Create/Edit Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-lg border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ editingTemplate ? $t('templates.edit') : $t('templates.newTitle') }}</h3>
            <form @submit.prevent="saveTemplate" class="space-y-4">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('templates.shortcut') }}</label>
                  <input v-model="form.shortcut" type="text" placeholder="/price" class="input-premium font-mono" required />
                  <div class="text-[10px] text-nexus-600 mt-1">{{ $t('templates.shortcutHint') }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('templates.name') }}</label>
                  <input v-model="form.name" type="text" :placeholder="$t('templates.name')" class="input-premium" required />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('templates.category') }}</label>
                <select v-model="form.category" class="input-premium cursor-pointer">
                  <option value="">{{ $t('templates.none') }}</option>
                  <option value="greeting">{{ $t('templates.greeting') }}</option>
                  <option value="pricing">{{ $t('templates.pricing') }}</option>
                  <option value="shipping">{{ $t('templates.shipping') }}</option>
                  <option value="returns">{{ $t('templates.returns') }}</option>
                  <option value="custom">{{ $t('templates.custom') }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('templates.messageBody') }}</label>
                <textarea v-model="form.body" rows="5" class="input-premium py-2.5 text-sm resize-none" :placeholder="$t('templates.placeholder')" required></textarea>
                <div class="text-[10px] text-nexus-600 mt-1">{{ $t('templates.variables') }}: {customer_name}, {order_number}, {tracking_number}</div>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showModal = false" class="btn-secondary text-sm px-4 py-2">{{ $t('common.cancel') }}</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">{{ editingTemplate ? $t('common.save') : $t('common.create') }}</button>
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
  templates: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editingTemplate = ref(null);

const form = reactive({ shortcut: '/', name: '', body: '', category: '' });

function resetForm() {
  form.shortcut = '/';
  form.name = '';
  form.body = '';
  form.category = '';
}

function startEdit(t) {
  editingTemplate.value = t;
  form.shortcut = t.shortcut;
  form.name = t.name;
  form.body = t.body;
  form.category = t.category || '';
  showModal.value = true;
}

function saveTemplate() {
  if (editingTemplate.value) {
    router.put(`/templates/${editingTemplate.value.id}`, { ...form }, { onSuccess: () => { showModal.value = false; editingTemplate.value = null; } });
  } else {
    router.post('/templates', { ...form }, { onSuccess: () => { showModal.value = false; resetForm(); } });
  }
}

function deleteTemplate(id) {
  router.delete(`/templates/${id}`);
}
</script>
