# NEXUS CRM

> Chat-to-Cart e-commerce platform for the Balkan market. Turns Instagram, WhatsApp, and Facebook DMs into trackable orders with AI-powered address extraction.

## Quick Start

```bash
# 1. Install dependencies
composer install
npm install

# 2. Set up environment
cp .env.example .env
php artisan key:generate

# 3. Create database & seed demo data
php artisan migrate --seed

# 4. Start development servers (both required)
npx vite          # Terminal 1 — Frontend HMR
php artisan serve  # Terminal 2 — Backend
```

**Open:** http://localhost:8000  
**Login:** `admin@nexus.test` / `password`

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.2 |
| Frontend | Vue 3 (Composition API), Inertia.js |
| Styling | Tailwind CSS 4 |
| State | Pinia |
| Database | SQLite (dev), MySQL 8 (prod) |
| Auth | Sanctum + Spatie Permissions |
| Queue | Laravel Queue (sync dev, Redis prod) |
| Infra | Docker Compose (PHP-FPM, Nginx, MySQL, Redis) |

## Features

### Core
- **Unified Inbox** — All IG/WA/FB messages in one view
- **Chat View** — Real-time conversation with message bubbles, reply, and Smart-Draft order panel
- **Orders** — Full pipeline: Draft → Confirmed → Dispatched → Delivered/Returned
- **Contacts** — Searchable customer database across all channels
- **Analytics** — Revenue, delivery rate, AOV, top cities, channel breakdown
- **Settings** — Business profile, account, channel token management

### AI Engine
- **GPT-4o-mini extraction** — Parses Albanian/English messages for name, phone, city, address, product intent
- **Regex fallback** — Works without OpenAI key, handles Kosovo phone formats and 30 city names
- **Smart-Draft** — Pre-fills order form from AI extraction data

### Integrations
- **Meta Webhooks** — Instagram DMs, Facebook Messenger (signature-validated)
- **WhatsApp Cloud API** — Inbound messages + outbound notifications
- **Courier Interface** — Pluggable adapter for any shipping provider
- **Notifications** — WhatsApp + SMS order status updates

## Project Structure

```
app/
├── Console/Commands/
│   └── SimulateWebhook.php       # Test webhooks locally
├── Http/Controllers/
│   ├── Auth/AuthController.php   # Login, Register, Logout
│   ├── Api/WebhookController.php # Meta/WhatsApp webhooks
│   ├── DashboardController.php
│   ├── InboxController.php       # + reply endpoint
│   ├── OrderController.php       # + dispatch to courier
│   ├── ContactController.php
│   ├── AnalyticsController.php
│   └── SettingsController.php
├── Jobs/
│   └── SendOutboundMessage.php   # Queued platform API calls
├── Models/
│   ├── User.php, Merchant.php, Contact.php
│   ├── Conversation.php, Message.php, Order.php
├── Services/
│   ├── MessageProcessor.php      # Unified message ingestion
│   ├── AiExtractionService.php   # GPT + regex extraction
│   ├── NotificationService.php   # WA + SMS notifications
│   └── Courier/
│       ├── CourierServiceInterface.php
│       └── DefaultCourierService.php
resources/js/
├── Layouts/AuthenticatedLayout.vue
├── Components/
│   ├── PlatformIcon.vue, StatusBadge.vue, StatCard.vue
├── Pages/
│   ├── Auth/Login.vue, Auth/Register.vue
│   ├── Dashboard.vue
│   ├── Inbox/Index.vue, Inbox/Show.vue
│   ├── Orders/Index.vue, Orders/Show.vue
│   ├── Contacts/Index.vue, Contacts/Show.vue
│   ├── Analytics/Index.vue
│   ├── Settings/Index.vue
│   └── Onboarding.vue
```

## Environment Variables

```env
# Required
APP_KEY=                    # php artisan key:generate

# Optional integrations
OPENAI_API_KEY=             # AI extraction (regex works without it)
META_APP_ID=                # From developers.facebook.com
META_APP_SECRET=            # From developers.facebook.com
META_VERIFY_TOKEN=          # Your custom verify string
META_PAGE_TOKEN=            # Facebook Page access token
WHATSAPP_TOKEN=             # WhatsApp Cloud API token
WHATSAPP_PHONE_ID=          # WhatsApp phone number ID
```

## Development Commands

```bash
# Simulate an incoming message (no Meta API needed)
php artisan nexus:simulate --platform=whatsapp --message="Dua ni bluze, Prishtina"

# Re-seed the database
php artisan migrate:fresh --seed

# Production build
npx vite build

# Docker (production)
docker-compose up -d
```

## License

Proprietary — All rights reserved.
