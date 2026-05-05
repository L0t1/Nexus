<template>
  <div class="min-h-screen bg-[var(--color-surface-base)] text-nexus-300 font-sans selection:bg-accent-cyan/30 overflow-x-hidden mesh-gradient">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300 border-b border-white/[0.04] bg-[var(--color-surface-base)]/50 backdrop-blur-xl">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent-cyan to-accent-emerald flex items-center justify-center shadow-[0_0_15px_rgba(34,211,238,0.5)]">
            <svg class="w-5 h-5 text-[var(--color-surface-base)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          </div>
          <span class="text-xl font-bold tracking-tight text-white font-mono">Nexus</span>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">
          <a href="#features" class="text-nexus-400 hover:text-white transition-colors">Features</a>
          <a href="#how-it-works" class="text-nexus-400 hover:text-white transition-colors">How it Works</a>
          <a href="#pricing" class="text-nexus-400 hover:text-white transition-colors">Pricing</a>
        </div>
        <div class="flex items-center gap-4">
          <Link href="/login" class="text-sm font-medium text-nexus-200 hover:text-white transition-colors">Log in</Link>
          <Link href="/register" class="btn-primary">Get Started</Link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-40 pb-20 px-6 max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16 relative">
      <div class="flex-1 text-center lg:text-left z-10 animate-fade-in">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-accent-cyan/10 border border-accent-cyan/20 text-accent-cyan text-xs font-semibold uppercase tracking-wider mb-8">
          <span class="w-2 h-2 rounded-full bg-accent-cyan animate-pulse"></span>
          Nexus CRM 2.0 is live
        </div>
        <h1 class="text-5xl md:text-7xl font-bold text-white tracking-tight leading-[1.1] mb-6">
          Turn your DMs into an <br class="hidden lg:block"/>
          <span class="text-gradient">automated sales machine.</span>
        </h1>
        <p class="text-lg md:text-xl text-nexus-300 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
          Stop losing track of orders. Nexus consolidates Instagram, WhatsApp, and Facebook into one smart inbox with an AI that extracts orders for you.
        </p>
        <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
          <Link href="/register" class="btn-primary w-full sm:w-auto text-lg py-4 px-8">Start 30-Day Free Trial</Link>
          <a href="#demo" class="btn-secondary w-full sm:w-auto text-lg py-4 px-8">View Demo</a>
        </div>
      </div>

      <!-- Hero Visual -->
      <div class="flex-1 w-full relative z-10 perspective-1000">
        <div class="relative w-full aspect-square md:aspect-video lg:aspect-square transform rotate-y-[-10deg] rotate-x-[5deg] hover:rotate-y-0 hover:rotate-x-0 transition-transform duration-700 ease-out">
          <!-- Glass Card 1 (Chat) -->
          <div class="absolute top-10 left-0 w-3/4 glass rounded-2xl p-5 z-20 transform -translate-x-4 translate-y-4">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-accent-pink to-accent-amber"></div>
              <div>
                <div class="text-sm font-semibold text-white">@sarah_styles</div>
                <div class="text-[10px] uppercase tracking-wider text-nexus-500">Instagram DM</div>
              </div>
            </div>
            <div class="bubble-inbound p-3 text-sm text-nexus-100 mb-3 max-w-[85%]">
              Hey! I want to buy the black leather jacket in size M. My address is rr. Agim Ramadani, Prishtina. Phone 044123456.
            </div>
            <div class="flex justify-end">
              <div class="bubble-outbound text-white p-3 text-sm max-w-[85%]">
                Perfect! Order confirmed. 📦
              </div>
            </div>
          </div>

          <!-- Glass Card 2 (Order Extraction) -->
          <div class="absolute bottom-10 right-0 w-3/4 glass-glow rounded-2xl p-6 z-30 transform translate-x-4 -translate-y-4">
            <div class="flex items-center justify-between mb-5 border-b border-white/[0.04] pb-4">
              <div class="text-xs font-bold uppercase tracking-widest text-accent-cyan flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                AI Extracted Order
              </div>
              <div class="badge badge-cyan">NX-84920</div>
            </div>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-nexus-500 text-xs uppercase tracking-wider">Customer</span>
                <span class="text-nexus-100 text-sm font-medium">Sarah</span>
              </div>
              <div class="flex justify-between">
                <span class="text-nexus-500 text-xs uppercase tracking-wider">Product</span>
                <span class="text-nexus-100 text-sm font-medium">Black Leather Jacket (M)</span>
              </div>
              <div class="flex justify-between">
                <span class="text-nexus-500 text-xs uppercase tracking-wider">Address</span>
                <span class="text-nexus-100 text-sm font-medium">rr. Agim Ramadani, Prishtina</span>
              </div>
              <div class="flex justify-between">
                <span class="text-nexus-500 text-xs uppercase tracking-wider">Phone</span>
                <span class="text-nexus-100 text-sm font-medium">044123456</span>
              </div>
            </div>
            <button class="w-full mt-6 btn-primary">Confirm & Dispatch</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof Marquee -->
    <section class="py-10 border-y border-white/[0.04] bg-white/[0.01]">
      <div class="max-w-7xl mx-auto px-6 overflow-hidden">
        <p class="text-center text-xs font-bold uppercase tracking-widest text-nexus-600 mb-6">Seamlessly integrated with</p>
        <div class="flex flex-wrap items-center justify-center gap-10 md:gap-20 opacity-75">
          <div class="flex items-center gap-2 font-bold text-xl text-nexus-200"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z"/></svg> Instagram</div>
          <div class="flex items-center gap-2 font-bold text-xl text-nexus-200"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg> WhatsApp</div>
          <div class="flex items-center gap-2 font-bold text-xl text-nexus-200"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg> Facebook</div>
          <div class="flex items-center gap-2 font-bold text-xl text-nexus-400">Couriers</div>
        </div>
      </div>
    </section>

    <!-- Bento Grid Features -->
    <section id="features" class="py-24 px-6 max-w-7xl mx-auto stagger-children">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Everything you need to scale</h2>
        <p class="text-nexus-300 text-lg max-w-2xl mx-auto">Designed specifically for modern e-commerce brands selling through social media.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[320px]">
        <!-- Feature 1: AI Extraction -->
        <div class="md:col-span-2 glass-strong rounded-3xl p-8 relative overflow-hidden group">
          <div class="absolute top-0 right-0 w-[120%] h-[120%] bg-gradient-to-bl from-accent-cyan/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <h3 class="text-2xl font-bold text-white mb-3">AI-Powered Order Extraction</h3>
          <p class="text-nexus-300 max-w-md relative z-10">Nexus automatically reads incoming messages, detects product intent, parses addresses, and creates draft orders instantly.</p>
          
          <div class="absolute bottom-[-30px] right-8 w-80 h-48 glass rounded-t-xl p-5 border border-accent-cyan/20 shadow-2xl transform rotate-[-2deg] group-hover:rotate-0 transition-transform duration-500">
            <div class="text-[10px] text-accent-cyan mb-3 uppercase tracking-widest font-bold">Extraction complete</div>
            <div class="space-y-3">
              <div class="h-3 w-3/4 bg-white/10 rounded-full"></div>
              <div class="h-3 w-1/2 bg-white/10 rounded-full"></div>
              <div class="h-3 w-5/6 bg-white/10 rounded-full"></div>
            </div>
          </div>
        </div>

        <!-- Feature 2: Omni Inbox -->
        <div class="glass rounded-3xl p-8 relative overflow-hidden flex flex-col justify-between group">
          <div>
            <h3 class="text-xl font-bold text-white mb-2">Omni-Channel Inbox</h3>
            <p class="text-nexus-400 text-sm">One unified screen for all your Instagram, Facebook, and WhatsApp conversations.</p>
          </div>
          <div class="flex gap-2 opacity-60 group-hover:opacity-100 transition-opacity duration-300">
            <div class="w-10 h-10 rounded-xl glass flex items-center justify-center text-xs font-bold text-white">IG</div>
            <div class="w-10 h-10 rounded-xl glass flex items-center justify-center text-xs font-bold text-white">WA</div>
            <div class="w-10 h-10 rounded-xl glass flex items-center justify-center text-xs font-bold text-white">FB</div>
          </div>
        </div>

        <!-- Feature 3: Team Management -->
        <div class="glass rounded-3xl p-8 relative overflow-hidden group">
          <h3 class="text-xl font-bold text-white mb-2">Team Collaboration</h3>
          <p class="text-nexus-400 text-sm">Add agents, assign conversations, and keep your admins in full control of data.</p>
          <div class="absolute bottom-8 right-8 flex -space-x-3">
            <div class="w-12 h-12 rounded-full border-2 border-[var(--color-surface-base)] bg-gradient-to-br from-accent-emerald to-accent-cyan"></div>
            <div class="w-12 h-12 rounded-full border-2 border-[var(--color-surface-base)] bg-gradient-to-br from-accent-cyan to-accent-violet"></div>
            <div class="w-12 h-12 rounded-full border-2 border-[var(--color-surface-base)] bg-gradient-to-br from-accent-violet to-accent-pink"></div>
          </div>
        </div>

        <!-- Feature 4: Logistics -->
        <div class="md:col-span-2 glass-glow rounded-3xl p-8 relative overflow-hidden group">
          <h3 class="text-2xl font-bold text-white mb-3">Seamless Logistics</h3>
          <p class="text-nexus-300 max-w-md">Track fulfillments from draft to dispatched to delivered. Generate waybills and update customers automatically.</p>
          <div class="mt-10 flex items-center gap-3">
            <div class="badge badge-amber">Processing</div>
            <div class="w-8 h-px bg-white/20"></div>
            <div class="badge badge-cyan">Dispatched</div>
            <div class="w-8 h-px bg-white/20"></div>
            <div class="badge badge-emerald">Delivered</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 px-6 max-w-7xl mx-auto relative">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Simple, transparent pricing</h2>
        <p class="text-nexus-300 text-lg">Start with a 30-day free Pro trial. No credit card required.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center stagger-children">
        <!-- Starter -->
        <div class="glass rounded-3xl p-8 stat-card">
          <h3 class="text-lg font-semibold text-white mb-2">Starter</h3>
          <div class="flex items-baseline gap-1 mb-6">
            <span class="text-4xl font-bold text-white">€19</span>
            <span class="text-nexus-500 text-sm">/mo</span>
          </div>
          <ul class="space-y-4 mb-8 text-sm text-nexus-300">
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> 1 Connected Channel</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Up to 500 orders/mo</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Basic Analytics</li>
          </ul>
          <Link href="/register" class="btn-secondary w-full block text-center">Start Starter</Link>
        </div>

        <!-- Pro (Highlighted) -->
        <div class="glass-glow border border-accent-cyan/50 rounded-3xl p-8 transform md:-translate-y-4 relative stat-card stat-card-cyan">
          <div class="absolute top-6 left-1/2 -translate-x-1/2 -translate-y-1/2 px-4 py-1.5 bg-gradient-to-r from-accent-cyan to-accent-emerald text-[var(--color-surface-base)] text-[10px] font-bold uppercase tracking-widest rounded-full shadow-[0_0_15px_rgba(34,211,238,0.5)]">Most Popular</div>
          <h3 class="text-lg font-semibold text-white mb-2">Pro</h3>
          <div class="flex items-baseline gap-1 mb-6">
            <span class="text-5xl font-bold text-white">€49</span>
            <span class="text-nexus-400 text-sm">/mo</span>
          </div>
          <ul class="space-y-4 mb-8 text-sm text-nexus-200">
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> All Channels (IG, FB, WA)</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Unlimited Orders</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Product Catalog</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Advanced Analytics</li>
          </ul>
          <Link href="/register" class="btn-primary w-full block text-center text-base py-3 shadow-[0_0_20px_rgba(99,102,241,0.4)]">Start 30-Day Free Trial</Link>
        </div>

        <!-- Ultimate -->
        <div class="glass rounded-3xl p-8 stat-card">
          <h3 class="text-lg font-semibold text-white mb-2">Ultimate</h3>
          <div class="flex items-baseline gap-1 mb-6">
            <span class="text-4xl font-bold text-white">€99</span>
            <span class="text-nexus-500 text-sm">/mo</span>
          </div>
          <ul class="space-y-4 mb-8 text-sm text-nexus-300">
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Everything in Pro</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> AI Order Extraction</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Priority Inbox Routing</li>
            <li class="flex items-center gap-3"><svg class="w-4 h-4 text-accent-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Mass Broadcasts</li>
          </ul>
          <Link href="/register" class="btn-secondary w-full block text-center">Start Ultimate</Link>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 border-t border-white/[0.04]">
      <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-6xl font-bold text-white mb-6 tracking-tight">Stop losing sales in the DMs.</h2>
        <p class="text-xl text-nexus-300 mb-10">Join thousands of modern brands scaling their operations with Nexus.</p>
        <Link href="/register" class="btn-primary text-lg px-12 py-5 shadow-[0_0_30px_rgba(99,102,241,0.5)]">
          Get Started Now
        </Link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 border-t border-white/[0.04] text-center text-nexus-500 text-sm bg-[var(--color-surface-raised)]">
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2 font-mono font-bold text-white">
          <svg class="w-4 h-4 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          Nexus CRM
        </div>
        <div>&copy; {{ new Date().getFullYear() }} Nexus Technologies. All rights reserved.</div>
        <div class="flex gap-6">
          <a href="#" class="hover:text-nexus-300 transition-colors">Privacy</a>
          <a href="#" class="hover:text-nexus-300 transition-colors">Terms</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style>
.perspective-1000 {
  perspective: 1000px;
}
</style>
