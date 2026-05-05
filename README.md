# NEXUS CRM - Developer Documentation

> Chat-to-Cart e-commerce platform designed for the Balkan market. Turns Instagram, WhatsApp, and Facebook DMs into trackable orders with AI-powered address extraction.

## System Architecture

Nexus CRM is built on a modern monolithic architecture using the VILT stack, emphasizing performance, real-time reactivity, and multi-tenant security.

### Core Stack
- **Backend**: Laravel 13, PHP 8.2
- **Frontend**: Vue 3 (Composition API), Inertia.js, Tailwind CSS 4
- **Database**: SQLite (Local Dev) / MySQL 8 (Production)
- **Auth & Authorization**: Laravel Sanctum + Spatie Permissions (`admin` vs `agent` roles)

---

## Technical Features & Workflows

### 1. Multi-Tenancy & Subscriptions
- **Tenant Context:** Users belong to a `Merchant`. Most records (Orders, Products, Contacts, Conversations) are scoped strictly to the `merchant_id`.
- **Subscription Gates:** Feature access is controlled via `Merchant::hasFeature()`. The system strictly enforces the following tiers:
  - **Starter**: Up to 500 orders/mo, 1 Channel Integration, Basic Analytics.
  - **Pro**: Unlimited orders, All Channel Integrations, Product & Stock Management, Advanced Analytics.
  - **Ultimate**: All Pro features, plus AI Chat Extraction, Priority Inbox Sorting, and Marketing Broadcasts.
- **Enforcement:** Features are verified gracefully on the backend controllers, and the frontend dynamically locks unavailable UI components, offering seamless up-sells directly to the `/subscription` route.

### 2. Intelligent Order & Inventory Management
- **Lifecycle:** Orders move through statuses (`draft` -> `confirmed` -> `dispatched` -> `delivered` / `returned` / `cancelled`).
- **Dynamic Stock linking:** Orders are directly linked to the `Product` models.
  - When an order transitions to **Delivered**, stock is automatically decremented.
  - When an order transitions to **Returned** or **Cancelled**, stock is safely incremented back.
- **Constraints:** Payment statuses strictly enforce `pending`, `collected`, `returned`, and `refunded` via database CHECK constraints (aligned with the Cash on Delivery model common in the Balkans).

### 3. AI Extraction & Smart-Draft Engine
- **GPT-4o-mini Integration:** The `AiExtractionService` securely processes incoming chat texts.
- **Data Parsing:** It identifies Name, Phone Number, City, Street Address, and Product Intent using strict JSON output formatting.
- **Frontend Hydration:** Extracted data is injected into Inertia's payload, allowing the Vue frontend to auto-fill the "Smart Order" drawer seamlessly.

### 4. Real-time Frontend Reactivity
- **Inertia Polling:** The `Inbox` and `Sidebar` use lightweight Inertia partial reloads (`router.reload({ only: ['messages', 'orders', 'stats'] })`) to fetch live updates without refreshing the DOM.
- **Dynamic Stats:** Red/yellow indicator badges (unread conversations, pending orders) in the navigation sidebar are purely driven by backend metrics shared globally via `HandleInertiaRequests`.

---

## Local Development Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment Configuration
cp .env.example .env
php artisan key:generate

# 3. Database & Seeding (Includes roles & demo user)
php artisan migrate --seed

# 4. Start Development Servers (Requires 2 separate terminals)
npx vite           # Terminal 1 - Frontend HMR & Tailwind Compilation
php artisan serve  # Terminal 2 - Backend Server
```

**Demo Account:**
- **Email:** `admin@nexus.test`
- **Password:** `password`

## Integrations & Webhooks
- **Meta Integrations:** The platform ingests incoming requests from Meta (Instagram & Facebook Messenger) via `WebhookController`. Verify tokens are validated via standard SHA256 HMAC signatures.
- **WhatsApp Cloud API:** Standardized connection for DMs and outbound templated notifications.

## Database Management
- Due to strict SQLite CHECK constraints on Enums (like `payment_status`), always use proper Laravel Migration conventions if modifying core enum columns, or recreate the tables entirely during local development (`migrate:fresh`).

## Roles
- **Admin**: System configuration, subscription management, full product catalog control, and analytics access.
- **Agent**: Day-to-day operations—messaging in the Inbox, drafting orders, and updating shipment statuses.
