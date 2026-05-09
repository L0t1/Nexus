<template>
  <AuthenticatedLayout :title="$t('chat.title')">
    <div class="flex h-[calc(100vh-140px)] gap-4 animate-fade-in">
      <!-- Chat area -->
      <div class="flex-1 glass-glow rounded-2xl flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="px-5 py-3.5 border-b border-white/[0.04] flex items-center justify-between">
          <div class="flex items-center gap-3">
            <button @click="$inertia.visit('/inbox')" class="p-1.5 rounded-lg hover:bg-surface-hover text-nexus-500 hover:text-nexus-300 transition-all">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-nexus-500/30 to-accent-cyan/20 flex items-center justify-center text-sm font-bold text-nexus-200">
              {{ conversation.contact?.name?.[0]?.toUpperCase() || '?' }}
            </div>
            <div>
              <div class="text-sm font-semibold text-nexus-100">{{ conversation.contact?.name || $t('chat.unknown') }}</div>
              <div class="flex items-center gap-2 text-xs text-nexus-500">
                <PlatformIcon :platform="conversation.platform" class="w-3.5 h-3.5" />
                <span>{{ conversation.contact?.phone }}</span>
                <span v-if="conversation.contact?.city" class="opacity-50">·</span>
                <span v-if="conversation.contact?.city">{{ conversation.contact.city }}</span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2.5">
            <button v-if="$page.props.auth?.user?.roles?.includes('admin')" @click="showDeleteModal = true" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 border-red-500/20">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              {{ $t('chat.delete') }}
            </button>
            <StatusBadge :status="conversation.status" />
            <button @click="openManualOrder" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5">
              <span>✍️</span> {{ $t('chat.manualOrder') }}
            </button>
            <button v-if="$page.props.auth.merchant.features.includes('ai_extraction')" @click="openSmartOrder" class="btn-primary text-xs py-1.5 px-3 flex items-center gap-1.5 bg-gradient-to-r from-accent-emerald to-accent-cyan border-none text-white shadow-lg shadow-accent-emerald/20 hover:shadow-accent-emerald/40">
              <span>✨</span> {{ $t('chat.smartOrder') }}
            </button>
            <button v-else @click="$inertia.visit('/subscription')" class="btn-primary text-xs py-1.5 px-3 flex items-center gap-1.5 bg-gradient-to-r from-accent-emerald to-accent-cyan border-none text-white shadow-lg shadow-accent-emerald/20 opacity-50 hover:shadow-none" title="Upgrade to unlock Smart Order">
              <span>🔒</span> {{ $t('chat.smartOrder') }}
            </button>
          </div>
        </div>

        <!-- Messages -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-5 space-y-3">
          <div v-for="msg in sortedMessages" :key="msg.id" :class="['flex', msg.direction === 'outbound' ? 'justify-end' : 'justify-start']">
            <div :class="['max-w-[65%] px-4 py-3 text-sm', msg.direction === 'outbound' ? 'bubble-outbound text-white' : 'bubble-inbound text-nexus-200']">
              <!-- Image message -->
              <div v-if="msg.type === 'image' && msg.media?.url" class="mb-2 -mx-1 -mt-1">
                <img :src="msg.media.url" :alt="msg.body || 'Image'" class="rounded-lg max-w-full max-h-64 object-contain cursor-pointer" @click="window.open(msg.media.url, '_blank')" />
              </div>
              <p v-if="msg.body" class="leading-relaxed">{{ msg.body }}</p>
              <div :class="['text-[10px] mt-1.5 flex items-center gap-1', msg.direction === 'outbound' ? 'text-white/50 justify-end' : 'text-nexus-600']">
                {{ formatTime(msg.created_at) }}
                <!-- AI-generated label -->
                <span v-if="msg.is_ai_generated" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-accent-violet/20 text-accent-violet text-[9px] font-bold uppercase tracking-wide">
                  <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                  AI
                </span>
                <span v-if="msg.direction === 'outbound'">
                  <!-- Read: Double Blue -->
                  <svg v-if="msg.status === 'read'" class="w-3 h-3 text-accent-cyan" viewBox="0 0 16 16" fill="currentColor"><path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486z"/></svg>
                  <!-- Delivered: Double Grey -->
                  <svg v-else-if="msg.status === 'delivered'" class="w-3 h-3" viewBox="0 0 16 16" fill="currentColor"><path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486z"/></svg>
                  <!-- Sent: Single Grey -->
                  <svg v-else class="w-3 h-3" viewBox="0 0 16 16" fill="currentColor"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                </span>
              </div>
              <!-- AI badge -->
              <div v-if="msg.ai_extraction" class="mt-3 px-3 py-2 rounded-xl bg-nexus-500/8 border border-nexus-500/15 text-xs space-y-0.5">
                <div class="text-nexus-400 font-semibold flex items-center gap-1 mb-1"><span>🤖</span> {{ $t('chat.aiDetected') }}</div>
                <div v-if="msg.ai_extraction.customer_name" class="text-nexus-300">{{ $t('chat.name') }}: {{ msg.ai_extraction.customer_name }}</div>
                <div v-if="msg.ai_extraction.phone_number" class="text-nexus-300">{{ $t('chat.phone') }}: {{ msg.ai_extraction.phone_number }}</div>
                <div v-if="msg.ai_extraction.city" class="text-nexus-300">{{ $t('chat.city') }}: {{ msg.ai_extraction.city }}</div>
                <div v-if="msg.ai_extraction.street_address" class="text-nexus-300">{{ $t('chat.address') }}: {{ msg.ai_extraction.street_address }}</div>
                <div v-if="msg.ai_extraction.product_intent" class="text-nexus-300">{{ $t('chat.product') }}: {{ msg.ai_extraction.product_intent }}</div>
                <div v-if="msg.ai_extraction.quantity" class="text-nexus-300">{{ $t('chat.quantity') }}: {{ msg.ai_extraction.quantity }}</div>
              </div>
            </div>
          </div>
          <div v-if="!sortedMessages.length" class="flex flex-col items-center justify-center h-full text-nexus-700">
            <svg class="w-12 h-12 mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
            <span class="text-sm">{{ $t('chat.noMessages') }}</span>
          </div>
        </div>

        <!-- Reply -->
        <div class="px-5 py-3.5 border-t border-white/[0.04]">
          <!-- Template dropdown -->
          <Transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0 translate-y-2">
            <div v-if="showTemplates && filteredTemplates.length" class="mb-2 glass-strong rounded-xl overflow-hidden border border-white/10 max-h-48 overflow-y-auto">
              <div v-for="(tpl, i) in filteredTemplates" :key="tpl.id" @click="insertTemplate(tpl)" :class="['px-4 py-2.5 cursor-pointer flex items-center gap-3 text-sm transition-colors', i === selectedTemplateIdx ? 'bg-nexus-500/15 text-nexus-100' : 'text-nexus-300 hover:bg-white/5']">
                <span class="font-mono text-xs text-accent-cyan shrink-0">{{ tpl.shortcut }}</span>
                <span class="truncate">{{ tpl.name }}</span>
                <span v-if="tpl.category" class="text-[9px] uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-nexus-500/10 text-nexus-500 ml-auto shrink-0">{{ tpl.category }}</span>
              </div>
            </div>
          </Transition>
          <!-- Image preview strip -->
          <div v-if="replyImage" class="mb-2 flex items-center gap-2">
            <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-white/[0.08]">
              <img :src="replyImagePreview" class="w-full h-full object-cover" />
              <button @click="clearReplyImage" class="absolute top-0.5 right-0.5 w-4 h-4 bg-black/60 rounded-full flex items-center justify-center text-white text-[10px] hover:bg-black/80">&times;</button>
            </div>
            <span class="text-xs text-nexus-500">{{ replyImage.name }}</span>
          </div>
          <form @submit.prevent="sendReply" class="flex gap-2 items-center">
            <label class="p-2.5 rounded-xl text-nexus-500 hover:text-nexus-300 hover:bg-white/[0.04] cursor-pointer transition-colors shrink-0">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z" /></svg>
              <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" @change="onReplyImageSelect" class="hidden" />
            </label>
            <input v-model="replyText" @input="onReplyInput" @keydown.tab.prevent="selectTemplate" type="text" :placeholder="$t('chat.typePlaceholder')" class="input-premium flex-1" />
            <button type="button" @click="showScheduleModal = true" class="p-2.5 rounded-xl text-nexus-500 hover:text-accent-amber hover:bg-accent-amber/10 transition-colors shrink-0" title="Schedule message">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </button>
            <button type="submit" :disabled="!replyText.trim() && !replyImage" class="btn-primary px-5 disabled:opacity-30">{{ $t('chat.send') }}</button>
          </form>
          <!-- Scheduled messages indicator -->
          <div v-if="scheduledMessages.length" class="mt-2 flex items-center gap-2 text-[10px] text-accent-amber">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ scheduledMessages.length }} {{ $t('chat.scheduled') }}
          </div>
        </div>
      </div>

      <!-- Order Panel Overlay (Mobile) -->
      <div v-if="showOrderPanel" @click="showOrderPanel = false" class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>

      <!-- Order Panel -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-x-full lg:translate-x-4" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 translate-x-full lg:translate-x-4">
        <div v-if="showOrderPanel" class="fixed inset-y-0 right-0 w-full sm:w-96 z-50 glass-strong border-l border-white/[0.04] flex flex-col lg:static lg:z-auto lg:border-0 lg:glass-glow lg:rounded-2xl lg:h-full flex-shrink-0 shadow-2xl lg:shadow-none">
          <div class="px-5 py-3.5 border-b border-white/[0.04] flex items-center justify-between">
            <h3 class="text-sm font-semibold text-nexus-200 flex items-center gap-2"><span>📦</span> {{ $t('chat.orderDetails') }}</h3>
            <button @click="showOrderPanel = false" class="text-nexus-600 hover:text-nexus-300 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
          </div>
          <form @submit.prevent="submitOrder" class="flex-1 overflow-y-auto p-5 space-y-4">
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.customerName') }} *</label><input v-model="orderForm.customer_name" type="text" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.customer_name}]" /><div v-if="orderForm.errors.customer_name" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.customer_name }}</div></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.phone') }} *</label><input v-model="orderForm.customer_phone" type="tel" oninput="this.value = this.value.replace(/[^0-9\s\+\-\(\)]/g, '')" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.customer_phone}]" /><div v-if="orderForm.errors.customer_phone" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.customer_phone }}</div></div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.city') }} *</label><CitySelect v-model="orderForm.shipping_city" :input-class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.shipping_city}]" /><div v-if="orderForm.errors.shipping_city" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_city }}</div></div>
              <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.neighborhood') }}</label><input v-model="orderForm.shipping_neighborhood" type="text" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.shipping_neighborhood}]" /><div v-if="orderForm.errors.shipping_neighborhood" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_neighborhood }}</div></div>
            </div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.address') }} *</label><input v-model="orderForm.shipping_street" type="text" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.shipping_street}]" /><div v-if="orderForm.errors.shipping_street" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_street }}</div></div>
            <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.shippingNotes') }}</label><textarea v-model="orderForm.shipping_notes" rows="2" :placeholder="$t('chat.shippingPlaceholder')" :class="['input-premium py-2 text-sm', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.shipping_notes}]"></textarea><div v-if="orderForm.errors.shipping_notes" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.shipping_notes }}</div></div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.payment') }} *</label>
                <select v-model="orderForm.payment_method" :class="['input-premium cursor-pointer', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.payment_method}]">
                  <option value="cod">{{ $t('chat.cashOnDelivery') }}</option>
                  <option value="bank_transfer">{{ $t('chat.bankTransfer') }}</option>
                  <option value="online">{{ $t('chat.onlinePayment') }}</option>
                </select>
                <div v-if="orderForm.errors.payment_method" class="text-accent-rose text-[10px] mt-1">{{ orderForm.errors.payment_method }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.product') }} *</label>
                <select v-if="products.length" v-model="selectedProductId" @change="onProductSelect" :class="['input-premium cursor-pointer', {'border-accent-rose/50 focus:border-accent-rose': itemErrors.name}]">
                  <option value="">{{ $t('chat.customProduct') }}</option>
                  <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} (€{{ Number(p.price).toFixed(2) }}) — {{ p.stock }} {{ $t('chat.inStock') }}</option>
                </select>
                <input v-if="!products.length || selectedProductId === ''" v-model="itemName" type="text" :placeholder="$t('chat.productName')" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': itemErrors.name}, products.length ? 'mt-2' : '']" />
                <div v-if="itemErrors.name" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ itemErrors.name }}</div>
              </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.qty') }} *</label>
                <input v-model.number="itemQty" type="number" min="1" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': itemErrors.qty}]" />
                <div v-if="itemErrors.qty" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ itemErrors.qty }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.price') }} *</label>
                <input v-model.number="itemPrice" type="number" min="0" step="0.01" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': itemErrors.price}]" />
                <div v-if="itemErrors.price" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ itemErrors.price }}</div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.shipping') }}</label>
                <input v-model.number="orderForm.shipping_cost" type="number" min="0" step="0.01" :class="['input-premium', {'border-accent-rose/50 focus:border-accent-rose': orderForm.errors.shipping_cost}]" />
                <div v-if="orderForm.errors.shipping_cost" class="text-accent-rose text-[10px] mt-1 leading-tight">{{ orderForm.errors.shipping_cost }}</div>
              </div>
            </div>
            <div class="pt-3 border-t border-white/[0.04]">
              <div class="flex justify-between text-sm mb-4"><span class="text-nexus-500">{{ $t('chat.total') }}</span><span class="font-bold text-xl text-gradient">€{{ ((itemQty * itemPrice) + (orderForm.shipping_cost || 0)).toFixed(2) }}</span></div>
              <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-accent-emerald to-accent-cyan text-white text-sm font-semibold transition-all hover:shadow-lg hover:shadow-accent-emerald/20">{{ $t('chat.confirmOrder') }}</button>
            </div>
          </form>
        </div>
      </Transition>
      <!-- Delete Confirm Modal -->
      <ConfirmModal 
        :show="showDeleteModal" 
        :title="$t('chat.deleteChat')" 
        :message="$t('chat.deleteChatMessage')" 
        @close="showDeleteModal = false" 
        @confirm="confirmDelete" 
      />

      <!-- Schedule Message Modal -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showScheduleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showScheduleModal = false" class="absolute inset-0 bg-black/60"></div>
          <div class="relative glass-strong rounded-2xl p-6 w-full max-w-md border border-white/10 shadow-2xl">
            <h3 class="text-base font-bold text-nexus-100 mb-4">{{ $t('chat.scheduleMessage') }}</h3>
            <form @submit.prevent="scheduleMessage" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.message') }}</label>
                <textarea v-model="scheduleForm.body" rows="3" class="input-premium py-2.5 text-sm resize-none" :placeholder="$t('chat.messagePlaceholder')" required></textarea>
              </div>
              <div>
                <label class="block text-xs font-semibold text-nexus-400 mb-1.5 uppercase tracking-wider">{{ $t('chat.sendAt') }}</label>
                <input v-model="scheduleForm.scheduled_at" type="datetime-local" class="input-premium" required />
              </div>
              <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showScheduleModal = false" class="btn-secondary text-sm px-4 py-2">{{ $t('common.cancel') }}</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  {{ $t('chat.schedule') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlatformIcon from '@/Components/PlatformIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import CitySelect from '@/Components/CitySelect.vue';

