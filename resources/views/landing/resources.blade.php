<!DOCTYPE html>

<html class="dark" lang="{{ app()->getLocale() }}"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Nexus CRM - {{ app()->getLocale() === 'sq' ? 'Burimet' : 'Resources' }}</title>
<!-- Google Fonts: Plus Jakarta Sans -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Theme Configuration -->
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
                    "body-lg": [
                            "Plus Jakarta Sans"
                    ],
                    "label-sm": [
                            "Plus Jakarta Sans"
                    ],
                    "h3": [
                            "Plus Jakarta Sans"
                    ],
                    "body-md": [
                            "Plus Jakarta Sans"
                    ],
                    "h2": [
                            "Plus Jakarta Sans"
                    ],
                    "h1": [
                            "Plus Jakarta Sans"
                    ]
            },
            "fontSize": {
                    "body-lg": [
                            "18px",
                            {
                                    "lineHeight": "1.6",
                                    "letterSpacing": "0",
                                    "fontWeight": "400"
                            }
                    ],
                    "label-sm": [
                            "13px",
                            {
                                    "lineHeight": "1",
                                    "letterSpacing": "0.05em",
                                    "fontWeight": "600"
                            }
                    ],
                    "h3": [
                            "24px",
                            {
                                    "lineHeight": "1.3",
                                    "letterSpacing": "-0.01em",
                                    "fontWeight": "600"
                            }
                    ],
                    "body-md": [
                            "16px",
                            {
                                    "lineHeight": "1.6",
                                    "letterSpacing": "0",
                                    "fontWeight": "400"
                            }
                    ],
                    "h2": [
                            "36px",
                            {
                                    "lineHeight": "1.2",
                                    "letterSpacing": "-0.02em",
                                    "fontWeight": "700"
                            }
                    ],
                    "h1": [
                            "48px",
                            {
                                    "lineHeight": "1.2",
                                    "letterSpacing": "-0.02em",
                                    "fontWeight": "700"
                            }
                    ]
            }
          },
        },
      }
    </script>
