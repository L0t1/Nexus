<template>
  <div class="min-h-screen flex items-center justify-center p-6 relative z-10">
    <div class="max-w-lg w-full animate-slide-up">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#1877F2]/20 mb-4">
          <svg class="w-7 h-7 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-nexus-100">Select Your Facebook Page</h1>
        <p class="text-nexus-400 text-sm mt-2">We found multiple Pages on your account. Choose which one to connect to NEXUS.</p>
      </div>

      <div class="glass rounded-2xl overflow-hidden">
        <div class="divide-y divide-white/[0.04]">
          <button
            v-for="page in pages"
            :key="page.id"
            @click="select(page.id)"
            :disabled="form.processing && form.page_id === page.id"
            class="w-full flex items-center gap-4 px-6 py-4 hover:bg-white/[0.03] transition-colors text-left"
          >
            <div class="w-10 h-10 rounded-xl bg-[#1877F2]/15 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-semibold text-nexus-100 truncate">{{ page.name }}</div>
              <div class="text-xs text-nexus-500 font-mono mt-0.5">ID: {{ page.id }}</div>
              <div v-if="page.instagram_business_account" class="text-xs text-purple-400 mt-0.5">✓ Instagram Business connected</div>
            </div>
            <div v-if="form.processing && form.page_id === page.id" class="w-5 h-5 rounded-full border-2 border-nexus-500 border-t-transparent animate-spin flex-shrink-0" />
            <svg v-else class="w-4 h-4 text-nexus-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <p class="text-center text-xs text-nexus-600 mt-4">
        Want a different account?
        <a href="/auth/meta" class="text-nexus-400 hover:text-nexus-300 underline">Reconnect with Facebook</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ pages: Array });

const form = useForm({ page_id: '' });

function select(pageId) {
  form.page_id = pageId;
  form.post('/auth/meta/select-page');
}
</script>
