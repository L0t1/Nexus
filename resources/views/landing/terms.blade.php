<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Terms of Service - Nexus CRM</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
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

@include("landing.partials.nav")

<main class="pt-[100px] pb-2xl max-w-4xl mx-auto px-lg flex flex-col gap-2xl">
    <!-- Hero Header -->
    <header class="text-center pt-xl pb-lg flex flex-col items-center gap-md max-w-3xl mx-auto border-b border-white/5">
        <h1 class="font-h1 text-h1 text-on-surface leading-tight">Terms of Service</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mt-sm">Last Updated: May 2026</p>
    </header>

    <div class="space-y-lg font-body-md text-body-md text-on-surface-variant">
        <h2 class="font-h2 text-h2 text-on-surface mb-sm">1. Acceptance of Terms</h2>
        <p class="mb-md">By accessing and using Nexus CRM ("Service"), you agree to be bound by these Terms of Service. If you do not agree, please do not use the Service.</p>
        
        <h2 class="font-h2 text-h2 text-on-surface mb-sm mt-xl">2. Description of Service</h2>
        <p class="mb-md">Nexus CRM is an elite AI command center designed to extract order details and track logistics across platforms like Instagram, Facebook, and WhatsApp. The Service provides a unified inbox and automated workflows.</p>

        <h2 class="font-h2 text-h2 text-on-surface mb-sm mt-xl">3. User Responsibilities</h2>
        <ul class="list-disc pl-lg space-y-sm mb-md">
            <li>You must provide accurate information when creating an account.</li>
            <li>You are responsible for maintaining the security of your account and password.</li>
            <li>You agree not to use the Service for any illegal or unauthorized purpose.</li>
        </ul>

        <h2 class="font-h2 text-h2 text-on-surface mb-sm mt-xl">4. Intellectual Property</h2>
        <p class="mb-md">All content, features, and functionality of the Service are owned by Nexus CRM and are protected by international copyright, trademark, and other intellectual property laws.</p>

        <h2 class="font-h2 text-h2 text-on-surface mb-sm mt-xl">5. Limitation of Liability</h2>
        <p class="mb-md">Nexus CRM shall not be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the Service.</p>
        
        <h2 class="font-h2 text-h2 text-on-surface mb-sm mt-xl">6. Contact Us</h2>
        <p class="mb-md">If you have any questions about these Terms, please contact us at <a href="mailto:support@nexuscrm.com" class="text-primary hover:text-primary-fixed transition-colors">support@nexuscrm.com</a>.</p>
    </div>
</main>

<footer class="w-full py-xl bg-surface-container-lowest border-t border-white/5 mt-auto">
    <div class="text-center font-label-sm text-label-sm text-on-surface-variant">
        © 2026 Nexus CRM. Elite command center for high-growth enterprises.
    </div>
</footer>

</body>
</html>
