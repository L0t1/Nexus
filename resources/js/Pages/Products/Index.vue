<template>
  <AuthenticatedLayout title="Products">
    <div class="space-y-6 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">Products</h2>
          <p class="text-nexus-500 text-sm mt-1">Manage your inventory and product catalog</p>
        </div>
        <button @click="openCreateModal" class="px-4 py-2 bg-gradient-to-r from-nexus-500 to-accent-cyan text-white text-sm font-medium rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
          Add Product
        </button>
      </div>

      <!-- Products Table -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-white/[0.04] text-xs uppercase tracking-wider text-nexus-600 bg-white/[0.01]">
                <th class="px-6 py-4 font-medium">Product</th>
                <th class="px-6 py-4 font-medium">SKU</th>
                <th class="px-6 py-4 font-medium">Price</th>
                <th class="px-6 py-4 font-medium">Stock</th>
                <th class="px-6 py-4 font-medium">Status</th>
                <th class="px-6 py-4 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04]">
              <tr v-for="product in products.data" :key="product.id" class="hover:bg-white/[0.01] transition-colors group">
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-nexus-200">{{ product.name }}</div>
                  <div class="text-xs text-nexus-600 truncate max-w-xs mt-0.5">{{ product.description || 'No description' }}</div>
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
                    {{ product.is_active ? 'Active' : 'Draft' }}
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
                  No products found. Create your first product!
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
          <h3 class="text-lg font-bold text-nexus-100">{{ isEditing ? 'Edit Product' : 'Add Product' }}</h3>
          <button @click="closeModal" class="p-1.5 text-nexus-500 hover:text-nexus-300 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-medium text-nexus-400 mb-1.5">Product Name <span class="text-accent-rose">*</span></label>
              <input v-model="form.name" type="text" required class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
              <p v-if="form.errors.name" class="mt-1 text-xs text-accent-rose">{{ form.errors.name }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">SKU</label>
                <input v-model="form.sku" type="text" class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
              </div>
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">Status</label>
                <select v-model="form.is_active" class="w-full px-3 py-2.5 rounded-xl bg-nexus-800 border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 transition-colors">
                  <option :value="true">Active</option>
                  <option :value="false">Draft</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">Price (€) <span class="text-accent-rose">*</span></label>
                <input v-model="form.price" type="number" step="0.01" min="0" required class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
                <p v-if="form.errors.price" class="mt-1 text-xs text-accent-rose">{{ form.errors.price }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-nexus-400 mb-1.5">Stock <span class="text-accent-rose">*</span></label>
                <input v-model="form.stock" type="number" min="0" required class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors" />
                <p v-if="form.errors.stock" class="mt-1 text-xs text-accent-rose">{{ form.errors.stock }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-nexus-400 mb-1.5">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-3 py-2.5 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 focus:outline-none focus:border-nexus-500/50 focus:bg-white/[0.05] transition-colors resize-none"></textarea>
            </div>
            
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-white/[0.06]">
              <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-nexus-400 hover:text-nexus-200 transition-colors">Cancel</button>
              <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-gradient-to-r from-nexus-500 to-accent-cyan text-white text-sm font-medium rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50">
                {{ form.processing ? 'Saving...' : 'Save Product' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <ConfirmModal 
      :show="!!productToDelete" 
      title="Delete Product?" 
      :message="`Are you sure you want to permanently delete ${productToDelete?.name}? This action cannot be undone.`" 
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
  price: 0,
  stock: 0,
  is_active: true,
});

function openCreateModal() {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
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
  isModalOpen.value = true;
}

function closeModal() {
  isModalOpen.value = false;
}

function submit() {
  if (isEditing.value) {
    form.put(`/products/${editingId.value}`, {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post('/products', {
      onSuccess: () => closeModal(),
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