const props = defineProps({
  conversation: { type: Object, required: true },
  messages: { type: Object, default: () => ({ data: [] }) },
  orders: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  templates: { type: Array, default: () => [] },
  scheduledMessages: { type: Array, default: () => [] },
});

const messagesContainer = ref(null);
let pollingInterval = null;

onMounted(() => {
  scrollToBottom();
  
  // Auto-refresh chat every 3 seconds
  pollingInterval = setInterval(() => {
    router.reload({ 
      only: ['messages', 'orders', 'stats'], 
      preserveScroll: true, 
      preserveState: true,
      onSuccess: () => {
        // Give the DOM a tiny fraction of a second to render the new message before scrolling
        nextTick(() => scrollToBottom());
      }
    });
  }, 3000);
});

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
});

const replyText = ref('');
const replyImage = ref(null);
const replyImagePreview = ref(null);
const showOrderPanel = ref(false);

function onReplyImageSelect(e) {
  const file = e.target.files[0];
  if (!file) return;
  replyImage.value = file;
  replyImagePreview.value = URL.createObjectURL(file);
}

function clearReplyImage() {
  replyImage.value = null;
  replyImagePreview.value = null;
}
const showDeleteModal = ref(false);
const showScheduleModal = ref(false);
const showTemplates = ref(false);
const filteredTemplates = ref([]);
const selectedTemplateIdx = ref(0);
const selectedProductId = ref('');
const scheduleForm = reactive({ body: '', scheduled_at: '' });
const itemName = ref('');
const itemQty = ref(1);
const itemPrice = ref(0);

