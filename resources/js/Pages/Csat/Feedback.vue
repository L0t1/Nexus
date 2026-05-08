<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div v-if="$page.props.flash?.success" class="bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Thank You!</h2>
        <p class="text-gray-500">Your feedback has been submitted. We appreciate your time.</p>
      </div>
      <div v-else class="bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-6">
          <h2 class="text-xl font-bold text-gray-900">How was your experience?</h2>
          <p class="text-sm text-gray-500 mt-1">{{ survey.merchant?.business_name }} — Order {{ survey.order?.order_number }}</p>
        </div>
        <form @submit.prevent="submit">
          <div class="flex justify-center gap-3 mb-6">
            <button v-for="s in 5" :key="s" type="button" @click="form.rating = s" :class="['w-12 h-12 rounded-xl text-lg transition-all', form.rating >= s ? 'bg-amber-400 text-white shadow-lg scale-110' : 'bg-gray-100 text-gray-400 hover:bg-gray-200']">★</button>
          </div>
          <div class="text-center text-sm text-gray-500 mb-4">{{ ratingLabels[form.rating] || 'Tap a star to rate' }}</div>
          <textarea v-model="form.feedback" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400/30 resize-none" placeholder="Tell us more (optional)..."></textarea>
          <button type="submit" :disabled="!form.rating" class="w-full mt-4 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 disabled:opacity-30 transition-all">Submit Feedback</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({ survey: Object });
const form = reactive({ rating: 0, feedback: '' });
const ratingLabels = { 1: '😡 Very Poor', 2: '😕 Poor', 3: '😐 Okay', 4: '😊 Good', 5: '🤩 Excellent!' };
function submit() { router.post(`/feedback/${props.survey.token}`, form); }
</script>
