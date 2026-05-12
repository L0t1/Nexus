<template>
  <AuthenticatedLayout :title="$t('orders.title')">
    <div class="space-y-5 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-nexus-100">{{ $t('orders.title') }}</h2>
          <p class="text-nexus-500 text-sm mt-1">{{ $t('orders.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
          <a href="/export/orders" class="btn-secondary flex items-center gap-2 text-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg> {{ $t('common.export') }}</a>
          <button @click="openManualOrder" class="btn-primary flex items-center gap-2 text-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg> {{ $t('orders.newOrder') }}</button>
        </div>
      </div>

      <!-- Status tabs -->
      <div class="flex gap-1.5 p-1 rounded-xl bg-white/[0.02] border border-white/[0.04] w-full sm:w-fit overflow-x-auto hide-scrollbar">
        <button v-for="f in statusFilters" :key="f.value" @click="setFilter(f.value)" :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap', activeFilter === f.value ? 'bg-nexus-500/15 text-nexus-300 shadow-sm' : 'text-nexus-600 hover:text-nexus-400']">{{ $t(f.label) }}</button>
      </div>

      <!-- Table -->
      <div class="glass-glow rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-premium min-w-[800px]">
          <thead>
            <tr>
              <th>{{ $t('orders.order') }}</th>
              <th>{{ $t('orders.customer') }}</th>
              <th>{{ $t('orders.city') }}</th>
              <th>{{ $t('orders.status') }}</th>
              <th>{{ $t('orders.payment') }}</th>
              <th class="text-right">{{ $t('orders.total') }}</th>
              <th class="text-right">{{ $t('orders.date') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders?.data" :key="order.id" class="cursor-pointer group" @click="$inertia.visit(`/orders/${order.id}`)">
              <td>
                <div class="flex items-center gap-2">
                  <span class="font-mono text-xs font-medium text-nexus-400 group-hover:text-nexus-300 transition-colors">{{ order.order_number }}</span>
                  <div v-if="order.is_ai_generated" title="AI Generated" class="w-4 h-4 rounded-full bg-accent-violet/10 flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12" /></svg>
                  </div>
                </div>
              </td>
              <td>
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-nexus-500/15 to-accent-violet/15 flex items-center justify-center text-[10px] font-bold text-nexus-400">{{ order.customer_name?.[0] }}</div>
                  <div>
                    <div class="text-sm text-nexus-200 group-hover:text-nexus-100 transition-colors">{{ order.customer_name }}</div>
                    <div class="text-[10px] text-nexus-600">{{ order.customer_phone }}</div>
                  </div>
                </div>
              </td>
              <td class="text-sm text-nexus-400">{{ order.shipping_city || '—' }}</td>
              <td><StatusBadge :status="order.fulfillment_status" /></td>
              <td><StatusBadge :status="order.payment_status" /></td>
              <td class="text-right font-medium text-nexus-200">€{{ Number(order.total).toFixed(2) }}</td>
              <td class="text-right text-xs text-nexus-600">{{ formatDate(order.created_at) }}</td>
            </tr>
          </tbody>
          </table>
        </div>
        <div v-if="!orders?.data?.length" class="p-16 text-center">
          <div class="w-16 h-16 rounded-2xl bg-nexus-500/5 flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-nexus-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" /></svg></div>
          <p class="text-nexus-600 text-sm">{{ $t('orders.noOrders') }}</p>
        </div>
      </div>
      
      <!-- Order Panel Overlay -->
      <div v-if="showOrderPanel" @click="showOrderPanel = false" class="fixed inset-0 bg-black/60 z-40"></div>

      <!-- Order Panel -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-x-full" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 translate-x-full">
        <div v-if="showOrderPanel" class="fixed inset-y-0 right-0 w-full sm:w-96 z-50 glass-strong border-l border-white/[0.04] flex flex-col shadow-2xl">
          <div class="px-5 py-3.5 border-b border-white/[0.04] flex items-center justify-between bg-nexus-900/80 backdrop-blur-xl">
            <h3 class="text-sm font-semibold text-nexus-200 flex items-center gap-2"><span>✍️</span> {{ $t('chat.manualOrder') || 'Manual Order' }}</h3>
            <button @click="showOrderPanel = false" class="text-nexus-600 hover:text-nexus-300 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
          </div>
          <form @submit.prevent="submitOrder" novalidate class="flex-1 overflow-y-auto p-5 space-y-4 bg-nexus-900/40">
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.customerName') }} *</label>
              <input v-model="orderForm.customer_name" type="text" :class="['input-premium', orderForm.errors.customer_name ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
              <div v-if="orderForm.errors.customer_name" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.customer_name }}</div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.phone') }} *</label>
              <input v-model="orderForm.customer_phone" type="tel" oninput="this.value = this.value.replace(/[^0-9\s\+\-\(\)]/g, '')" :class="['input-premium', orderForm.errors.customer_phone ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
              <div v-if="orderForm.errors.customer_phone" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.customer_phone }}</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.city') }} *</label>
                <CitySelect v-model="orderForm.shipping_city" :input-class="['input-premium', orderForm.errors.shipping_city ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
                <div v-if="orderForm.errors.shipping_city" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_city }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.neighborhood') }}</label>
                <input v-model="orderForm.shipping_neighborhood" type="text" :class="['input-premium', orderForm.errors.shipping_neighborhood ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
                <div v-if="orderForm.errors.shipping_neighborhood" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_neighborhood }}</div>
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.address') }} *</label>
              <input v-model="orderForm.shipping_street" type="text" :class="['input-premium', orderForm.errors.shipping_street ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
              <div v-if="orderForm.errors.shipping_street" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_street }}</div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.shippingNotes') }}</label>
              <textarea v-model="orderForm.shipping_notes" rows="2" :class="['input-premium py-2 text-sm resize-none', orderForm.errors.shipping_notes ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']"></textarea>
              <div v-if="orderForm.errors.shipping_notes" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_notes }}</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.payment') }} *</label>
                <select v-model="orderForm.payment_method" :class="['input-premium cursor-pointer', orderForm.errors.payment_method ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']">
                  <option value="cod">{{ $t('chat.cashOnDelivery') || 'Cash on Delivery' }}</option>
                  <option value="bank_transfer">{{ $t('chat.bankTransfer') || 'Bank Transfer' }}</option>
                  <option value="online">{{ $t('chat.onlinePayment') || 'Online Payment' }}</option>
                </select>
                <div v-if="orderForm.errors.payment_method" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.payment_method }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.product') }} *</label>
                <select v-if="products.length" v-model="selectedProductId" @change="onProductSelect" :class="['input-premium cursor-pointer', itemErrors.name ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']">
                  <option value="">{{ $t('chat.customProduct') || 'Custom Product' }}</option>
                  <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} (€{{ Number(p.price).toFixed(2) }}) — {{ p.stock }} in stock</option>
                </select>
                <input v-if="!products.length || selectedProductId === ''" v-model="itemName" type="text" :placeholder="$t('chat.productName') || 'Product name'" :class="['input-premium', itemErrors.name ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '', products.length ? 'mt-2' : '']" />
                <div v-if="itemErrors.name" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ itemErrors.name }}</div>
              </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.qty') }} *</label>
                <input v-model.number="itemQty" type="number" min="1" :class="['input-premium', itemErrors.qty ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
                <div v-if="itemErrors.qty" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ itemErrors.qty }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.price') }} *</label>
                <input v-model.number="itemPrice" type="number" min="0.01" step="0.01" :class="['input-premium', itemErrors.price ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
                <div v-if="itemErrors.price" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ itemErrors.price }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.shipping') }}</label>
                <input v-model.number="orderForm.shipping_cost" type="number" min="0" step="0.01" :class="['input-premium', orderForm.errors.shipping_cost ? 'border-accent-rose/50 focus:border-accent-rose animate-shake' : '']" />
                <div v-if="orderForm.errors.shipping_cost" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ orderForm.errors.shipping_cost }}</div>
              </div>
            </div>
            <div class="pt-3 border-t border-white/[0.04]">
              <div class="flex justify-between text-sm mb-4"><span class="text-nexus-500">{{ $t('chat.total') || 'Total' }}</span><span class="font-bold text-xl text-gradient">€{{ ((itemQty * itemPrice) + (orderForm.shipping_cost || 0)).toFixed(2) }}</span></div>
              <button type="submit" :disabled="orderForm.processing" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-accent-emerald to-accent-cyan text-white text-sm font-semibold transition-all hover:shadow-lg hover:shadow-accent-emerald/20 disabled:opacity-50">
                {{ orderForm.processing ? $t('common.processing') || '...' : ($t('chat.confirmOrder') || 'Confirm Order') }}
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import CitySelect from '@/Components/CitySelect.vue';