const orderForm = useForm({
  customer_name: props.conversation.contact?.name || '',
  customer_phone: props.conversation.contact?.phone || '',
  shipping_city: props.conversation.contact?.city || '',
  shipping_neighborhood: '',
  shipping_street: '',
  shipping_notes: '',
  shipping_cost: 3.00,
  payment_method: 'cod',
  contact_id: props.conversation.contact?.id,
  conversation_id: props.conversation.id,
  items: [],
});

const itemErrors = computed(() => {
  return {
    name: orderForm.errors['items.0.name'] || orderForm.errors?.items?.[0]?.name,
    qty: orderForm.errors['items.0.qty'] || orderForm.errors?.items?.[0]?.qty,
    price: orderForm.errors['items.0.price'] || orderForm.errors?.items?.[0]?.price,
  };
});

const sortedMessages = computed(() => [...(props.messages?.data || [])].reverse());

function openManualOrder() {
  orderForm.reset();
  orderForm.customer_name = props.conversation.contact?.name || '';
  orderForm.customer_phone = props.conversation.contact?.phone || '';
  orderForm.shipping_city = props.conversation.contact?.city || '';
  orderForm.shipping_neighborhood = '';
  orderForm.shipping_street = '';
  orderForm.shipping_notes = '';
  orderForm.payment_method = 'cod';
  selectedProductId.value = '';
  itemName.value = '';
  itemQty.value = 1;
  itemPrice.value = '';
  showOrderPanel.value = true;
}