<style>
        /* Ambient Mesh Background Glow */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: -10%;
            width: 70vw;
            height: 70vh;
            background: radial-gradient(circle, rgba(76, 215, 246, 0.05) 0%, rgba(19, 19, 21, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            right: -10%;
            width: 60vw;
            height: 60vh;
            background: radial-gradient(circle, rgba(78, 222, 163, 0.05) 0%, rgba(19, 19, 21, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }
        
        /* Refractive Edge Utility */
        .glass-edge {
            box-shadow: inset 1px 1px 0px 0px rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen font-body-md antialiased flex flex-col">
<!-- Shared Component: TopNavBar -->
@include("landing.partials.nav")
<!-- Main Content -->
<main class="flex-grow pt-[80px] sm:pt-[104px] pb-2xl px-4 sm:px-gutter max-w-7xl mx-auto w-full flex flex-col gap-2xl">
<!-- Hero Section -->
<header class="text-center max-w-3xl mx-auto mt-xl mb-md">
<h1 class="font-h1 text-h1 text-on-surface mb-md">{{ app()->getLocale() === 'sq' ? 'Qendra e Njohurive' : 'Learning Center' }}</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Mësoni si të shisni më shumë, eksploroni integrimet, dhe forconi biznesin tuaj.' : 'Learn how to sell more, explore integrations, and strengthen your business.' }}</p>
</header>
<!-- Bento Grid Layout -->
<section class="grid grid-cols-1 md:grid-cols-12 auto-rows-[250px] sm:auto-rows-[320px] gap-4 sm:gap-gutter">
<!-- Featured Blog (Spans 8 cols) -->
<article class="md:col-span-8 rounded-xl bg-white/[0.03] backdrop-blur-xl border border-white/10 glass-edge overflow-hidden relative group cursor-pointer hover:border-primary/50 transition-colors duration-500">
<!-- Background Image -->
<div class="absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-50 transition-opacity duration-700 mix-blend-luminosity" data-alt="A cinematic, wide-angle shot of a diverse team of professionals collaborating in a highly futuristic, glass-walled skyscraper office overlooking a neon-lit metropolis at dusk. The lighting is moody and sophisticated, utilizing deep shadows contrasted with vibrant cyan and emerald green screen reflections. The overall aesthetic implies high-growth enterprise success and advanced social selling techniques. The scene is sharp, ultra-realistic, and polished." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuACg_lRT9teHUG65eAoSyVGKt51L_xMKnzwZ4Q0sTB2L90KiBJAVP447VtluADXO_YO1xb_oQzaODc5Cj7BrK1XCq1emdNU0E33HMxSz5FQYW_Lg3HCQI4eobKujZbOUXuAZT_IWPWiE80LF_Hnj5gYqWQuGezG_I1QtBqZyRfYYl_q9zMUW1Y8rpCf7iNcmo0XuuCG491qv0XRp_Z1Ee-t7BL2AS6nWXM_iaQTxpGjLX8pDG6dVDNUGU4UsV4G4xk9_lSRlcF48pR-');"></div>
<!-- Gradient Overlay -->
<div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest via-surface-container-lowest/80 to-transparent"></div>
<!-- Content -->
<div class="absolute bottom-0 left-0 p-xl w-full">
<span class="inline-block px-sm py-xs rounded-full bg-primary/10 text-primary font-label-sm text-label-sm mb-md border border-primary/20">Social Selling</span>
<h2 class="font-h2 text-h2 text-on-surface mb-sm">{{ app()->getLocale() === 'sq' ? 'Udhëzuesi2026 për Shitje në Rrjete Sociale' : 'The2026 Guide to Social Selling' }}</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">{{ app()->getLocale() === 'sq' ? 'Zbuloni si bizneset më të mira përdorin komunikimin transparent dhe shumë kanale për të rritur shitjet.' : 'Discover how top businesses use transparent communication and multiple channels to grow sales.' }}</p>
</div>
</article>
<!-- API Docs (Spans 4 cols) -->
<article class="md:col-span-4 rounded-xl bg-white/[0.03] backdrop-blur-xl border border-white/10 glass-edge p-xl flex flex-col justify-between group cursor-pointer hover:border-secondary/50 transition-colors duration-500 relative overflow-hidden">
<div class="absolute -right-10 -top-10 w-40 h-40 bg-secondary/10 rounded-full blur-3xl group-hover:bg-secondary/20 transition-colors"></div>
<div>
<div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center border border-white/5 mb-lg group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-secondary text-[28px]">code_blocks</span>
</div>
<h3 class="font-h3 text-h3 text-on-surface mb-sm">{{ app()->getLocale() === 'sq' ? 'Dokumentimi i API' : 'API Documentation' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant">{{ app()->getLocale() === 'sq' ? 'Lidhni Nexus CRM me sistemet tuaja ekzistuese. Versioni 2.0 tani i disponueshëm.' : 'Connect Nexus CRM with your existing systems. Version 2.0 now available.' }}</p>
</div>
<div class="flex items-center gap-sm text-secondary font-label-sm text-label-sm mt-lg">
<span>{{ app()->getLocale() === 'sq' ? 'Shiko Dokumentimin' : 'View Docs' }}</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</article>
<!-- Help Center (Spans 5 cols) -->
<article class="md:col-span-5 rounded-xl bg-white/[0.03] backdrop-blur-xl border border-white/10 glass-edge p-xl flex flex-col group cursor-pointer hover:border-primary/30 transition-colors duration-500">
<div class="flex items-center gap-md mb-lg">
<span class="material-symbols-outlined text-primary text-[32px]">support_agent</span>
<h3 class="font-h3 text-h3 text-on-surface">{{ app()->getLocale() === 'sq' ? 'Qendra e Ndihmës' : 'Help Center' }}</h3>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mb-xl flex-grow">{{ app()->getLocale() === 'sq' ? 'Gjeni udhëzues të detajuara, zgjidhje problemesh, dhe konfigurime për Nexus.' : 'Find detailed guides, troubleshooting, and settings for Nexus.' }}</p>
<!-- Mock Search -->
<div class="w-full bg-black/20 border border-outline-variant rounded-lg p-sm flex items-center gap-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">search</span>
<span class="font-body-md text-body-md opacity-70">{{ app()->getLocale() === 'sq' ? 'Kërko në bazën e njohurive...' : 'Search help articles...' }}</span>
</div>
</article>
<!-- Secondary Blog/Case Study (Spans 7 cols) -->
<article class="md:col-span-7 rounded-xl bg-white/[0.03] backdrop-blur-xl border border-white/10 glass-edge flex flex-col md:flex-row overflow-hidden group cursor-pointer hover:border-white/20 transition-colors duration-500">
<div class="w-full md:w-2/5 h-48 md:h-full bg-cover bg-center border-r border-white/5 relative" data-alt="An abstract, tech-forward 3D rendering of interconnected data nodes floating in a dark, atmospheric void. Subtle streams of cyan and emerald green light connect the glowing white data points, representing seamless API integration and high-speed information flow. The visual style is premium glassmorphism, with deep depth of field and soft, volumetric lighting highlighting the technological complexity. The mood is analytical, precise, and cutting-edge." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDmWOVMjn4DjGeM8BM6E5waXSuMzyz2IaTZFga_eiaeZ4IRY2IyMYAY6O0-mEHohuWkxp-46xxGrVACCc4u1_23Fr3vo6oL2HI_q-qQPVt27H9DOtD05YCle1PY4Nptb3EyRsQm6vtJYzKctFFaR8ZIRgaoBJ0nb_ElOAd15ljsjohmKx4yA-iKQhoTon3GGMRDVRJSh-lFkyYu1yw8Q7IkHiH5Wj5Nb094Cky2IblqavLgrGw1fPVyizHZQZHptrqMC3kgIPn_gtz0');">
<div class="absolute inset-0 bg-surface-container-lowest/30 group-hover:bg-transparent transition-colors duration-500"></div>
</div>
<div class="w-full md:w-3/5 p-xl flex flex-col justify-center">
<span class="inline-block px-sm py-xs rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm w-max mb-md border border-white/5">Case Study</span>
<h3 class="font-h3 text-h3 text-on-surface mb-sm">{{ app()->getLocale() === 'sq' ? 'Rritja 300% e GlobalTech' : 'GlobalTech\'s 300% Growth' }}</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg">{{ app()->getLocale() === 'sq' ? 'Si centralizimi i të dhënave të klientëve në Nexus e transformoi një ekip shitjesh të fragmentuar në një ekip të bashkuar e të suksesshëm.' : 'How centralizing customer data in Nexus transformed a scattered sales team into a united, successful one.' }}</p>
<div class="flex items-center gap-sm text-on-surface font-label-sm text-label-sm">
<span>{{ app()->getLocale() === 'sq' ? 'Lexo studimin e plotë' : 'Read full study' }}</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
</article>
</section>
<!-- Newsletter Signup (Glassmorphism highlight) -->
<section class="mt-xl relative w-full max-w-4xl mx-auto">
<!-- Glow behind -->
<div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-secondary/20 blur-3xl -z-10 rounded-full opacity-50"></div>
<div class="bg-white/[0.04] backdrop-blur-2xl border border-white/10 rounded-xl p-xl md:p-2xl text-center glass-edge flex flex-col items-center">
<span class="material-symbols-outlined text-primary text-[40px] mb-md">mark_email_unread</span>
<h2 class="font-h2 text-h2 text-on-surface mb-sm">{{ app()->getLocale() === 'sq' ? 'Qendroni të Informuar' : 'Stay Updated' }}</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-lg mb-xl">{{ app()->getLocale() === 'sq' ? 'Merrni këshilla për CRM dhe shitje në rrjete sociale çdo javë, direkt në emailin tuaj.' : 'Get CRM and social selling tips every week, straight to your email.' }}</p>
<form class="w-full max-w-md flex flex-col sm:flex-row gap-sm">
<input class="flex-grow bg-black/30 border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary rounded-lg px-md py-sm text-on-surface font-body-md text-body-md placeholder-on-surface-variant/50 outline-none transition-all" placeholder="{{ app()->getLocale() === 'sq' ? 'Email-i juaj i biznesit' : 'Enter your business email' }}" type="email"/>
<button class="bg-gradient-to-r from-primary to-secondary text-on-primary font-label-sm text-label-sm px-lg py-sm rounded-lg hover:shadow-[0_0_20px_rgba(76,215,246,0.4)] transition-all whitespace-nowrap" type="submit">
                        {{ app()->getLocale() === 'sq' ? 'Abonohu Tani' : 'Subscribe Now' }}
                    </button>
</form>
</div>
</section>
</main>
<!-- Shared Component: Footer -->
<footer class="bg-surface-container-lowest w-full border-t border-white/5">
<div class="flex flex-col md:flex-row justify-between items-center px-lg py-xl gap-md max-w-7xl mx-auto">
<!-- Brand & Copyright -->
<div class="flex flex-col items-center md:items-start gap-xs">
<span class="text-h3 font-h3 font-bold text-primary">Nexus CRM</span>
<p class="font-body-md text-body-md text-secondary">©2026 Nexus CRM. {{ app()->getLocale() === 'sq' ? 'Të gjitha të drejtat e rezervuara.' : 'All rights reserved.' }}</p>
</div>
<!-- Links -->
<div class="flex flex-wrap justify-center gap-md">
<a class="font-body-md text-body-md text-on-surface-variant hover:text-secondary hover:opacity-80 transition-all" href="{{ route('privacy') }}">{{ app()->getLocale() === 'sq' ? 'Politika e Privatësisë' : 'Privacy Policy' }}</a>
<a class="font-body-md text-body-md text-on-surface-variant hover:text-secondary hover:opacity-80 transition-all" href="{{ route('terms') }}">{{ app()->getLocale() === 'sq' ? 'Kushtet e Shërbimit' : 'Terms of Service' }}</a>
</div>
</div>
</footer>
</body></html>