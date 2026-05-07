<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Privacy Policy - Nexus CRM</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        background: "#131315",
                        surface: "#131315",
                        "surface-dim": "#131315",
                        "surface-container-lowest": "#0e0e10",
                        "on-surface": "#e5e1e4",
                        "on-surface-variant": "#bcc9cd",
                        primary: "#4cd7f6",
                    },
                    fontFamily: {
                        "h1": ["Plus Jakarta Sans"],
                        "h2": ["Plus Jakarta Sans"],
                        "body-lg": ["Plus Jakarta Sans"],
                        "body-md": ["Plus Jakarta Sans"]
                    }
                }
            }
        }
    </script>
    <style>
        .mesh-bg {
            background-color: #09090b;
            background-image: 
                radial-gradient(at 80% 0%, hsla(189,100%,40%,0.15) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(152,100%,40%,0.15) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen mesh-bg">

@include("landing.partials.nav")

<main class="pt-[120px] pb-32 max-w-4xl mx-auto px-6">
    <h1 class="text-4xl font-bold mb-8 text-on-surface">Privacy Policy</h1>
    <div class="space-y-6 text-on-surface-variant text-lg">
        <p><strong>Last Updated: May 2026</strong></p>
        
        <h2 class="text-2xl font-semibold text-on-surface mt-8 mb-4">1. Introduction</h2>
        <p>Welcome to Nexus CRM. We value your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, and share information when you use our services, including our integration with Meta products (Facebook, Instagram, WhatsApp).</p>
        
        <h2 class="text-2xl font-semibold text-on-surface mt-8 mb-4">2. Information We Collect</h2>
        <p>When you connect your Meta accounts to Nexus CRM, we collect specific data necessary to provide our CRM services:</p>
        <ul class="list-disc pl-6 space-y-2">
            <li><strong>Profile Information:</strong> Your name, email, and business page details.</li>
            <li><strong>Messages and Conversations:</strong> Messages sent to your connected Instagram, Facebook, or WhatsApp business accounts to power our unified inbox and AI extraction.</li>
            <li><strong>Customer Data:</strong> Details of your customers extracted from conversations (e.g., shipping addresses, order intent) strictly for fulfillment purposes.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-on-surface mt-8 mb-4">3. How We Use Your Data</h2>
        <p>We use the data collected strictly for the following purposes:</p>
        <ul class="list-disc pl-6 space-y-2">
            <li>To provide and maintain the Nexus CRM service.</li>
            <li>To extract order details and automate fulfillment tracking via our AI Command Center.</li>
            <li>To facilitate your communication with your customers through Meta platforms.</li>
        </ul>
        <p>We do not sell your personal data or your customers' data to third parties.</p>

        <h2 class="text-2xl font-semibold text-on-surface mt-8 mb-4">4. Meta Platform Terms</h2>
        <p>By connecting your Meta channels, you acknowledge that your use of the messaging services is also governed by Meta's Privacy Policy and Terms of Service. Nexus CRM acts as a tool to manage your existing communications.</p>

        <h2 class="text-2xl font-semibold text-on-surface mt-8 mb-4">5. Data Retention & Deletion</h2>
        <p>You can request data deletion at any time by disconnecting your Meta accounts from the Settings panel or by contacting our support team. Upon disconnection, we will delete the synchronization tokens immediately.</p>
        
        <h2 class="text-2xl font-semibold text-on-surface mt-8 mb-4">6. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please contact us at privacy@nexuscrm.com.</p>
    </div>
</main>

<footer class="w-full py-8 bg-surface-container-lowest border-t border-white/5 mt-auto">
    <div class="text-center text-sm text-on-surface-variant">
        © 2026 Nexus CRM. Elite command center for high-growth enterprises.
    </div>
</footer>

</body>
</html>