function openSmartOrder() {
  openManualOrder(); // reset first
  
  if (!props.messages || !props.messages.data.length) {
    showOrderPanel.value = true;
    return;
  }
  
  const latestExtractedMsg = props.messages.data.find(m => m.ai_extraction);
  if (latestExtractedMsg) {
    const ai = latestExtractedMsg.ai_extraction;
    if (ai.customer_name) orderForm.customer_name = ai.customer_name;
    if (ai.phone_number) orderForm.customer_phone = ai.phone_number;
    if (ai.city) orderForm.shipping_city = ai.city;
    if (ai.street_address) orderForm.shipping_street = ai.street_address;
    if (ai.product_intent && typeof ai.product_intent === 'string' && ai.product_intent !== 'Unknown Product') {
      itemName.value = ai.product_intent;
    }
    if (ai.quantity) itemQty.value = ai.quantity;
  }
  showOrderPanel.value = true;
}

function sendReply() {
  if (!replyText.value.trim() && !replyImage.value) return;

  const data = new FormData();
  if (replyText.value.trim()) data.append('body', replyText.value);
  if (replyImage.value) data.append('image', replyImage.value);

  router.post(`/inbox/${props.conversation.id}/reply`, data, {
    preserveScroll: true,
    onSuccess: () => { replyText.value = ''; clearReplyImage(); nextTick(() => scrollToBottom()); },
  });
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

function confirmDelete() {
  showDeleteModal.value = false;
  router.delete(`/inbox/${props.conversation.id}`);
}

function scrollToBottom() {
  if (messagesContainer.value) messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
}

function formatTime(d) {
  return d ? new Date(d).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) : '';
}

