<template>
  <div class="min-h-screen flex items-center justify-center p-6 relative z-10">
    <div class="max-w-lg w-full animate-slide-up">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-nexus-500 to-nexus-700 glow-strong mb-5">
          <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gradient mb-2">Welcome to NEXUS</h1>
        <p class="text-nexus-400 text-base">Let's set up your business to start receiving orders</p>
      </div>

      <div class="glass rounded-2xl p-8 glow">
        <div class="space-y-6">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-nexus-500/15 flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-nexus-400 font-bold">1</span>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-nexus-200">Connect your channels</h3>
              <p class="text-xs text-nexus-500 mt-1">Link Instagram, WhatsApp, or Facebook to start receiving messages.</p>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-nexus-500/15 flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-nexus-400 font-bold">2</span>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-nexus-200">Set up your courier</h3>
              <p class="text-xs text-nexus-500 mt-1">Add your courier API keys to automate shipping labels.</p>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-nexus-500/15 flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-nexus-400 font-bold">3</span>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-nexus-200">Start selling</h3>
              <p class="text-xs text-nexus-500 mt-1">AI will extract orders from your chats automatically.</p>
            </div>
          </div>
        </div>

        <button @click="goToDashboard" class="w-full mt-8 py-3 rounded-lg bg-gradient-to-r from-nexus-600 to-nexus-500 hover:from-nexus-500 hover:to-nexus-400 text-white font-semibold text-sm transition-all hover:shadow-lg hover:shadow-nexus-500/25">
          Get Started →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

function goToDashboard() {
  router.visit('/');
}
</script>
