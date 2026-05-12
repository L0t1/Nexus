<!DOCTYPE html>

<html class="dark" lang="{{ app()->getLocale() }}"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Nexus CRM - {{ app()->getLocale() === 'sq' ? 'Menaxho Biznesin Tënd nga Mesazhet' : 'Manage Your Business From Messages' }}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-dim": "#131315",
                        "primary-fixed": "#acedff",
                        "on-tertiary-fixed-variant": "#454747",
                        "tertiary-fixed": "#e2e2e2",
                        "error": "#ffb4ab",
                        "inverse-on-surface": "#313032",
                        "surface-bright": "#39393b",
                        "primary-container": "#06b6d4",
                        "background": "#131315",
                        "on-secondary-fixed-variant": "#005236",
                        "surface-container-low": "#1c1b1d",
                        "surface-variant": "#353437",
                        "on-primary-fixed-variant": "#004e5c",
                        "on-secondary-fixed": "#002113",
                        "tertiary-container": "#a5a6a6",
                        "on-primary": "#003640",
                        "on-tertiary-container": "#3a3c3c",
                        "surface-container-lowest": "#0e0e10",
                        "on-secondary-container": "#00311f",
                        "secondary": "#4edea3",
                        "on-error": "#690005",
                        "surface-container-highest": "#353437",
                        "on-primary-container": "#00424f",
                        "surface-tint": "#4cd7f6",
                        "on-primary-fixed": "#001f26",
                        "surface": "#131315",
                        "error-container": "#93000a",
                        "secondary-container": "#00a572",
                        "primary-fixed-dim": "#4cd7f6",
                        "on-surface-variant": "#bcc9cd",
                        "on-background": "#e5e1e4",
                        "on-tertiary-fixed": "#1a1c1c",
                        "outline-variant": "#3d494c",
                        "inverse-surface": "#e5e1e4",
                        "tertiary": "#c6c6c7",
                        "on-error-container": "#ffdad6",
                        "on-secondary": "#003824",
                        "on-surface": "#e5e1e4",
                        "primary": "#4cd7f6",
                        "outline": "#869397",
                        "inverse-primary": "#00687a",
                        "secondary-fixed-dim": "#4edea3",
                        "surface-container-high": "#2a2a2c",
                        "surface-container": "#201f22",
                        "on-tertiary": "#2f3131",
                        "secondary-fixed": "#6ffbbe",
                        "tertiary-fixed-dim": "#c6c6c7"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "md": "16px",
                        "gutter": "24px",
                        "lg": "24px",
                        "unit": "4px",
                        "xs": "4px",
                        "2xl": "64px",
                        "margin": "32px",
                        "sm": "8px",
                        "xl": "40px"
                    },
                    "fontFamily": {
                        "h2": ["Plus Jakarta Sans"],
                        "label-sm": ["Plus Jakarta Sans"],
                        "body-lg": ["Plus Jakarta Sans"],
                        "h1": ["Plus Jakarta Sans"],
                        "h3": ["Plus Jakarta Sans"],
                        "body-md": ["Plus Jakarta Sans"]
                    },
                    "fontSize": {
                        "h2": ["36px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-sm": ["13px", { "lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400" }],
                        "h1": ["clamp(28px, 5vw, 48px)", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "h3": ["clamp(18px, 3vw, 24px)", { "lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-md": ["clamp(14px, 2vw, 16px)", { "lineHeight": "1.6", "letterSpacing": "0", "fontWeight": "400" }]
                    }
                }
            }
        }
    </script>
<style>
        .glass-card {
            background-color: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
        }
        .glass-layer-2 {
            background-color: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px -10px rgba(6, 182, 212, 0.1);
        }
        .mesh-bg {
            background-color: #09090b;
            background-image: 
                radial-gradient(at 80% 0%, hsla(189,100%,40%,0.15) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(152,100%,40%,0.15) 0px, transparent 50%);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #06b6d4 0%, #00a572 100%);
            box-shadow: 0 4px 15px -3px rgba(6, 182, 212, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            box-shadow: 0 8px 25px -5px rgba(6, 182, 212, 0.6);
            filter: brightness(1.1);
        }
        .btn-glass {
            background-color: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .btn-glass:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen mesh-bg selection:bg-primary-container selection:text-white overflow-x-hidden">
<!-- TopNavBar Shared Component -->
@include("landing.partials.nav")
<main class="pt-[80px] sm:pt-[100px] pb-margin">
<!-- Hero Section -->
<section class="min-h-[60vh] sm:min-h-[819px] flex items-center px-4 sm:px-margin max-w-7xl mx-auto relative overflow-hidden py-8 sm:py-0">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-2xl items-center relative z-10">
<div class="space-y-lg">
<div class="inline-block px-sm py-xs rounded-full border border-primary-container/30 bg-primary-container/10 text-primary font-label-sm text-label-sm mb-sm">
<span class="material-symbols-outlined text-[16px] align-middle mr-xs">auto_awesome</span> {{ app()->getLocale() === 'sq' ? 'Automatizo Shitjet me AI' : 'AI-Powered Sales Tool' }}
                    </div>
<h1 class="font-h1 text-h1 text-on-surface">
@if(app()->getLocale() === 'sq')
                        Kthe Mesazhet në <br/>
<span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Shitje Automatike</span>
@else
                        Turn Messages into <br/>
<span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Automatic Sales</span>
@endif
</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
@if(app()->getLocale() === 'sq')
                        Nexus lexon mesazhet tuaja në Instagram, Facebook dhe WhatsApp, nxjerr detajet e porosive automatikisht, dhe i mban të gjitha në një vend. Thjesht.
@else
                        Nexus reads your Instagram, Facebook and WhatsApp messages, pulls out order details automatically, and keeps everything in one place. Simple.
@endif
                    </p>
<div class="flex flex-col sm:flex-row gap-md pt-md">
<a class="btn-gradient text-white px-lg py-md rounded-lg font-label-sm text-label-sm uppercase tracking-wider inline-flex justify-center items-center gap-sm" href="/register">
                            {{ app()->getLocale() === 'sq' ? 'Provo 30 Ditë Falas' : 'Try 30 Days Free' }}
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
<a class="btn-glass text-on-surface px-lg py-md rounded-lg font-label-sm text-label-sm uppercase tracking-wider inline-flex justify-center items-center gap-sm" href="#features">
                            {{ app()->getLocale() === 'sq' ? 'Shiko Si Funksionon' : 'See How It Works' }}
                        </a>
</div>
</div>
<div class="relative hidden lg:block">
<!-- Abstract 3D Mockup Representation using Glassmorphism -->
<div class="glass-layer-2 rounded-xl p-md aspect-square relative transform rotate-y-[-10deg] rotate-x-[5deg] transition-transform hover:rotate-0 duration-700">
<img class="w-full h-full object-cover rounded-lg opacity-80 mix-blend-screen" data-alt="A futuristic, abstract 3D rendering of floating glass panels and glowing cyan data streams converging into a structured grid. The scene is set against a deep #09090b background, illuminated by soft emerald and cyan mesh gradients. Light refracts through the translucent layers, creating a highly technical, premium 'command center' aesthetic. The overall mood is sophisticated, precise, and technologically advanced." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBySiWpfaNrtYJCTSVMWmxTflODQO6RWEa-pSUobLWWyCxRuaGAi38mpKgGIXuE3DFLbyK5PCok8mpxZFfozWCLP_uFAaxR_w0kBFBvF5Q5r0ZAyV-85-Vi-oCwXJgaORPrPmPA_45iw9uisvzYnabDa2clRJn_2ObJwiWs3uVuaX8MQlCf2eH-Blj1fb5mtMbJqqOClUx-EzmSQPfsauIB3rcbG_kPdoTREBtcyqqw3RNWc-RwsPvRxRUPt-YFXCMRc7E149IyB01u"/>
<!-- Floating UI Elements -->
<div class="absolute top-[20%] left-[-10%] glass-card p-sm rounded-lg flex items-center gap-sm w-48 shadow-2xl backdrop-blur-2xl">
<div class="w-8 h-8 rounded-full bg-surface-dim flex items-center justify-center border border-white/10">
<span class="material-symbols-outlined text-[16px] text-primary">chat</span>
</div>
<div>
<div class="h-2 w-24 bg-white/20 rounded-full mb-1"></div>
<div class="h-2 w-16 bg-white/10 rounded-full"></div>
</div>
</div>
<div class="absolute bottom-[20%] right-[-10%] glass-card p-sm rounded-lg flex items-center gap-sm w-56 shadow-2xl backdrop-blur-2xl border-primary/30">
<div class="w-8 h-8 rounded-full bg-surface-dim flex items-center justify-center border border-white/10">
<span class="material-symbols-outlined text-[16px] text-secondary">check_circle</span>
</div>
<div>
<div class="text-[10px] text-secondary font-bold mb-1">ORDER EXTRACTED</div>
<div class="h-2 w-32 bg-white/20 rounded-full"></div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Features Bento Grid -->
<section class="py-2xl px-4 sm:px-margin max-w-7xl mx-auto" id="features">
<div class="text-center mb-xl">
<h2 class="font-h2 text-h2 text-on-surface mb-sm">{{ app()->getLocale() === 'sq' ? 'Çfarë Mund Të Bëni Me Nexus' : 'What You Can Do With Nexus' }}</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">{{ app()->getLocale() === 'sq' ? 'Të gjitha mjetet që ju duhen për të menaxhuar porosite dhe klientët nga mesazhet.' : 'All the tools you need to manage orders and customers from messages.' }}</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-gutter">
<!-- Large Feature -->
<div class="glass-card rounded-xl p-lg md:col-span-2 flex flex-col justify-end min-h-[300px] relative overflow-hidden group">
<div class="absolute inset-0 bg-gradient-to-t from-background/90 to-transparent z-10"></div>
<img class="absolute inset-0 w-full h-full object-cover opacity-30 group-hover:opacity-40 transition-opacity duration-500" data-alt="A macro shot of a sleek, dark-themed digital dashboard displayed on a high-resolution screen. The interface features intricate data visualizations, glowing cyan charts, and crisp emerald typography against a deep black backdrop. Subtle reflections on the screen surface suggest a premium, glass-like finish. The lighting is cinematic, focusing on the precision of the technical data." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBOJyWusFK5hBJF1oBH7U4VDqwLRKrE4UA2KowZki8lg-ti1NYDuxU-pRuCYwvHlkcAd7z0PpcdEUiECH-moHNXtDd5G2v0Md-Syl8g-JAPvEbZ6xJO9T3bPl8D6RtTiNPAQRLIwmMhIk99eKEe1vZkSgTRCaSNEEry4QfUDzoZPTs46JOj2XE6MGv2MdCZGNFC1-PW3zTAQVjBgt_6zzGlwyucoWFOoO9fwhyjgF099PEVrX5_KpC7xwjoZzDxlcdlxzzdpAGyIgWH"/>
<div class="relative z-20">
<div class="w-12 h-12 rounded-lg bg-primary-container/20 border border-primary/30 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-primary" data-weight="fill">robot</span>
</div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'AI Nxjerrje Porosish' : 'AI Order Reading' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'AI lexon mesazhet e klientëve dhe nxjerr automatikisht detajet e porosisë — emrin, adresën, produktet.' : 'AI reads customer messages and automatically pulls out order details — name, address, products.' }}</p>
</div>
</div>
<!-- Small Feature 1 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">inbox</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Një Inbox Për Të Gjitha' : 'One Inbox For Everything' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Shikoni të gjitha mesazhet nga Instagram, Facebook dhe WhatsApp në një ekran.' : 'See all your Instagram, Facebook and WhatsApp messages on one screen.' }}</p>
</div>
</div>
<!-- Small Feature 2 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">group</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Puno Me Ekipin' : 'Work With Your Team' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Shto punonjës me role të ndryshme — admin ose agjent — dhe punoni së bashku.' : 'Add team members with different roles — admin or agent — and work together.' }}</p>
</div>
</div>
<!-- Medium Feature -->
<div class="glass-card rounded-xl p-lg md:col-span-2 flex flex-col justify-center min-h-[300px] relative overflow-hidden bg-gradient-to-br from-surface-dim to-surface-container-low">
<div class="absolute right-[-10%] top-[-10%] w-64 h-64 bg-secondary/10 rounded-full blur-[80px]"></div>
<div class="w-12 h-12 rounded-lg bg-secondary-container/20 border border-secondary/30 flex items-center justify-center mb-md relative z-10">
<span class="material-symbols-outlined text-[24px] text-secondary">local_shipping</span>
</div>
<div class="relative z-10 max-w-lg">
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Ndiqni Porosinë Nga Fillimi Deri Në Fund' : 'Track Orders From Start to Finish' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Ndiqni çdo porosi nga "Në Pritje" deri te "Dorëzuar" — pa dalë nga Nexus.' : 'Follow every order from "Pending" to "Delivered" — without leaving Nexus.' }}</p>
</div>
</div>
<!-- Small Feature 3 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">storefront</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Dyqani Juaj Online' : 'Your Online Shop' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Një katalog online ku klientët shikojnë dhe porosisin direkt nga WhatsApp.' : 'An online catalog where customers browse and order directly via WhatsApp.' }}</p>
</div>
</div>
<!-- Small Feature 4 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">sync</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Sinkronizo Me WhatsApp' : 'Sync With WhatsApp' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Produktet tuaja shfaqen automatikisht në katalogun e WhatsApp Business.' : 'Your products automatically show up in your WhatsApp Business catalog.' }}</p>
</div>
</div>
<!-- Small Feature 5 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">monitoring</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Parashiko Kërkesën' : 'Predict Demand' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'AI parashikon çfarë produktesh do të shiten në 14 ditët e ardhshme dhe ju njofton kur ti shtoni stokun.' : 'AI predicts which products will sell in the next 14 days and alerts you when to restock.' }}</p>
</div>
</div>
<!-- Small Feature 6 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">receipt_long</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Fatura & Kupona' : 'Invoices & Coupons' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Krijo fatura PDF me një klikim dhe menaxho zbritjet me kupona.' : 'Create PDF invoices with one click and manage discounts with coupons.' }}</p>
</div>
</div>
<!-- Medium Feature 2 -->
<div class="glass-card rounded-xl p-lg md:col-span-2 flex flex-col justify-center min-h-[300px] relative overflow-hidden bg-gradient-to-br from-surface-dim to-surface-container-low">
<div class="w-12 h-12 rounded-lg bg-primary-container/20 border border-primary/30 flex items-center justify-center mb-md relative z-10">
<span class="material-symbols-outlined text-[24px] text-primary">sentiment_satisfied</span>
</div>
<div class="relative z-10 max-w-lg">
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Kënaqësia & Kthimet' : 'Satisfaction & Returns' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Dërgoni sondazhe kënaqësie automatikisht dhe menaxhoni kthimet e porosive lehtësisht.' : 'Send satisfaction surveys automatically and manage order returns easily.' }}</p>
</div>
</div>
<!-- Small Feature 7 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">contacts</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Profilet e Klientëve' : 'Customer Profiles' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Shikoni historinë e blerjeve, totalin e shpenzuar, dhe nga cila platformë erdhi klienti.' : 'See purchase history, total spent, and which platform each customer came from.' }}</p>
</div>
</div>
<!-- Small Feature 8 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">sell</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Etiketat & Grupet' : 'Tags & Groups' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Etiketoni klientët automatikisht sipas rregullave — p.sh. numri i porosive ose qyteti.' : 'Tag customers automatically by rules — like order count or city.' }}</p>
</div>
</div>
<!-- Small Feature 9 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">campaign</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Mesazhe Masive' : 'Mass Messages' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Dërgoni njoftime, mesazhe të programuara, dhe ndjekje automatike pas blerjes.' : 'Send announcements, scheduled messages, and automatic follow-ups after purchase.' }}</p>
</div>
</div>
<!-- Medium Feature 3 -->
<div class="glass-card rounded-xl p-lg md:col-span-2 flex flex-col justify-center min-h-[300px] relative overflow-hidden bg-gradient-to-br from-surface-dim to-surface-container-low">
<div class="w-12 h-12 rounded-lg bg-secondary-container/20 border border-secondary/30 flex items-center justify-center mb-md relative z-10">
<span class="material-symbols-outlined text-[24px] text-secondary">quick_phrases</span>
</div>
<div class="relative z-10 max-w-lg">
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Përgjigje Të Shpejta' : 'Quick Replies' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Shkruani "/" për të futur përgjigje të gatshme me emrin e klientit. Kurseni orë çdo ditë.' : 'Type "/" to insert ready-made replies with the customer\'s name. Save hours every day.' }}</p>
</div>
</div>
<!-- Small Feature 10 -->
<div class="glass-card rounded-xl p-lg flex flex-col justify-between min-h-[300px]">
<div class="w-12 h-12 rounded-lg bg-surface-container-high border border-white/10 flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-[24px] text-on-surface">format_list_bulleted</span>
</div>
<div>
<h3 class="font-h3 text-h3 text-on-surface mb-xs">{{ app()->getLocale() === 'sq' ? 'Regjistri i Veprimeve' : 'Activity Log' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Shikoni çdo veprim që ka bërë çdo anëtar i ekipit — kush bëri çfarë dhe kur.' : 'See every action each team member has taken — who did what and when.' }}</p>
</div>
</div>
</div>
</section>
<!-- Pricing -->
<section class="py-2xl px-4 sm:px-margin max-w-7xl mx-auto relative" id="pricing">
<div class="absolute left-1/2 top-0 -translate-x-1/2 w-full max-w-3xl h-64 bg-primary/5 rounded-full blur-[100px] -z-10"></div>
<div class="text-center mb-xl">
<h2 class="font-h2 text-h2 text-on-surface mb-sm">{{ app()->getLocale() === 'sq' ? 'Çmimet e Thjeshta' : 'Simple Pricing' }}</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Zgjidhni planin që përshtatet me biznesin tuaj. Pa kosto të fshehura.' : 'Pick the plan that fits your business. No hidden fees.' }}</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-lg items-center">
<!-- Starter -->
<div class="glass-card rounded-xl p-xl">
<div class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-sm">{{ app()->getLocale() === 'sq' ? 'Fillestar' : 'Starter' }}</div>
<div class="font-h1 text-h1 text-on-surface mb-md">$49<span class="text-h3 text-on-surface-variant">/mo</span></div>
<ul class="space-y-sm mb-lg border-t border-white/5 pt-md">
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> 1 Channel Integration</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> 500 Orders / mo</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> Unified Inbox</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> CRM Profiles & Tags</li>
</ul>
<button class="w-full btn-glass text-on-surface py-sm rounded-lg font-label-sm text-label-sm uppercase">{{ app()->getLocale() === 'sq' ? 'Zgjidh Fillestar' : 'Select Starter' }}</button>
</div>
<!-- Pro (Highlighted) -->
<div class="glass-layer-2 rounded-xl p-xl border-primary/30 relative transform md:scale-105 z-10">
<div class="absolute top-0 right-0 bg-gradient-to-r from-primary to-secondary text-surface-container-lowest font-label-sm text-[10px] px-sm py-xs rounded-bl-lg rounded-tr-lg uppercase tracking-widest font-bold">{{ app()->getLocale() === 'sq' ? 'Rekomanduar' : 'Recommended' }}</div>
<div class="font-label-sm text-label-sm text-primary uppercase tracking-wider mb-sm">Pro</div>
<div class="font-h1 text-h1 text-on-surface mb-md">$99<span class="text-h3 text-on-surface-variant">/mo</span></div>
<div class="text-sm text-secondary mb-md font-bold">{{ app()->getLocale() === 'sq' ? '30 Ditë Provim Falas' : '30-Day Free Trial' }}</div>
<ul class="space-y-sm mb-lg border-t border-white/10 pt-md">
<li class="flex items-center gap-sm text-body-md text-on-surface"><span class="material-symbols-outlined text-[18px] text-secondary">check_circle</span> Unlimited Channels & Orders</li>
<li class="flex items-center gap-sm text-body-md text-on-surface"><span class="material-symbols-outlined text-[18px] text-secondary">check_circle</span> Storefront & WA Sync</li>
<li class="flex items-center gap-sm text-body-md text-on-surface"><span class="material-symbols-outlined text-[18px] text-secondary">check_circle</span> Invoices, Coupons & CSAT</li>
<li class="flex items-center gap-sm text-body-md text-on-surface"><span class="material-symbols-outlined text-[18px] text-secondary">check_circle</span> Team Logs & Broadcasts</li>
</ul>
<button class="w-full btn-gradient text-white py-sm rounded-lg font-label-sm text-label-sm uppercase">{{ app()->getLocale() === 'sq' ? 'Fillo Provimin Pro' : 'Start Pro Trial' }}</button>
</div>
<!-- Ultimate -->
<div class="glass-card rounded-xl p-xl">
<div class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-sm">{{ app()->getLocale() === 'sq' ? 'Premium' : 'Ultimate' }}</div>
<div class="font-h3 text-h3 text-on-surface mb-md flex items-center h-[57px]">{{ app()->getLocale() === 'sq' ? 'Çmim Sipas Nevojës' : 'Custom Pricing' }}</div>
<ul class="space-y-sm mb-lg border-t border-white/5 pt-md">
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> Everything in Pro</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> Full AI Order Extraction</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> Smart Order (AI Auto-fills)</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> AI Demand Forecasting</li>
<li class="flex items-center gap-sm text-body-md text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">check</span> Dedicated Priority API</li>
</ul>
<button class="w-full btn-glass text-on-surface py-sm rounded-lg font-label-sm text-label-sm uppercase">{{ app()->getLocale() === 'sq' ? 'Na Kontaktoni' : 'Contact Sales' }}</button>
</div>
</div>
</section>
</main>
<!-- Footer Shared Component -->
<footer class="w-full py-xl bg-surface-container-lowest dark:bg-surface-dim border-t border-white/5">
<div class="flex flex-col md:flex-row justify-between items-center px-4 sm:px-margin max-w-7xl mx-auto gap-lg">
<div class="font-h3 text-h3 font-bold text-on-surface">Nexus CRM</div>
<div class="flex flex-wrap justify-center gap-md">
<a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors hover:text-primary-fixed hover:translate-x-1 transition-transform" href="{{ route('privacy') }}">{{ app()->getLocale() === 'sq' ? 'Politika e Privatësisë' : 'Privacy Policy' }}</a>
<a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors hover:text-primary-fixed hover:translate-x-1 transition-transform" href="{{ route('terms') }}">{{ app()->getLocale() === 'sq' ? 'Kushtet e Shërbimit' : 'Terms of Service' }}</a>
</div>
<div class="font-label-sm text-label-sm text-secondary text-center md:text-right">
                ©2026 Nexus CRM. {{ app()->getLocale() === 'sq' ? 'Të gjitha të drejtat e rezervuara.' : 'All rights reserved.' }}
            </div>
</div>
</footer>
</body></html>