onMounted(() => nextTick(() => scrollToBottom()));

// ── Template slash commands ──────────────────────────
function onReplyInput() {
  const text = replyText.value;
  if (text.startsWith('/')) {
    const search = text.slice(1).toLowerCase();
    filteredTemplates.value = props.templates.filter(t =>
      t.shortcut.slice(1).includes(search) || t.name.toLowerCase().includes(search)
    );
    showTemplates.value = filteredTemplates.value.length > 0;
    selectedTemplateIdx.value = 0;
  } else {
    showTemplates.value = false;
  }
}

function selectTemplate() {
  if (showTemplates.value && filteredTemplates.value.length) {
    insertTemplate(filteredTemplates.value[selectedTemplateIdx.value]);
  }
}

function insertTemplate(tpl) {
  // Replace variables
  let body = tpl.body;
  body = body.replace('{customer_name}', props.conversation.contact?.name || '');
  body = body.replace('{order_number}', '');
  body = body.replace('{tracking_number}', '');
  replyText.value = body;
  showTemplates.value = false;
  // Track usage
  fetch(`/templates/${tpl.id}/use`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' } });
}

// ── Scheduled messages ──────────────────────────────
function scheduleMessage() {
  router.post('/scheduled-messages', {
    conversation_id: props.conversation.id,
    body: scheduleForm.body,
    scheduled_at: scheduleForm.scheduled_at,
  }, {
    onSuccess: () => {
      showScheduleModal.value = false;
      scheduleForm.body = '';
      scheduleForm.scheduled_at = '';
    },
  });
}
</script>
