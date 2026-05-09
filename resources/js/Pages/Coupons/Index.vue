<template>
  <AuthenticatedLayout :title="$t('pageTitles.coupons')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-nexus-100">{{ $t('coupons.title') }}</h2><p class="text-nexus-500 text-sm mt-1">{{ $t('coupons.subtitle') }}</p></div>
        <button @click="showModal = true; editing = null; resetForm()" class="btn-primary flex items-center gap-2 text-sm">{{ $t('coupons.new') }}</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="c in coupons?.data" :key="c.id" class="glass-glow rounded-2xl p-5 group hover:border-white/10 transition-all">
          <div class="flex items-center justify-between mb-2">
            <span class="font-mono text-sm font-bold px-3 py-1 rounded-lg bg-accent-emerald/10 text-accent-emerald">{{ c.code }}</span>
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="edit(c)" class="p-1.5 rounded-lg hover:bg-white/5 text-nexus-500 hover:text-nexus-300 text-xs">✎</button>
              <button @click="del(c.id)" class="p-1.5 rounded-lg hover:bg-red-500/10 text-nexus-500 hover:text-red-400 text-xs">✕</button>
            </div>
          </div>
          <div class="text-sm text-nexus-200">{{ c.name }}</div>
          <div class="text-2xl font-bold text-gradient mt-1">{{ c.type === 'percentage' ? c.value + '%' : '€' + c.value }}</div>
          <div class="mt-3 space-y-1 text-xs text-nexus-500">
            <div class="flex justify-between"><span>{{ $t('coupons.used') }}</span><span>{{ c.usage_count || 0 }}{{ c.usage_limit ? ` / ${c.usage_limit}` : '' }}</span></div>
            <div v-if="c.expires_at" class="flex justify-between"><span>{{ $t('coupons.expires') }}</span><span>{{ fmtDate(c.expires_at) }}</span></div>
          </div>
        </div>
      </div>
      <div v-if="!coupons?.data?.length" class="p-16 text-center glass-glow rounded-2xl"><p class="text-nexus-600 text-sm">{{ $t('coupons.noCoupons') }}</p></div>

      <!-- Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-lg border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ editing ? $t('common.edit') : $t('common.new') }} {{ $t('coupons.title').split(' ')[0] }}</h3>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('coupons.code') }}</label><input v-model="form.code" class="input-premium font-mono uppercase" :disabled="!!editing" required /></div>
                <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('coupons.name') }}</label><input v-model="form.name" class="input-premium" required /></div>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('coupons.type') }}</label><select v-model="form.type" class="input-premium"><option value="percentage">{{ $t('coupons.percentage') }}</option><option value="fixed">{{ $t('coupons.fixed') }}</option></select></div>
                <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('coupons.value') }}</label><input v-model.number="form.value" type="number" step="0.01" min="0.01" class="input-premium" required /></div>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('coupons.minOrder') }}</label><input v-model.number="form.min_order_amount" type="number" step="0.01" class="input-premium" /></div>
                <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase">{{ $t('coupons.expires') }}</label><input v-model="form.expires_at" type="datetime-local" class="input-premium" /></div>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showModal = false" class="btn-secondary text-sm px-4 py-2">{{ $t('common.cancel') }}</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">{{ editing ? $t('common.save') : $t('common.create') }}</button>
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
defineProps({ coupons: { type: Object, default: () => ({ data: [] }) } });
const showModal = ref(false);
const editing = ref(null);
const form = reactive({ code: '', name: '', type: 'percentage', value: 10, min_order_amount: null, max_discount_amount: null, usage_limit: null, expires_at: '' });
function resetForm() { Object.assign(form, { code: '', name: '', type: 'percentage', value: 10, min_order_amount: null, max_discount_amount: null, usage_limit: null, expires_at: '' }); }
function edit(c) { editing.value = c; Object.assign(form, c); showModal.value = true; }
function save() { editing.value ? router.put(`/coupons/${editing.value.id}`, { ...form }, { onSuccess: () => showModal.value = false }) : router.post('/coupons', { ...form }, { onSuccess: () => { showModal.value = false; resetForm(); } }); }
function del(id) { router.delete(`/coupons/${id}`); }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }
</script>
