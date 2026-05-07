<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Nexus CRM - Pricing</title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Google Fonts: Plus Jakarta Sans -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind Config -->
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
<style>
        /* Custom styles for detail/summary accordion */
        details > summary {
            list-style: none;
        }
        details > summary::-webkit-details-marker {
            display: none;
        }
        details[open] summary ~ * {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col relative overflow-x-hidden">
<!-- Ambient Background Mesh Glows -->
<div class="fixed top-0 left-1/4 w-[800px] h-[800px] bg-primary/5 blur-[150px] rounded-full pointer-events-none z-0"></div>
<div class="fixed bottom-0 right-1/4 w-[600px] h-[600px] bg-secondary/5 blur-[150px] rounded-full pointer-events-none z-0"></div>
<!-- TopNavBar -->
@include("landing.partials.nav")
<!-- Main Content Canvas -->
<main class="flex-grow pt-[120px] pb-2xl relative z-10 w-full max-w-7xl mx-auto px-lg flex flex-col gap-2xl">
<!-- Hero Section -->
<section class="text-center max-w-3xl mx-auto flex flex-col gap-lg mt-xl">
<div class="inline-flex items-center gap-sm bg-white/5 border border-white/10 rounded-full px-md py-sm mx-auto backdrop-blur-md">
<span class="material-symbols-outlined text-secondary text-[16px]">verified</span>
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Transparent Pricing</span>
</div>
<h1 class="font-h1 text-h1 text-on-surface">
                Elite performance, <br/>
<span class="bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">scaled for your growth.</span>
</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant">
                Select a command center tailored to your team's velocity. No hidden fees, just uncompromising technical precision.
            </p>
</section>
<!-- Pricing Bento Grid -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<!-- Starter Tier -->
<div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-xl flex flex-col gap-lg relative overflow-hidden group hover:bg-white/10 transition-colors duration-300">
<div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
<div class="flex flex-col gap-xs">
<h3 class="font-h3 text-h3 text-on-surface">Starter</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Essential tools for emerging teams.</p>
</div>
<div class="flex items-end gap-xs">
<span class="font-h1 text-h1 text-on-surface">$49</span>
<span class="font-body-md text-body-md text-on-surface-variant mb-sm">/mo</span>
</div>
<button class="w-full bg-surface-container border border-outline text-on-surface font-label-sm text-label-sm px-lg py-md rounded-lg hover:bg-surface-container-high transition-colors">
                    Start Free Trial
                </button>
<div class="w-full h-px bg-white/5 my-sm"></div>
<ul class="flex flex-col gap-md">
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">Max 500 Orders / Month</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">1 Integration Channel</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">Basic Analytics</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">No AI Features</span>
</li>
</ul>
</div>
<!-- Pro Tier (Highlighted) -->
<div class="bg-white/[0.08] backdrop-blur-2xl border border-primary/40 rounded-xl p-xl flex flex-col gap-lg relative overflow-hidden transform md:-translate-y-md shadow-[0_16px_40px_0_rgba(6,182,212,0.15)]">
<div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-primary to-secondary"></div>
<div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/20 blur-[40px] rounded-full pointer-events-none"></div>
<div class="flex justify-between items-start">
<div class="flex flex-col gap-xs">
<h3 class="font-h3 text-h3 text-primary">Pro</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Advanced command center for scaling.</p>
</div>
<span class="bg-primary/10 text-primary font-label-sm text-label-sm px-md py-xs rounded-full border border-primary/20">Most Popular</span>
</div>
<div class="flex items-end gap-xs">
<span class="font-h1 text-h1 text-on-surface">$99</span>
<span class="font-body-md text-body-md text-on-surface-variant mb-sm">/mo</span>
</div>
<button class="w-full bg-gradient-to-r from-primary to-secondary text-on-primary-fixed font-label-sm text-label-sm px-lg py-md rounded-lg hover:shadow-[0_0_20px_rgba(76,215,246,0.4)] transition-all">
                    Upgrade to Pro
                </button>
<div class="w-full h-px bg-white/10 my-sm"></div>
<ul class="flex flex-col gap-md">
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span class="font-body-md text-body-md text-on-surface">Unlimited Orders</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span class="font-body-md text-body-md text-on-surface">Unlimited Omni-Channels</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span class="font-body-md text-body-md text-on-surface">Advanced Analytics & Products</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span class="font-body-md text-body-md text-on-surface">No AI Features</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
<span class="font-body-md text-body-md text-on-surface">Product Management</span>
</li>
</ul>
</div>
<!-- Ultimate Tier -->
<div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-xl flex flex-col gap-lg relative overflow-hidden group hover:bg-white/10 transition-colors duration-300">
<div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
<div class="flex flex-col gap-xs">
<h3 class="font-h3 text-h3 text-on-surface">Ultimate</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Bespoke architecture for global operations.</p>
</div>
<div class="flex items-end gap-xs h-[48px] items-center">
<span class="font-h3 text-h3 text-on-surface">Custom Pricing</span>
</div>
<button class="w-full bg-surface-container border border-outline text-on-surface font-label-sm text-label-sm px-lg py-md rounded-lg hover:bg-surface-container-high transition-colors">
                    Contact Sales
                </button>
<div class="w-full h-px bg-white/5 my-sm"></div>
<ul class="flex flex-col gap-md">
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">Unlimited Orders & Channels</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">Unlimited AI Auto-Reply</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">AI Order Extraction</span>
</li>
<li class="flex items-center gap-md">
<span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
<span class="font-body-md text-body-md text-on-surface-variant">Broadcasts & Priority Inbox</span>
</li>
</ul>
</div>
</section>
<!-- Feature Comparison Matrix -->
<section class="mt-xl flex flex-col gap-lg">
<h2 class="font-h2 text-h2 text-on-surface text-center mb-md">Comprehensive Feature Matrix</h2>
<div class="w-full overflow-x-auto rounded-xl border border-white/10 bg-white/5 backdrop-blur-xl">
<table class="w-full text-left border-collapse min-w-[800px]">
<thead>
<tr class="border-b border-white/10 bg-white/5">
<th class="p-lg font-h3 text-h3 text-on-surface w-2/5">Features</th>
<th class="p-lg font-body-lg text-body-lg text-on-surface font-semibold w-1/5 text-center">Starter</th>
<th class="p-lg font-body-lg text-body-lg text-primary font-semibold w-1/5 text-center border-l border-r border-primary/20 bg-primary/5">Pro</th>
<th class="p-lg font-body-lg text-body-lg text-on-surface font-semibold w-1/5 text-center">Ultimate</th>
</tr>
</thead>
<tbody class="font-body-md text-body-md text-on-surface-variant">
<!-- Category Row -->
<tr class="bg-white/5 border-b border-white/5">
<td class="p-md px-lg font-label-sm text-label-sm text-on-surface uppercase tracking-widest bg-surface-container-low" colspan="4">Core CRM Engine</td>
</tr>
<tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
<td class="p-md px-lg">Contact Storage Limit</td>
<td class="p-md text-center">10,000</td>
<td class="p-md text-center border-l border-r border-primary/10 bg-primary/[0.02]">Unlimited</td>
<td class="p-md text-center">Unlimited</td>
</tr>
<tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
<td class="p-md px-lg">Custom Data Fields</td>
<td class="p-md text-center">50</td>
<td class="p-md text-center border-l border-r border-primary/10 bg-primary/[0.02]">500</td>
<td class="p-md text-center">Unlimited</td>
</tr>
<tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
<td class="p-md px-lg">Omni-channel Feed</td>
<td class="p-md text-center"><span class="material-symbols-outlined text-primary">check</span></td>
<td class="p-md text-center border-l border-r border-primary/10 bg-primary/[0.02]"><span class="material-symbols-outlined text-primary">check</span></td>
<td class="p-md text-center"><span class="material-symbols-outlined text-primary">check</span></td>
</tr>
<!-- Category Row -->
<tr class="bg-white/5 border-b border-white/5">
<td class="p-md px-lg font-label-sm text-label-sm text-on-surface uppercase tracking-widest bg-surface-container-low" colspan="4">Intelligence &amp; Automation</td>
</tr>
<tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
<td class="p-md px-lg">Workflow Automations</td>
<td class="p-md text-center">Basic (10 rules)</td>
<td class="p-md text-center border-l border-r border-primary/10 bg-primary/[0.02]">Advanced (Unlimited)</td>
<td class="p-md text-center">Custom Architecture</td>
</tr>
<tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
<td class="p-md px-lg">Predictive Lead Scoring (AI)</td>
<td class="p-md text-center text-outline-variant"><span class="material-symbols-outlined">remove</span></td>
<td class="p-md text-center border-l border-r border-primary/10 bg-primary/[0.02]"><span class="material-symbols-outlined text-primary">check</span></td>
<td class="p-md text-center"><span class="material-symbols-outlined text-primary">check</span></td>
</tr>
<tr class="hover:bg-white/5 transition-colors">
<td class="p-md px-lg">Sentiment Analysis</td>
<td class="p-md text-center text-outline-variant"><span class="material-symbols-outlined">remove</span></td>
<td class="p-md text-center border-l border-r border-primary/10 bg-primary/[0.02]"><span class="material-symbols-outlined text-primary">check</span></td>
<td class="p-md text-center"><span class="material-symbols-outlined text-primary">check</span></td>
</tr>
</tbody>
</table>
</div>
</section>
<!-- Glassmorphic FAQ Section -->
<section class="max-w-3xl mx-auto w-full flex flex-col gap-lg mt-xl">
<h2 class="font-h2 text-h2 text-on-surface text-center mb-md">Frequently Asked Questions</h2>
<div class="flex flex-col gap-md">
<details class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl overflow-hidden cursor-pointer" open="">
<summary class="flex justify-between items-center p-lg font-h3 text-h3 text-on-surface outline-none">
                        What constitutes a 'User' in your pricing?
                        <span class="material-symbols-outlined text-primary transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="p-lg pt-0 text-on-surface-variant font-body-md text-body-md border-t border-white/5 mt-sm">
                        A user is defined as any individual with a unique login credential who actively accesses the Nexus CRM command center to view, edit, or manage data. Read-only external stakeholders do not consume a user license.
                    </div>
</details>
<details class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl overflow-hidden cursor-pointer">
<summary class="flex justify-between items-center p-lg font-h3 text-h3 text-on-surface outline-none">
                        Can we upgrade our tier mid-cycle?
                        <span class="material-symbols-outlined text-primary transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="p-lg pt-0 text-on-surface-variant font-body-md text-body-md border-t border-white/5 mt-sm">
                        Yes, you can scale your tier up at any time. Prorated charges will be applied automatically to your next billing cycle. Downgrades require a 30-day notice prior to your contract renewal date.
                    </div>
</details>
<details class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl overflow-hidden cursor-pointer">
<summary class="flex justify-between items-center p-lg font-h3 text-h3 text-on-surface outline-none">
                        Is data migration included?
                        <span class="material-symbols-outlined text-primary transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="p-lg pt-0 text-on-surface-variant font-body-md text-body-md border-t border-white/5 mt-sm">
                        Automated data migration tools are included in all tiers. White-glove, engineered data migration managed by our technical success team is exclusive to the Ultimate tier or available as a one-time add-on for Pro users.
                    </div>
</details>
</div>
</section>
<!-- Ultimate CTA Section -->
<section class="mt-xl w-full">
<div class="bg-white/5 backdrop-blur-2xl border border-secondary/30 rounded-2xl p-2xl flex flex-col md:flex-row items-center justify-between gap-xl relative overflow-hidden shadow-[0_8px_32px_0_rgba(78,222,163,0.1)]">
<!-- Abstract Glass Element -->
<div class="absolute -bottom-24 -left-24 w-64 h-64 bg-secondary/20 blur-[60px] rounded-full pointer-events-none"></div>
<div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-secondary to-transparent opacity-50"></div>
<div class="flex flex-col gap-md relative z-10 max-w-xl">
<h2 class="font-h2 text-h2 text-on-surface">Require unparalleled scale?</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">
                        Our Ultimate architecture provides dedicated instances, custom security compliance, and a dedicated engineering liaison to ensure Nexus CRM seamlessly integrates into your existing complex tech stack.
                    </p>
</div>
<div class="relative z-10 shrink-0">
<button class="bg-surface-container-lowest border border-secondary text-secondary font-label-sm text-label-sm px-xl py-lg rounded-xl hover:bg-secondary/10 hover:shadow-[0_0_20px_rgba(78,222,163,0.3)] transition-all duration-300 flex items-center gap-sm">
<span class="material-symbols-outlined text-[20px]">headset_mic</span>
                        Engage Ultimate Sales
                    </button>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-surface-container-lowest w-full border-t border-white/5 relative z-10 mt-auto">
<div class="flex flex-col md:flex-row justify-between items-center px-lg py-xl gap-md max-w-7xl mx-auto">
<span class="text-secondary font-body-md text-body-md">
                © 2024 Nexus CRM. Elite Craftsmanship for High-Growth Ultimate.
            </span>
<div class="flex items-center gap-lg">
<a class="text-on-surface-variant font-body-md text-body-md hover:text-secondary hover:opacity-80 transition-colors transition-opacity" href="{{ route('privacy') }}">Privacy Policy</a>
<a class="text-on-surface-variant font-body-md text-body-md hover:text-secondary hover:opacity-80 transition-colors transition-opacity" href="{{ route('terms') }}">Terms of Service</a>
</div>
</div>
</footer>
</body></html>