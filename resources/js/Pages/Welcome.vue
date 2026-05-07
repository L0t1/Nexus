<template>
  <div class="bg-background text-on-surface font-body-md min-h-screen mesh-bg selection:bg-primary-container selection:text-white">

    <nav class="fixed top-0 w-full z-50 backdrop-blur-xl bg-white/3 dark:bg-surface-container/30 border-b border-white/10 shadow-sm">
      <div class="flex justify-between items-center px-margin py-md max-w-7xl mx-auto">
        <div class="font-h3 text-h3 font-bold text-on-surface tracking-tight">Nexus CRM</div>
        <div class="hidden md:flex gap-lg items-center">
          <a class="text-on-surface-variant hover:text-on-surface transition-colors font-body-md text-body-md cursor-pointer" href="#features">Features</a>
          <a class="text-on-surface-variant hover:text-on-surface transition-colors font-body-md text-body-md cursor-pointer" href="#solutions">Solutions</a>
          <a class="text-on-surface-variant hover:text-on-surface transition-colors font-body-md text-body-md cursor-pointer" href="#pricing">Pricing</a>
          <Link class="text-on-surface-variant hover:text-on-surface transition-colors font-body-md text-body-md" href="/login">Login</Link>
        </div>
        <div>
          <Link class="btn-gradient text-white px-md py-sm rounded-lg font-label-sm text-label-sm uppercase tracking-wider inline-flex items-center gap-xs" href="/register">
            Start Trial
          </Link>
        </div>
      </div>
    </nav>

    <main class="pt-[100px] pb-margin">
      <section class="min-h-[819px] flex items-center px-margin max-w-7xl mx-auto relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2xl items-center relative z-10">
          <div class="space-y-lg">
            <div class="inline-block px-sm py-xs rounded-full border border-primary-container/30 bg-primary-container/10 text-primary font-label-sm text-label-sm mb-sm">
              <span class="material-symbols-outlined text-[16px] align-middle mr-xs">auto_awesome</span> Elite AI Command Center
            </div>
            <h1 class="font-h1 text-[48px] leading-[1.2] tracking-[-0.02em] font-bold text-on-surface">
              Turn Instagram DMs into <br/>
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#06b6d4] to-[#10b981]">Automated Sales</span>
            </h1>
            <p class="font-body-lg text-[18px] leading-[1.6] text-on-surface-variant max-w-xl">
              Nexus AI extracts order details and tracks logistics across IG, FB, and WhatsApp. Centralize your high-growth enterprise operations in one sleek, cinematic interface.
            </p>
            <div class="flex flex-col sm:flex-row gap-md pt-md">
              <Link class="btn-gradient text-white px-lg py-md rounded-lg font-label-sm text-[13px] font-bold uppercase tracking-wider inline-flex justify-center items-center gap-sm" href="/register">
                  Start Your 30-Day Free Pro Trial
                  <span class="material-symbols-outlined text-[18px] ml-2">arrow_forward</span>
              </Link>
              <a class="btn-glass text-on-surface px-lg py-md rounded-lg font-label-sm text-[13px] font-bold uppercase tracking-wider inline-flex justify-center items-center gap-sm cursor-pointer" href="#how-it-works">
                  See How It Works
              </a>
            </div>
          </div>
          <div class="relative hidden lg:block">
            <div class="glass-layer-2 rounded-xl p-md aspect-square relative transform rotate-y-[-10deg] rotate-x-[5deg] transition-transform hover:rotate-0 duration-700">
              <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop" class="rounded-lg object-cover w-full h-full opacity-60 mix-blend-overlay" alt="Abstract interface"/>
              
              <div class="glass-card p-md rounded-lg absolute -left-xl top-1/4 w-64 animate-bounce" style="animation-duration: 3s">
                  <div class="text-primary font-label-sm text-[13px] font-bold mb-xs">New Order Detected</div>
                  <div class="text-on-surface text-[16px] mb-xs">Sarah placed an order.</div>
                  <div class="text-on-surface-variant font-label-sm text-[13px]">Total: €120.00</div>
              </div>
              
              <div class="glass-card p-md rounded-lg absolute -right-lg bottom-1/3 w-56 animate-bounce" style="animation-duration: 4s; animation-delay: 1s">
                  <div class="text-secondary font-label-sm text-[13px] font-bold mb-xs">Logistics Synced</div>
                  <div class="text-on-surface-variant text-[16px]">Tracking #NX-84920</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-primary/10 rounded-full blur-[120px] pointer-events-none transform translate-x-1/3 -translate-y-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-secondary/10 rounded-full blur-[100px] pointer-events-none transform -translate-x-1/4 translate-y-1/4"></div>
      </section>

      <section class="py-2xl border-y border-white/5 bg-surface-container-lowest/50 backdrop-blur-sm relative z-20">
        <div class="max-w-7xl mx-auto px-margin text-center">
            <p class="font-label-sm text-[13px] font-bold text-on-surface-variant mb-xl uppercase tracking-widest">Powering High-Volume Commerce Across Elite Platforms</p>
            <div class="flex flex-wrap justify-center items-center gap-xl opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="text-h3 font-bold text-on-surface flex items-center gap-sm"><span class="material-symbols-outlined text-[32px]">photo_camera</span> Instagram</div>
                <div class="text-h3 font-bold text-on-surface flex items-center gap-sm"><span class="material-symbols-outlined text-[32px]">chat</span> WhatsApp</div>
                <div class="text-h3 font-bold text-on-surface flex items-center gap-sm"><span class="material-symbols-outlined text-[32px]">store</span> Facebook</div>
                <div class="text-h3 font-bold text-on-surface flex items-center gap-sm"><span class="material-symbols-outlined text-[32px]">local_shipping</span> Elite Logistics</div>
            </div>
        </div>
      </section>

    </main>
    <footer class="border-t border-white/5 py-lg text-center text-on-surface-variant font-label-sm text-[13px]">
      <div class="max-w-7xl mx-auto px-margin">
        <p>&copy; 2026 Nexus CRM. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<style>
.glass-card { background-color: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); position: relative; overflow: hidden; }
.glass-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%); }
.glass-layer-2 { background-color: rgba(255, 255, 255, 0.06); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 20px 40px -10px rgba(6, 182, 212, 0.1); }
.mesh-bg { background-color: #09090b; background-image: radial-gradient(at 80% 0%, hsla(189,100%,40%,0.15) 0px, transparent 50%), radial-gradient(at 0% 50%, hsla(152,100%,40%,0.15) 0px, transparent 50%); }
.btn-gradient { background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%); box-shadow: 0 4px 15px -3px rgba(6, 182, 212, 0.4); border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.3s ease; }
.btn-gradient:hover { box-shadow: 0 8px 25px -5px rgba(6, 182, 212, 0.6); filter: brightness(1.1); }
.btn-glass { background-color: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
.btn-glass:hover { background-color: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.3); }

.material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-weight: normal; font-style: normal; font-size: 24px; line-height: 1; letter-spacing: normal; text-transform: none; display: inline-block; white-space: nowrap; word-wrap: normal; direction: ltr; font-feature-settings: 'liga'; -webkit-font-feature-settings: 'liga'; -webkit-font-smoothing: antialiased; }
.text-on-surface { color: #e5e1e4; }
.text-on-surface-variant { color: #bcc9cd; }
.bg-surface-container { background-color: #201f22; }
.bg-surface-container-lowest { background-color: #0e0e10; }
</style>
