<nav class="bg-white/3 backdrop-blur-xl fixed top-0 w-full z-50 border-b border-white/10 shadow-[0_8px_32px_0_rgba(6,182,212,0.1)]">
    <div class="flex justify-between items-center px-lg py-md max-w-7xl mx-auto">
        <!-- Brand -->
        <a href="/">
            <div class="text-h3 font-h3 font-bold text-primary">
                Nexus CRM
            </div>
        </a>
        
        <!-- Navigation Links (Web) -->
        <div class="hidden md:flex items-center gap-lg font-body-md text-body-md">
            <a class="{{ request()->is('features') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/features">Features</a>
            <a class="{{ request()->is('solutions') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/solutions">Solutions</a>
            <a class="{{ request()->is('pricing') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/pricing">Pricing</a>
            <a class="{{ request()->is('resources') ? 'text-primary font-bold border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}" href="/resources">Resources</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors duration-300" href="/login">Login</a>
        </div>
        
        <!-- Trailing Action -->
        <div class="flex items-center gap-md">
            <a href="/register">
                <button class="font-label-sm text-label-sm bg-gradient-to-r from-primary to-secondary text-background px-lg py-sm rounded-full hover:opacity-90 transition-opacity shadow-[0_0_20px_rgba(76,215,246,0.3)]">
                    Start For Free
                </button>
            </a>
        </div>
    </div>
</nav>
