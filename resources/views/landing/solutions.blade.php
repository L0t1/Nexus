<!DOCTYPE html>

<html class="dark" lang="{{ app()->getLocale() }}"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Nexus CRM - {{ app()->getLocale() === 'sq' ? 'Zgjidhjet' : 'Solutions' }}</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Tailwind Configuration -->
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-error-container": "#ffdad6",
                    "background": "#131315",
                    "inverse-on-surface": "#313032",
                    "surface-container-low": "#1c1b1d",
                    "secondary-fixed-dim": "#4edea3",
                    "error-container": "#93000a",
                    "on-primary-fixed-variant": "#004e5c",
                    "surface-container-lowest": "#0e0e10",
                    "on-secondary-fixed-variant": "#005236",
                    "on-secondary-fixed": "#002113",
                    "tertiary-fixed-dim": "#c6c6c7",
                    "outline-variant": "#3d494c",
                    "primary": "#4cd7f6",
                    "primary-fixed-dim": "#4cd7f6",
                    "on-background": "#e5e1e4",
                    "secondary": "#4edea3",
                    "tertiary-fixed": "#e2e2e2",
                    "on-tertiary-container": "#3a3c3c",
                    "tertiary": "#c6c6c7",
                    "outline": "#869397",
                    "on-tertiary-fixed": "#1a1c1c",
                    "surface-container-high": "#2a2a2c",
                    "secondary-fixed": "#6ffbbe",
                    "on-secondary-container": "#00311f",
                    "on-error": "#690005",
                    "on-surface-variant": "#bcc9cd",
                    "on-surface": "#e5e1e4",
                    "on-secondary": "#003824",
                    "on-primary": "#003640",
                    "surface-container-highest": "#353437",
                    "surface-container": "#201f22",
                    "surface": "#131315",
                    "inverse-primary": "#00687a",
                    "on-primary-fixed": "#001f26",
                    "surface-dim": "#131315",
                    "primary-fixed": "#acedff",
                    "primary-container": "#06b6d4",
                    "surface-tint": "#4cd7f6",
                    "inverse-surface": "#e5e1e4",
                    "surface-bright": "#39393b",
                    "secondary-container": "#00a572",
                    "on-tertiary-fixed-variant": "#454747",
                    "tertiary-container": "#a5a6a6",
                    "surface-variant": "#353437",
                    "on-tertiary": "#2f3131",
                    "error": "#ffb4ab",
                    "on-primary-container": "#00424f"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "gutter": "24px",
                    "2xl": "64px",
                    "md": "16px",
                    "lg": "24px",
                    "xs": "4px",
                    "xl": "40px",
                    "margin": "32px",
                    "unit": "4px",
                    "sm": "8px"
            },
            "fontFamily": {
                    "body-lg": ["Plus Jakarta Sans"],
                    "label-sm": ["Plus Jakarta Sans"],
                    "h3": ["Plus Jakarta Sans"],
                    "body-md": ["Plus Jakarta Sans"],
                    "h2": ["Plus Jakarta Sans"],
                    "h1": ["Plus Jakarta Sans"]
            },
            "fontSize": {
                    "body-lg": ["18px", {"lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400"}],
                    "label-sm": ["13px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "600"}],
                    "h3": ["24px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                    "body-md": ["16px", {"lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400"}],
                    "h2": ["36px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "h1": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
          }
        }
      }
    </script>
<style>
        .material-symbols-outlined {
          font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* Glassmorphism Utilities */
        .glass-panel {
            background-color: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: inset 1px 1px 0 0 rgba(255, 255, 255, 0.1);
        }
        .mesh-bg {
            background-image: 
                radial-gradient(at 0% 0%, rgba(6, 182, 212, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(78, 222, 163, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(6, 182, 212, 0.05) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-background text-on-surface antialiased min-h-screen flex flex-col font-body-md mesh-bg">
<!-- TopNavBar -->
@include("landing.partials.nav")
<!-- Main Content Canvas -->
<main class="flex-grow pt-[80px] sm:pt-[100px] pb-2xl flex flex-col gap-2xl px-4 sm:px-lg max-w-7xl mx-auto w-full">
<!-- Hero Section -->
<section class="flex flex-col items-center text-center py-xl mt-margin">
<div class="inline-flex items-center gap-sm bg-primary/10 border border-primary/20 rounded-full px-md py-xs mb-lg">
<span class="material-symbols-outlined text-primary text-[16px]">architecture</span>
<span class="text-primary font-label-sm text-label-sm">{{ app()->getLocale() === 'sq' ? 'Ndertuar Për Rritje' : 'Built To Grow' }}</span>
</div>
<h1 class="font-h1 text-h1 max-w-4xl mb-lg leading-tight">
@if(app()->getLocale() === 'sq')
                Zgjidhje që <br/>
<span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">rriten me biznesin tuaj.</span>
@else
                Solutions that <br/>
<span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">grow with your business.</span>
@endif
</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mb-xl">
                {{ app()->getLocale() === 'sq' ? 'Pavarësisht nëse sapo keni filluar ose keni një ekip të madh, Nexus përshtatet me nevojat tuaja. I thjeshtë për fillestarët, i fuqishëm për bizneset e mëdha.' : 'Whether you\'re just starting out or have a big team, Nexus fits your needs. Simple for beginners, powerful for big businesses.' }}
            </p>
</section>
<!-- Solutions Bento Grid -->
<section class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-lg auto-rows-[minmax(250px,auto)] sm:auto-rows-[minmax(300px,auto)]">
<!-- Solution 1: Social Commerce (Spans 4 columns) -->
<div class="md:col-span-4 glass-panel rounded-xl p-xl flex flex-col relative overflow-hidden group">
<div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
<div class="mb-auto z-10">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-outline-variant flex items-center justify-center mb-lg">
<span class="material-symbols-outlined text-secondary text-[24px]">storefront</span>
</div>
<h3 class="font-h3 text-h3 mb-sm">{{ app()->getLocale() === 'sq' ? 'Shitës në Rrjete Sociale' : 'Social Selling' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">
                        {{ app()->getLocale() === 'sq' ? 'Për shitës individualë që menaxhojnë porosi nga mesazhet. Inbox i bashkuar, përgjigje të shpejta, dhe katalog online.' : 'For individual sellers managing orders from messages. Unified inbox, quick replies, and online catalog.' }}
                    </p>
</div>
<ul class="mt-lg z-10 flex flex-col gap-sm border-t border-white/5 pt-md">
<li class="flex items-center gap-md font-body-md text-body-md text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]">check_circle</span> Single-view inbox
                    </li>
<li class="flex items-center gap-md font-body-md text-body-md text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]">check_circle</span> Automated quick replies
                    </li>
<li class="flex items-center gap-md font-body-md text-body-md text-on-surface">
<span class="material-symbols-outlined text-primary text-[16px]">check_circle</span> Public Storefront & WA Sync
                    </li>
</ul>
</div>
<!-- Solution 2: High-Volume Brands (Spans 8 columns, feature image) -->
<div class="md:col-span-8 glass-panel rounded-xl p-0 flex flex-col md:flex-row relative overflow-hidden border border-primary/20 shadow-[0_0_40px_0_rgba(6,182,212,0.05)]">
<div class="p-xl flex flex-col md:w-1/2 z-10 bg-gradient-to-r from-surface-container-lowest/80 to-transparent">
<div class="mb-auto">
<div class="w-12 h-12 rounded-lg bg-primary/10 border border-primary/30 flex items-center justify-center mb-lg">
<span class="material-symbols-outlined text-primary text-[24px]">query_stats</span>
</div>
<h3 class="font-h3 text-h3 mb-sm text-primary">{{ app()->getLocale() === 'sq' ? 'Biznese Me Volum Të Lartë' : 'High-Volume Businesses' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">
                            {{ app()->getLocale() === 'sq' ? 'Për biznese që procesojnë qindra porosi në ditë. Automatizoni proceset, parashikoni kërkesën, dhe shikoni analitikën e biznesit.' : 'For businesses processing hundreds of orders daily. Automate processes, predict demand, and view business analytics.' }}
                        </p>
</div>
<div class="mt-lg flex items-center gap-md">
<button class="bg-surface-container-high border border-outline-variant px-md py-sm rounded-lg font-label-sm text-label-sm hover:border-primary transition-colors flex items-center gap-sm">
                            {{ app()->getLocale() === 'sq' ? 'Shiko Veçoritë' : 'See Features' }} <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</button>
</div>
</div>
<!-- Abstract visual representation for High-Volume -->
<div class="md:w-1/2 min-h-[200px] relative bg-cover bg-center" data-alt="A highly complex, abstract 3D visualization of massive data pipelines glowing with cyan and emerald neon light within a stark, dark architectural matrix. The visual communicates immense scale, processing speed, and technical sophistication associated with high-volume enterprise e-commerce systems, styled with a premium, cinematic glassmorphism aesthetic." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDruvxqphZsIKg0TrS_zMavISReRbfL_z6pAJ2IeLojYzJY9ZaSE4ujvv4Vt2CrszayQiigcVMzFap-_9quw6-5g6kEGx7bfyl6j5d14LT3C5YgYDM6SP7L_cLt0NvbgUjlJCxoBzco365pL3EyS2ft8B7VHnT5X-dJAFp3qvaodoDMAJjX52_Xb1lbgzj5lI6cDggs9RUpaLSf1qenhDtGIV1nITnDpM2mIHGBey7XFW88kqOsSn8OVWaLqk1ImaC5JEeDEUMMcs0R');">
<div class="absolute inset-0 bg-gradient-to-l from-transparent to-surface-container-lowest/90"></div>
</div>
</div>
<!-- Solution 3: Multi-Brand Agencies (Full width, sophisticated layout) -->
<div class="md:col-span-12 glass-panel rounded-xl p-xl flex flex-col md:flex-row gap-xl items-center relative overflow-hidden">
<div class="absolute right-0 top-0 w-1/2 h-full bg-gradient-to-l from-secondary/5 to-transparent pointer-events-none"></div>
<div class="md:w-1/3 z-10">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-outline-variant flex items-center justify-center mb-lg">
<span class="material-symbols-outlined text-on-surface text-[24px]">account_tree</span>
</div>
<h2 class="font-h2 text-h2 mb-md">{{ app()->getLocale() === 'sq' ? 'Agjenci Me Shumë Brande' : 'Multi-Brand Agencies' }}</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-lg">
                        {{ app()->getLocale() === 'sq' ? 'Menaxhoni klientë të ndryshëm, të dhëna të ndara, dhe role të ndryshme për secilin, të gjitha nga një vend.' : 'Manage different clients, separated data, and different roles for each, all from one place.' }}
                    </p>
</div>
<div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-md z-10 w-full">
<!-- Feature Card -->
<div class="bg-surface-container border border-white/5 rounded-lg p-md flex flex-col gap-sm hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-tertiary text-[20px]">hub</span>
<h4 class="font-label-sm text-label-sm text-on-surface">{{ app()->getLocale() === 'sq' ? 'Ndarje e Të Dhënave' : 'Data Separation' }}</h4>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">{{ app()->getLocale() === 'sq' ? 'Të dhënat e secilit klient janë të ndara dhe të sigurta, ndërsa ju keni kontroll të plotë.' : 'Each client\'s data is separate and secure, while you have full control.' }}</p>
</div>
<!-- Feature Card -->
<div class="bg-surface-container border border-white/5 rounded-lg p-md flex flex-col gap-sm hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-tertiary text-[20px]">admin_panel_settings</span>
<h4 class="font-label-sm text-label-sm text-on-surface">{{ app()->getLocale() === 'sq' ? 'Rolet & Lejet' : 'Roles & Permissions' }}</h4>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">{{ app()->getLocale() === 'sq' ? 'Vendosni kush ka akses në çfarë — për ekipin tuaj dhe klientët e jashtëm.' : 'Control who has access to what — for your team and external clients.' }}</p>
</div>
<!-- Feature Card -->
<div class="bg-surface-container border border-white/5 rounded-lg p-md flex flex-col gap-sm hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-tertiary text-[20px]">insights</span>
<h4 class="font-label-sm text-label-sm text-on-surface">{{ app()->getLocale() === 'sq' ? 'Raporte Të Bashkuara' : 'Combined Reports' }}</h4>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">{{ app()->getLocale() === 'sq' ? 'Bashkoni analitikën nga të gjithë klientët në raporte të thjeshta.' : 'Combine analytics from all clients into simple reports.' }}</p>
</div>
<!-- Feature Card -->
<div class="bg-surface-container border border-white/5 rounded-lg p-md flex flex-col gap-sm hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-tertiary text-[20px]">palette</span>
<h4 class="font-label-sm text-label-sm text-on-surface">{{ app()->getLocale() === 'sq' ? 'Branding i Personalizuar' : 'Custom Branding' }}</h4>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">{{ app()->getLocale() === 'sq' ? 'Paraqisni portale me brandin tuaj për klientët.' : 'Show custom-branded portals for your clients.' }}</p>
</div>
</div>
</div>
</section>
<!-- Call to Action Banner -->
<section class="mt-xl glass-panel rounded-2xl p-2xl text-center relative overflow-hidden border border-primary/30">
<div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-transparent to-secondary/10"></div>
<div class="relative z-10 flex flex-col items-center">
<h2 class="font-h2 text-h2 mb-md text-on-surface">{{ app()->getLocale() === 'sq' ? 'Gati për të filluar?' : 'Ready to get started?' }}</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mb-xl">
                    {{ app()->getLocale() === 'sq' ? 'Filloni me Nexus në pak minuta, ose na kontaktoni për një plan të personalizuar.' : 'Start with Nexus in minutes, or contact us for a custom plan.' }}
                </p>
<div class="flex flex-col sm:flex-row gap-md">
<button class="bg-gradient-to-r from-primary-container to-secondary-container text-white px-xl py-md rounded-lg font-label-sm text-label-sm hover:opacity-90 transition-opacity shadow-[0_4px_24px_rgba(6,182,212,0.4)]">
                        {{ app()->getLocale() === 'sq' ? 'Fillo Provimin Falas' : 'Start Free Trial' }}
                    </button>
<button class="bg-surface-container-lowest border border-outline-variant text-on-surface px-xl py-md rounded-lg font-label-sm text-label-sm hover:border-primary transition-colors">
                        {{ app()->getLocale() === 'sq' ? 'Na Kontaktoni' : 'Contact Sales' }}
                    </button>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-surface-container-lowest text-secondary font-body-md text-body-md w-full border-t border-white/5 flex flex-col md:flex-row justify-between items-center px-lg py-xl gap-md mt-auto">
<!-- Copyright -->
<div class="text-on-surface-variant">
            ©2026 Nexus CRM. {{ app()->getLocale() === 'sq' ? 'Të gjitha të drejtat e rezervuara.' : 'All rights reserved.' }}
        </div>
<!-- Links -->
<div class="flex gap-lg flex-wrap justify-center">
<a class="text-on-surface-variant hover:text-secondary transition-colors hover:opacity-80" href="{{ route('privacy') }}">{{ app()->getLocale() === 'sq' ? 'Politika e Privatësisë' : 'Privacy Policy' }}</a>
<a class="text-on-surface-variant hover:text-secondary transition-colors hover:opacity-80" href="{{ route('terms') }}">{{ app()->getLocale() === 'sq' ? 'Kushtet e Shërbimit' : 'Terms of Service' }}</a>
</div>
</footer>
</body></html>