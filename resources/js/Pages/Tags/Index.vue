<template>
  <AuthenticatedLayout :title="$t('pageTitles.tags')">
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">Tags & Segmentation</h2>
          <p class="text-nexus-500 text-sm mt-1">Organize your contacts with custom tags</p>
        </div>
        <button @click="showCreateModal = true" class="btn-primary flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
          New Tag
        </button>
      </div>

      <!-- Tags Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="tag in tags" :key="tag.id" class="glass-glow rounded-2xl p-5 group hover:border-white/10 transition-all">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
              <div class="w-4 h-4 rounded-full shadow-lg" :style="{ backgroundColor: tag.color }"></div>
              <span class="text-sm font-semibold text-nexus-100">{{ tag.name }}</span>
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="editTag(tag)" class="p-1.5 rounded-lg hover:bg-white/5 text-nexus-500 hover:text-nexus-300 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
              </button>
              <button @click="confirmDeleteTag(tag)" class="p-1.5 rounded-lg hover:bg-red-500/10 text-nexus-500 hover:text-red-400 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          </div>
          <div class="text-xs text-nexus-500">{{ tag.contacts_count || 0 }} contacts</div>
          <button @click="$inertia.visit(`/contacts?tag=${tag.id}`)" class="mt-3 text-[10px] text-nexus-400 hover:text-accent-cyan transition-colors flex items-center gap-1">
            View contacts →
          </button>
        </div>

        <div v-if="!tags.length" class="col-span-full p-12 text-center glass rounded-2xl">
          <div class="w-16 h-16 rounded-2xl bg-nexus-500/5 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
          </div>
          <p class="text-nexus-500 text-sm">No tags yet. Create your first tag to start segmenting contacts.</p>
        </div>
      </div>

      <!-- Auto-Tag Rules -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.04] flex items-center justify-between">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12" /></svg>
            <h3 class="text-sm font-semibold text-nexus-200">Auto-Tag Rules</h3>
          </div>
          <div class="flex items-center gap-2">
            <button @click="runRules" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" /></svg>
              Run Now
            </button>
            <button @click="showRuleModal = true" class="btn-primary text-xs py-1.5 px-3">+ Add Rule</button>
          </div>
        </div>
        <div class="divide-y divide-white/[0.03]">
          <div v-for="rule in autoRules" :key="rule.id" class="px-6 py-3.5 flex items-center justify-between hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center gap-3 text-sm">
              <span class="text-nexus-400">If</span>
              <span class="font-mono text-xs px-2 py-1 rounded-lg bg-accent-cyan/10 text-accent-cyan">{{ rule.field }}</span>
              <span class="text-nexus-500">{{ rule.operator }}</span>
              <span class="font-mono text-xs px-2 py-1 rounded-lg bg-accent-amber/10 text-accent-amber">{{ rule.value }}</span>
              <span class="text-nexus-400">→ tag as</span>
              <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium" :style="{ backgroundColor: rule.tag?.color + '20', color: rule.tag?.color }">
                <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: rule.tag?.color }"></span>
                {{ rule.tag?.name }}
              </span>
            </div>
            <button @click="deleteRule(rule.id)" class="p-1.5 rounded-lg hover:bg-red-500/10 text-nexus-600 hover:text-red-400 transition-all">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
          <div v-if="!autoRules.length" class="px-6 py-8 text-center text-sm text-nexus-600">
            No auto-tag rules. Add one to automatically categorize contacts.
          </div>
        </div>
      </div>

      <!-- Create/Edit Tag Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showCreateModal || editingTag" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="closeTagModal" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ editingTag ? 'Edit Tag' : 'Create Tag' }}</h3>
            <form @submit.prevent="saveTag" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Name</label>
                <input v-model="tagForm.name" type="text" placeholder="e.g. VIP, Wholesale, Repeat" class="input-premium" required />
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Color</label>
                <div class="flex gap-2 flex-wrap">
                  <button v-for="c in colorOptions" :key="c" type="button" @click="tagForm.color = c" :class="['w-8 h-8 rounded-lg transition-all', tagForm.color === c ? 'ring-2 ring-white/40 scale-110' : 'hover:scale-105']" :style="{ backgroundColor: c }"></button>
                </div>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="closeTagModal" class="btn-secondary text-sm px-4 py-2">Cancel</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">{{ editingTag ? 'Save' : 'Create' }}</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>

      <!-- Auto-Tag Rule Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showRuleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showRuleModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">Create Auto-Tag Rule</h3>
            <form @submit.prevent="saveRule" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">When</label>
                <select v-model="ruleForm.field" class="input-premium cursor-pointer">
                  <option value="total_spent">Total Spent (€)</option>
                  <option value="total_orders">Total Orders</option>
                  <option value="city">City</option>
                  <option value="platform">Platform</option>
                </select>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Operator</label>
                  <select v-model="ruleForm.operator" class="input-premium cursor-pointer">
                    <option value=">">&gt; Greater than</option>
                    <option value=">=">&ge; Greater or equal</option>
                    <option value="<">&lt; Less than</option>
                    <option value="<=">&le; Less or equal</option>
                    <option value="=">=  Equals</option>
                    <option value="!=">≠ Not equal</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Value</label>
                  <input v-model="ruleForm.value" type="text" class="input-premium" placeholder="e.g. 500" required />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">Apply Tag</label>
                <select v-model="ruleForm.tag_id" class="input-premium cursor-pointer" required>
                  <option value="">Select a tag...</option>
                  <option v-for="t in tags" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showRuleModal = false" class="btn-secondary text-sm px-4 py-2">Cancel</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Create Rule</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>

      <ConfirmModal :show="showDeleteConfirm" :title="$t('pageTitles.deleteTag')" :message="$t('pageTitles.deleteTagMsg')" @close="showDeleteConfirm = false" @confirm="doDeleteTag" />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
  tags: { type: Array, default: () => [] },
  autoRules: { type: Array, default: () => [] },
});

const colorOptions = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#64748b'];

const showCreateModal = ref(false);
const editingTag = ref(null);
const showDeleteConfirm = ref(false);
const tagToDelete = ref(null);
const showRuleModal = ref(false);

const tagForm = reactive({ name: '', color: '#6366f1' });
const ruleForm = reactive({ field: 'total_spent', operator: '>', value: '', tag_id: '' });

function editTag(tag) {
  editingTag.value = tag;
  tagForm.name = tag.name;
  tagForm.color = tag.color;
}

function closeTagModal() {
  showCreateModal.value = false;
  editingTag.value = null;
  tagForm.name = '';
  tagForm.color = '#6366f1';
}

function saveTag() {
  if (editingTag.value) {
    router.put(`/tags/${editingTag.value.id}`, { ...tagForm }, { onSuccess: () => closeTagModal() });
  } else {
    router.post('/tags', { ...tagForm }, { onSuccess: () => closeTagModal() });
  }
}

function confirmDeleteTag(tag) {
  tagToDelete.value = tag;
  showDeleteConfirm.value = true;
}

function doDeleteTag() {
  showDeleteConfirm.value = false;
  if (tagToDelete.value) router.delete(`/tags/${tagToDelete.value.id}`);
}

function saveRule() {
  router.post('/tags/rules', { ...ruleForm }, { onSuccess: () => { showRuleModal.value = false; ruleForm.field = 'total_spent'; ruleForm.operator = '>'; ruleForm.value = ''; ruleForm.tag_id = ''; } });
}

function deleteRule(id) {
  router.delete(`/tags/rules/${id}`);
}

function runRules() {
  router.post('/tags/rules/run');
}
</script>
