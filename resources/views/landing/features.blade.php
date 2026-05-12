<!DOCTYPE html>

<html class="dark" lang="{{ app()->getLocale() }}"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Nexus CRM - {{ app()->getLocale() === 'sq' ? 'Veçoritë' : 'Features' }}</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
        }
    </style>
<!-- Tailwind CSS & Config -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
                        "full": "9999px",
                        "2xl": "1rem",
                        "3xl": "1.5rem"
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
                        "body-lg": ["18px", { "lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400" }],
                        "label-sm": ["13px", { "lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "h3": ["24px", { "lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400" }],
                        "h2": ["36px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "h1": ["48px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-on-surface min-h-screen relative overflow-x-hidden selection:bg-primary-container selection:text-on-primary-container">
<!-- Global Ambient Mesh Gradient Background -->
<div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
<div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-primary/10 blur-[100px]"></div>
<div class="absolute bottom-[-20%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-secondary/5 blur-[120px]"></div>
<div class="absolute top-[40%] right-[10%] w-[30vw] h-[30vw] rounded-full bg-inverse-primary/10 blur-[80px]"></div>
</div>
<!-- TopNavBar (Generated from JSON) -->
@include("landing.partials.nav")
<!-- Main Content Canvas -->
<main class="pt-[80px] sm:pt-[100px] pb-2xl max-w-7xl mx-auto px-4 sm:px-lg flex flex-col gap-2xl">
<!-- Hero Header -->
<header class="text-center pt-xl pb-lg flex flex-col items-center gap-md max-w-3xl mx-auto">
<div class="inline-flex items-center gap-xs px-sm py-unit rounded-full bg-primary/10 border border-primary/20 font-label-sm text-label-sm text-primary uppercase tracking-widest mb-md">
<span class="material-symbols-outlined text-[16px]">bolt</span>
                {{ app()->getLocale() === 'sq' ? 'Çfarë Mund Të Bëni' : 'What You Can Do' }}
            </div>
<h1 class="font-h1 text-h1 text-on-surface leading-tight">
                {{ app()->getLocale() === 'sq' ? 'Kontrolloni Punën Tuaj me' : 'Manage Your Work with' }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-primary-fixed">{{ app()->getLocale() === 'sq' ? 'Lehtësi Të Plotë' : 'Total Ease' }}</span>.
            </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mt-sm">
                {{ app()->getLocale() === 'sq' ? 'Nexus CRM ktheu mesazhet kaotike nga shumë platforma në një sistem të organizuar që ju ndihmon të shisni më shumë. I duhur për çdo ekip.' : 'Nexus CRM turns messy messages from multiple platforms into an organized system that helps you sell more. Built for every team.' }}
            </p>
</header>
<!-- Section 1: AI Order Extraction -->
<section class="flex flex-col gap-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-primary text-[28px]" style="font-variation-settings: 'FILL' 1;">psychology</span>
<h2 class="font-h2 text-h2 text-on-surface">{{ app()->getLocale() === 'sq' ? 'AI Lexon Porositë' : 'AI Reads Orders' }}</h2>
</div>
<div class="relative w-full h-[250px] sm:h-[400px] rounded-2xl sm:rounded-3xl bg-white/[0.03] backdrop-blur-xl border border-white/10 overflow-hidden shadow-[0_8px_32px_0_rgba(0,0,0,0.4)] group">
<!-- Refractive Top Edge -->
<div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-white/20 via-primary/30 to-transparent"></div>
<!-- Graphic Visualization Placeholder -->
<div class="absolute inset-0 w-full h-full bg-cover bg-center opacity-70 group-hover:opacity-100 transition-opacity duration-700" data-alt="A highly detailed, futuristic data visualization UI displayed on a dark glassmorphic screen. The visualization shows a complex, glowing network graph with cyan and emerald data nodes streaming from left to right, representing raw direct messages transforming into structured order data. The aesthetic is ultra-modern, high-tech, and dark mode with intense glowing mesh gradients in the background. The lighting highlights the crisp, refractive edges of the data containers." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCgK1uWvuOiBYLOQqKFUpaqHBWB9sxBmsyUkE3ImqE5tWulT_xcuTtYqY-LWjHS_ZYPZT6I8UYerlD1KmrctRSQWRifSnPdHw2VHaOMdOWMNqjfUAgRU4fCc9R2YBa3o3G9wxfedClIYUVzpKcGEoj4bKJ70pmljGwx467IdNX_TiUCGWsSuXx2DT1h9jpHpu4damvDGLS2l4Y3xUhwK3nzDtvmDOam-NE_ON2-eOucfmI3knvJJj4RvzPmCnezg4N7_QDOOCDGH5vl');">
</div>
<!-- Overlay Content -->
<div class="absolute inset-0 bg-gradient-to-t from-background via-background/60 to-transparent flex flex-col justify-end p-xl">
<div class="max-w-2xl backdrop-blur-md bg-surface/40 p-lg rounded-2xl border border-white/5">
<h3 class="font-h3 text-h3 text-primary mb-xs">{{ app()->getLocale() === 'sq' ? 'Nga Mesazh në Porosi Automatikisht' : 'From Message to Order Automatically' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">
                            {{ app()->getLocale() === 'sq' ? 'Sistemi ynë skanon të gjitha mesazhet që merrni. Kur një klient dëshiron të blejë, AI nxjerr automatikisht produktet, sasinë, adresën, dhe krijon një porosi të gatshme për ju.' : 'Our system scans all your incoming messages. When a customer wants to buy, AI automatically pulls out the products, quantity, address, and creates a ready-made order for you.' }}
                        </p>
</div>
</div>
</div>
</section>
<!-- Section 2: Unified Inbox Bento Grid -->
<section class="flex flex-col gap-lg mt-xl">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary text-[28px]" style="font-variation-settings: 'FILL' 1;">all_inbox</span>
<h2 class="font-h2 text-h2 text-on-surface">{{ app()->getLocale() === 'sq' ? 'Një Inbox Për Të Gjitha Mesazhet' : 'One Inbox For All Messages' }}</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-lg">
<!-- Main Feed UI Mockup -->
<div class="md:col-span-8 bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-3xl p-lg flex flex-col gap-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]">
<div class="absolute top-0 right-0 w-[200px] h-[200px] bg-secondary/10 blur-[60px] rounded-full pointer-events-none"></div>
<div class="flex justify-between items-center pb-sm border-b border-white/5">
<span class="font-label-sm text-label-sm text-on-surface uppercase tracking-wider">{{ app()->getLocale() === 'sq' ? 'Mesazhet në Kohë Reale' : 'Live Messages' }}</span>
<div class="flex gap-sm">
<span class="w-3 h-3 rounded-full bg-error/80 shadow-[0_0_10px_rgba(255,180,171,0.5)]"></span>
<span class="w-3 h-3 rounded-full bg-secondary shadow-[0_0_10px_rgba(78,222,163,0.5)]"></span>
</div>
</div>
<!-- Mock Messages -->
<div class="flex flex-col gap-md flex-grow">
<!-- Message 1: External -->
<div class="flex gap-md w-[85%]">
<div class="w-10 h-10 rounded-full bg-surface-container-high border border-white/10 flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-on-surface-variant text-[20px]">person</span>
</div>
<div class="bg-surface-container-low border border-white/5 rounded-2xl rounded-tl-sm p-md backdrop-blur-md">
<p class="font-body-md text-body-md text-on-surface">{{ app()->getLocale() === 'sq' ? 'Përshëndetje, dua të përmirësoj planin tim muajin tjetër. A mund të planifikojmë një demonstrim?' : 'Hey, looking to upgrade my plan next month. Can we schedule a demo?' }}</p>
<span class="font-label-sm text-label-sm text-on-surface-variant mt-sm block opacity-50">Instagram • 2m {{ app()->getLocale() === 'sq' ? 'më parë' : 'ago' }}</span>
</div>
</div>
<!-- Message 2: Internal Note (More opaque glass) -->
<div class="flex gap-md w-[85%] ml-auto flex-row-reverse">
<div class="w-10 h-10 rounded-full bg-primary/20 border border-primary/30 flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-primary text-[20px]">badge</span>
</div>
<div class="bg-primary/10 border border-primary/20 rounded-2xl rounded-tr-sm p-md backdrop-blur-xl shadow-[0_4px_24px_rgba(6,182,212,0.15)] relative">
<div class="absolute -left-2 top-3 w-4 h-4 bg-primary/20 blur-sm rounded-full"></div>
<p class="font-body-md text-body-md text-primary-fixed">{{ app()->getLocale() === 'sq' ? 'E kuptova. AI ka përgatitur linkun e takimit dhe fletën e çmimeve. Gati për dërgim.' : 'Got it. AI has already drafted the meeting link and pricing sheet. Ready to dispatch.' }}</p>
<span class="font-label-sm text-label-sm text-primary mt-sm block opacity-70">{{ app()->getLocale() === 'sq' ? 'Brendshëm' : 'Internal' }} • 1m {{ app()->getLocale() === 'sq' ? 'më parë' : 'ago' }}</span>
</div>
</div>
</div>
</div>
<!-- Integration Pillars -->
<div class="md:col-span-4 flex flex-col gap-lg">
<!-- Integrations Card -->
<div class="flex-1 bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-3xl p-lg shadow-[inset_0_1px_0_rgba(255,255,255,0.1)] flex flex-col justify-center gap-md">
<h4 class="font-h3 text-h3 text-on-surface">{{ app()->getLocale() === 'sq' ? 'Të Gjitha Në Një Vend' : 'All In One Place' }}</h4>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Lidhni Instagram, Facebook dhe WhatsApp në një inbox. Asnjë mesazh nuk humbet.' : 'Connect Instagram, Facebook and WhatsApp in one inbox. No message gets lost.' }}</p>
<div class="flex gap-sm mt-sm">
<div class="w-12 h-12 rounded-xl bg-surface-container border border-white/10 flex items-center justify-center shadow-lg">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">photo_camera</span> <!-- Proxy for IG -->
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container border border-white/10 flex items-center justify-center shadow-lg">
<span class="material-symbols-outlined text-blue-400" style="font-variation-settings: 'FILL' 1;">chat</span> <!-- Proxy for FB/Messenger -->
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container border border-white/10 flex items-center justify-center shadow-lg">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">forum</span> <!-- Proxy for WA -->
</div>
</div>
</div>
<!-- Stats Card -->
<div class="bg-gradient-to-br from-surface-container-high to-surface border border-white/10 rounded-3xl p-lg flex flex-col justify-center">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">{{ app()->getLocale() === 'sq' ? 'Koha Mesatare e Përgjigjes' : 'Average Reply Time Drop' }}</span>
<div class="font-h1 text-h1 text-secondary mt-xs">-84%</div>
</div>
</div>
</div>
</section>

<!-- Section 3: CRM & Marketing Suite -->
<section class="flex flex-col gap-lg mt-xl mb-xl">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary text-[28px]" style="font-variation-settings: 'FILL' 1;">campaign</span>
<h2 class="font-h2 text-h2 text-on-surface">{{ app()->getLocale() === 'sq' ? 'Klientët & Marketingu' : 'Customers & Marketing' }}</h2>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-lg">
    <!-- Profiles -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary text-[20px]">contacts</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Profilet e Klientëve' : 'Customer Profiles' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Të gjitha të dhënat e klientit në një vend — blerjet, shpenzimet, historiku.' : 'All customer info in one place — purchases, spending, history.' }}</p>
        </div>
    </div>
    <!-- Tags -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-[20px]">sell</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Etiketim Automatik' : 'Auto-Tagging' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Vendosni rregulla që etiketojnë klientët automatikisht, p.sh. kur shpenzojnë mbi 500€.' : 'Set rules that tag customers automatically, like when they spend over €500.' }}</p>
        </div>
    </div>
    <!-- Broadcasts -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary text-[20px]">schedule</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Ndjekje Automatike' : 'Automatic Follow-ups' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Dërgoni mesazhe automatike pas blerjes ose kur klienti harron porrosinë.' : 'Send automatic messages after purchase or when a customer forgets their order.' }}</p>
        </div>
    </div>
    <!-- Templates -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-[20px]">quick_phrases</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Përgjigje të Shpejta' : 'Quick Replies' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Kurseni kohë me shabllone (p.sh. "Përshëndetje {emri}") që futen me komandën "/".' : 'Save time with templates (e.g. "Hi {name}") that you insert with the "/" command.' }}</p>
        </div>
    </div>
</div>
</section>

<!-- Section 4: Commerce & Analytics Suite -->
<section class="flex flex-col gap-lg mt-xl mb-xl">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-primary text-[28px]" style="font-variation-settings: 'FILL' 1;">insights</span>
<h2 class="font-h2 text-h2 text-on-surface">{{ app()->getLocale() === 'sq' ? 'Dyqani & Analitika' : 'Shop & Analytics' }}</h2>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-lg">
    <!-- Storefront -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-[20px]">storefront</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Dyqani Publik' : 'Public Storefront' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Një katalog produktesh me markën tuaj. Klientët shfletojnë dhe porosisin direkt përmes WhatsApp.' : 'A branded product catalog. Customers browse and order directly via WhatsApp.' }}</p>
        </div>
    </div>
    <!-- WA Sync -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary text-[20px]">sync</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Sinkronizimi WhatsApp' : 'WhatsApp Sync' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Mbani katalogun tuaj të WhatsApp Business në sinkronizim të plotë me inventarin Nexus.' : 'Keep your WhatsApp Business catalog in perfect sync with your Nexus inventory.' }}</p>
        </div>
    </div>
    <!-- Forecasting -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-[20px]">trending_up</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Parashikimi i Kërkesës' : 'Demand Forecasting' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Parashikime 14-ditore me AI, që ju njoftojnë para se produktet më të shitura të mbarojnë.' : 'AI-powered 14-day predictions, alerting you before top-selling products go out of stock.' }}</p>
        </div>
    </div>
    <!-- CSAT -->
    <div class="bg-surface-container-low border border-white/5 rounded-2xl p-lg flex flex-col gap-md">
        <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary text-[20px]">sentiment_satisfied</span>
        </div>
        <div>
            <h3 class="font-h3 text-[18px] text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Kënaqësia & Kthimet' : 'CSAT & Returns' }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Anketa automatike kënaqësie, vlerësimi NPS, dhe menaxhim i plotë për kthime dhe rimbursime.' : 'Automated satisfaction surveys, NPS scoring, and a full lifecycle for returns and refunds.' }}</p>
        </div>
    </div>
</div>
</section>
</main>
<!-- Footer (Generated from JSON) -->
<footer class="w-full bg-surface-container-lowest border-t border-white/5 mt-2xl">
<div class="flex flex-col md:flex-row justify-between items-center px-4 sm:px-lg py-xl gap-md max-w-7xl mx-auto">
<!-- Brand & Copyright -->
<div class="flex flex-col gap-sm">
<div class="text-h3 font-h3 font-bold text-primary">
                    Nexus CRM
                </div>
<div class="font-body-md text-body-md text-on-surface-variant">
                    ©2026 Nexus CRM. {{ app()->getLocale() === 'sq' ? 'Të gjitha të drejtat e rezervuara.' : 'All rights reserved.' }}
                </div>
</div>
<!-- Links -->
<div class="flex flex-wrap gap-md font-body-md text-body-md">
<a class="text-on-surface-variant hover:text-secondary transition-colors hover:opacity-80 transition-opacity" href="{{ route('privacy') }}">{{ app()->getLocale() === 'sq' ? 'Politika e Privatësisë' : 'Privacy Policy' }}</a>
<a class="text-on-surface-variant hover:text-secondary transition-colors hover:opacity-80 transition-opacity" href="{{ route('terms') }}">{{ app()->getLocale() === 'sq' ? 'Kushtet e Shërbimit' : 'Terms of Service' }}</a>
</div>
</div>
</footer>
</body></html>