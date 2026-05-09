<template>
  <AuthenticatedLayout :title="$t('products.title')">
    <div class="space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">{{ $t('products.title') }}</h2>
          <p class="text-nexus-500 text-sm mt-1">{{ $t('products.subtitle') }}</p>
        </div>
        <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-nexus-500 to-accent-cyan text-white text-sm font-medium rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
          {{ $t('products.addProduct') }}
        </button>
      </div>

      <!-- Products Table -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-white/[0.04] text-xs uppercase tracking-wider text-nexus-600 bg-white/[0.01]">
                <th class="px-6 py-4 font-medium">{{ $t('products.product') }}</th>
                <th class="px-6 py-4 font-medium">{{ $t('products.sku') }}</th>
                <th class="px-6 py-4 font-medium">{{ $t('products.price') }}</th>
                <th class="px-6 py-4 font-medium">{{ $t('products.stock') }}</th>
                <th class="px-6 py-4 font-medium">{{ $t('products.status') }}</th>
                <th class="px-6 py-4 font-medium text-right">{{ $t('common.actions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04]">
              <tr v-for="product in products.data" :key="product.id" class="hover:bg-white/[0.01] transition-colors group">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-white/[0.03] border border-white/[0.06] shrink-0 flex items-center justify-center">
                      <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
                      <svg v-else class="w-5 h-5 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z" /></svg>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-nexus-200">{{ product.name }}</div>
                      <div class="text-xs text-nexus-600 truncate max-w-xs mt-0.5">{{ product.description || 'No description' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-nexus-400">{{ product.sku || '-' }}</td>
                <td class="px-6 py-4 text-sm font-medium text-nexus-200">€{{ parseFloat(product.price).toFixed(2) }}</td>
                <td class="px-6 py-4">
                  <span class="text-sm" :class="product.stock > 10 ? 'text-accent-emerald' : product.stock > 0 ? 'text-accent-amber' : 'text-accent-rose font-bold'">
                    {{ product.stock }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider" :class="product.is_active ? 'bg-accent-emerald/10 text-accent-emerald' : 'bg-nexus-600/20 text-nexus-500'">
                    {{ product.is_active ? $t('products.active') : $t('products.draft') }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="openEditModal(product)" class="p-1.5 text-nexus-500 hover:text-nexus-300 rounded-lg hover:bg-white/5 transition-colors">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </button>
                    <button v-if="$page.props.auth?.user?.roles?.includes('admin')" @click="confirmDeleteProduct(product)" class="p-1.5 text-nexus-500 hover:text-accent-rose rounded-lg hover:bg-accent-rose/10 transition-colors">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="products.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-sm text-nexus-500">
                  {{ $t('products.noProducts') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="products.links && products.links.length > 3" class="px-6 py-4 border-t border-white/[0.04] flex items-center justify-center gap-1">
          <Link v-for="(link, i) in products.links" :key="i" :href="link.url || '#'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="link.active ? 'bg-nexus-500/20 text-nexus-200' : 'text-nexus-500 hover:text-nexus-300 hover:bg-white/[0.02]'" :disabled="!link.url" v-html="link.label" />
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
      <div class="relative w-full max-w-md glass-strong border border-white/10 rounded-2xl shadow-2xl animate-scale-up overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-white/[0.06] flex items-center justify-between shrink-0">
          <h3 class="text-lg font-bold text-nexus-100">{{ isEditing ? $t('products.editProduct') : $t('products.addProduct') }}</h3>
          <button @click="closeModal" class="p-1.5 text-nexus-500 hover:text-nexus-300 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.productName') }} <span class="text-accent-rose">*</span></label>
              <input v-model="form.name" type="text" required class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
              <p v-if="form.errors.name" class="mt-1 text-xs text-accent-rose">{{ form.errors.name }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.sku') }}</label>
                <input v-model="form.sku" type="text" class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
              </div>
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.status') }}</label>
                <select v-model="form.is_active" class="w-full px-3 py-2.5 rounded-xl bg-nexus-800 border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 transition-colors">
                  <option :value="true">{{ $t('products.active') }}</option>
                  <option :value="false">{{ $t('products.draft') }}</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.price') }} (€) <span class="text-accent-rose">*</span></label>
                <input v-model="form.price" type="number" step="0.01" min="0" required class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
                <p v-if="form.errors.price" class="mt-1 text-xs text-accent-rose">{{ form.errors.price }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.stock') }} <span class="text-accent-rose">*</span></label>
                <input v-model="form.stock" type="number" min="0" required class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
                <p v-if="form.errors.stock" class="mt-1 text-xs text-accent-rose">{{ form.errors.stock }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.productImage') }}</label>
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-white/[0.03] border border-white/[0.06] flex items-center justify-center shrink-0">
                  <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                  <svg v-else class="w-6 h-6 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z" /></svg>
                </div>
                <div class="flex-1">
                  <label class="block cursor-pointer">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/[0.08] text-xs text-nexus-300 hover:bg-white/[0.08] transition-colors">📷 {{ $t('products.chooseImage') }}</span>
                    <input type="file" accept="image/jpeg,image/png,image/webp" @change="onImageSelect" class="hidden" />
                  </label>
                  <p class="text-[10px] text-nexus-600 mt-1">JPG, PNG or WebP · Max 2MB</p>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-nexus-400 mb-1.5">{{ $t('products.description') }}</label>
              <textarea v-model="form.description" rows="3" class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors resize-none"></textarea>
            </div>
            
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-white/[0.06]">
              <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-nexus-400 hover:text-nexus-200 transition-colors">{{ $t('common.cancel') }}</button>
              <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-gradient-to-r from-nexus-500 to-accent-cyan text-white text-sm font-medium rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50">
                {{ form.processing ? $t('products.saving') : $t('products.saveProduct') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <ConfirmModal 
      :show="!!productToDelete" 
      :title="$t('products.deleteTitle')" 
      :message="$t('products.deleteConfirm').replace('{name}', productToDelete?.name)" 
      @close="productToDelete = null" 
      @confirm="executeDeleteProduct" 
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
  products: Object
});

const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const productToDelete = ref(null);

const form = useForm({
  name: '',
  sku: '',
  description: '',
  image: null,
  price: 0,
  stock: 0,
  is_active: true,
});

const imagePreview = ref(null);
const imageFile = ref(null);

function onImageSelect(e) {
  const file = e.target.files[0];
  if (!file) return;
  imageFile.value = file;
  imagePreview.value = URL.createObjectURL(file);
}

function openCreateModal() {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  imagePreview.value = null;
  imageFile.value = null;
  isModalOpen.value = true;
}

function openEditModal(product) {
  isEditing.value = true;
  editingId.value = product.id;
  form.name = product.name;
  form.sku = product.sku || '';
  form.description = product.description || '';
  form.price = product.price;
  form.stock = product.stock;
  form.is_active = product.is_active;
  form.clearErrors();
  imagePreview.value = product.image_url || null;
  imageFile.value = null;
  isModalOpen.value = true;
}

function closeModal() {
  isModalOpen.value = false;
}

function submit() {
  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('sku', form.sku || '');
  formData.append('description', form.description || '');
  formData.append('price', form.price);
  formData.append('stock', form.stock);
  formData.append('is_active', form.is_active ? '1' : '0');
  if (imageFile.value) formData.append('image', imageFile.value);

  if (isEditing.value) {
    formData.append('_method', 'PUT');
    router.post(`/products/${editingId.value}`, formData, {
      onSuccess: () => closeModal(),
      onError: (errors) => { form.errors = errors; },
    });
  } else {
    router.post('/products', formData, {
      onSuccess: () => closeModal(),
      onError: (errors) => { form.errors = errors; },
    });
  }
}

function confirmDeleteProduct(product) {
  productToDelete.value = product;
}

function executeDeleteProduct() {
  if (productToDelete.value) {
    router.delete(`/products/${productToDelete.value.id}`, {
      onSuccess: () => productToDelete.value = null
    });
  }
}
</script>
