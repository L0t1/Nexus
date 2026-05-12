<nav class="bg-white/3 backdrop-blur-xl fixed top-0 w-full z-50 border-b border-white/10 shadow-[0_8px_32px_0_rgba(6,182,212,0.1)]">
    <div class="flex justify-between items-center px-4 sm:px-lg py-md max-w-7xl mx-auto">
        <!-- Brand -->
        <a href="/">
            <div class="text-h3 font-h3 font-bold text-primary">
                Nexus CRM
            </div>
        </a>
        
        <!-- Navigation Links (Desktop) -->
        <div class="hidden md:flex items-center gap-lg font-body-md text-body-md">
            <a class="{{ request()->is('features') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/features">{{ app()->getLocale() === 'sq' ? 'Veçoritë' : 'Features' }}</a>
            <a class="{{ request()->is('solutions') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/solutions">{{ app()->getLocale() === 'sq' ? 'Zgjidhjet' : 'Solutions' }}</a>
            <a class="{{ request()->is('pricing') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/pricing">{{ app()->getLocale() === 'sq' ? 'Çmimet' : 'Pricing' }}</a>
            <a class="{{ request()->is('resources') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/resources">{{ app()->getLocale() === 'sq' ? 'Burimet' : 'Resources' }}</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors duration-300" href="/login">{{ app()->getLocale() === 'sq' ? 'Hyr' : 'Login' }}</a>
        </div>
        
        <!-- Right Actions -->
        <div class="flex items-center gap-2 sm:gap-md">
            <!-- Language Toggle -->
            <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'sq' ? 'en' : 'sq']) }}" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors px-2 py-1 rounded-lg border border-white/10 hover:border-primary/30">
                {{ app()->getLocale() === 'sq' ? 'EN' : 'SQ' }}
            </a>
            <!-- CTA Button (Desktop) -->
            <a href="/register" class="hidden sm:block">
                <button class="font-label-sm text-label-sm bg-gradient-to-r from-primary to-secondary text-background px-lg py-sm rounded-full hover:opacity-90 transition-opacity shadow-[0_0_20px_rgba(76,215,246,0.3)]">
                    {{ app()->getLocale() === 'sq' ? 'Fillo Falas' : 'Start For Free' }}
                </button>
            </a>
            <!-- Hamburger (Mobile) -->
            <button onclick="document.getElementById('mobile-menu').classList.toggle('translate-x-full')" class="md:hidden p-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu Drawer -->
<div id="mobile-menu" class="fixed inset-0 z-[60] md:hidden translate-x-full transition-transform duration-300 ease-out">
    <!-- Overlay -->
    <div onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <!-- Drawer -->
    <div class="absolute right-0 top-0 h-full w-72 bg-surface-container-lowest/95 backdrop-blur-2xl border-l border-white/10 flex flex-col p-6">
        <!-- Close -->
        <div class="flex justify-between items-center mb-8">
            <span class="text-h3 font-h3 font-bold text-primary">Nexus</span>
            <button onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="p-2 rounded-lg text-on-surface-variant hover:text-primary">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Links -->
        <div class="flex flex-col gap-1">
            <a href="/features" onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="px-4 py-3 rounded-xl text-on-surface hover:bg-white/5 transition-colors font-body-md text-body-md {{ request()->is('features') ? 'text-primary bg-primary/5' : '' }}">{{ app()->getLocale() === 'sq' ? 'Veçoritë' : 'Features' }}</a>
            <a href="/solutions" onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="px-4 py-3 rounded-xl text-on-surface hover:bg-white/5 transition-colors font-body-md text-body-md {{ request()->is('solutions') ? 'text-primary bg-primary/5' : '' }}">{{ app()->getLocale() === 'sq' ? 'Zgjidhjet' : 'Solutions' }}</a>
            <a href="/pricing" onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="px-4 py-3 rounded-xl text-on-surface hover:bg-white/5 transition-colors font-body-md text-body-md {{ request()->is('pricing') ? 'text-primary bg-primary/5' : '' }}">{{ app()->getLocale() === 'sq' ? 'Çmimet' : 'Pricing' }}</a>
            <a href="/resources" onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="px-4 py-3 rounded-xl text-on-surface hover:bg-white/5 transition-colors font-body-md text-body-md {{ request()->is('resources') ? 'text-primary bg-primary/5' : '' }}">{{ app()->getLocale() === 'sq' ? 'Burimet' : 'Resources' }}</a>
            <a href="/login" onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')" class="px-4 py-3 rounded-xl text-on-surface hover:bg-white/5 transition-colors font-body-md text-body-md">{{ app()->getLocale() === 'sq' ? 'Hyr' : 'Login' }}</a>
        </div>
        <!-- Mobile CTA -->
        <div class="mt-auto">
            <a href="/register" class="block w-full text-center font-label-sm text-label-sm bg-gradient-to-r from-primary to-secondary text-background px-lg py-md rounded-xl hover:opacity-90 transition-opacity shadow-[0_0_20px_rgba(76,215,246,0.3)]">
                {{ app()->getLocale() === 'sq' ? 'Fillo Falas' : 'Start For Free' }}
            </a>
        </div>
    </div>
</div>
