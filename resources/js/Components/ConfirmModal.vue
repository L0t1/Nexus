<template>
  <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
      <div class="relative bg-nexus-900 border border-white/10 p-6 rounded-2xl shadow-2xl max-w-sm w-full">
        <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 mb-4 mx-auto">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-nexus-100 text-center mb-2">{{ title }}</h3>
        <p class="text-sm text-nexus-400 text-center mb-6">{{ message }}</p>
        <div class="flex gap-3">
          <button @click="$emit('close')" class="flex-1 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-semibold transition-all">{{ $t('common.cancel') }}</button>
          <button @click="$emit('confirm')" class="flex-1 py-2.5 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white text-sm font-semibold transition-all">{{ $t('common.confirm') }}</button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: { type: String, default: 'Delete Item' },
  message: { type: String, default: 'Are you sure you want to delete this item? This action cannot be undone.' }
});
defineEmits(['close', 'confirm']);
</script>