const props = defineProps({
  orders: { type: Object, default: () => ({ data: [] }) },
  filters: { type: Object, default: () => ({}) },
  products: { type: Array, default: () => [] },
});

const activeFilter = ref(props.filters.status || '');
const statusFilters = [
  { label: 'orders.all', value: '' },
  { label: 'orders.draft', value: 'draft' },
  { label: 'orders.confirmed', value: 'confirmed' },
  { label: 'orders.dispatched', value: 'dispatched' },
  { label: 'orders.delivered', value: 'delivered' },
  { label: 'orders.returned', value: 'returned' },
];

function setFilter(v) { activeFilter.value = v; router.get('/orders', { status: v }, { preserveState: true }); }
function formatDate(d) { return d ? new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''; }

// Order Creation Logic
const showOrderPanel = ref(false);
const selectedProductId = ref('');
const itemName = ref('');
const itemQty = ref(1);
const itemPrice = ref(0);

const orderForm = useForm({
  customer_name: '',
  customer_phone: '',
  shipping_city: '',
  shipping_neighborhood: '',
  shipping_street: '',
  shipping_notes: '',
  shipping_cost: 3.00,
  payment_method: 'cod',
  items: [],
});

const itemErrors = computed(() => {
  return {
    name: orderForm.errors['items.0.name'] || orderForm.errors?.items?.[0]?.name,
    qty: orderForm.errors['items.0.qty'] || orderForm.errors?.items?.[0]?.qty,
    price: orderForm.errors['items.0.price'] || orderForm.errors?.items?.[0]?.price,
  };
});

function openManualOrder() {
  orderForm.reset();
  orderForm.shipping_cost = 3.00;
  orderForm.payment_method = 'cod';
  selectedProductId.value = '';
  itemName.value = '';
  itemQty.value = 1;
  itemPrice.value = '';
  showOrderPanel.value = true;
}

function onProductSelect() {
  if (selectedProductId.value) {
    const product = props.products.find(p => p.id === selectedProductId.value);
    if (product) {
      itemName.value = product.name;
      itemPrice.value = product.price;
    }
  } else {
    itemName.value = '';
    itemPrice.value = '';
  }
}

function submitOrder() {
  const item = { name: itemName.value || 'Product', qty: itemQty.value, price: itemPrice.value };
  if (selectedProductId.value) item.product_id = selectedProductId.value;
  orderForm.items = [item];
  orderForm.post('/orders', { onSuccess: () => { showOrderPanel.value = false; } });
}
</script>
