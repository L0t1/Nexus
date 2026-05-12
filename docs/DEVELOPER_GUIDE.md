# Nexus CRM - Developer Documentation

Nexus CRM is built on a modern monolithic architecture using the VILT stack, emphasizing performance, real-time reactivity, and multi-tenant security.

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Local Development Setup](#local-development-setup)
3. [Multi-Tenancy & Authorization](#multi-tenancy--authorization)
4. [Intelligent Order & Inventory Management](#intelligent-order--inventory-management)
5. [AI Extraction & Smart-Draft Engine](#ai-extraction--smart-draft-engine)
6. [Frontend & Real-Time Reactivity](#frontend--real-time-reactivity)
7. [Integrations & Webhooks](#integrations--webhooks)

---

## System Architecture

### Core Stack
- **Backend**: Laravel 13, PHP 8.2
- **Frontend**: Vue 3 (Composition API), Inertia.js, Tailwind CSS 4
- **Database**: SQLite (Local Dev) / MySQL 8 (Production)
- **Auth & Authorization**: Laravel Sanctum + Spatie Permissions (`admin` vs `agent` roles)

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

## Multi-Tenancy & Authorization
- **Tenant Context:** Users belong to a `Merchant`. Most records (Orders, Products, Contacts, Conversations) are scoped strictly to the `merchant_id`.
- **Subscription Gates:** Feature access is controlled via `Merchant::hasFeature()`. Subscriptions strictly enforce tiers (Starter, Pro, Ultimate). Features are verified gracefully on backend controllers, and the frontend dynamically locks unavailable UI components, offering up-sells to `/subscription`.
- **Roles**: Spatie Permissions are used to differentiate `admin` and `agent` capabilities.

## Intelligent Order & Inventory Management
- **Lifecycle:** Orders transition through `draft` -> `confirmed` -> `dispatched` -> `delivered` / `returned` / `cancelled`.
- **Dynamic Stock linking:** Orders are directly linked to the `Product` models.
  - When an order transitions to **Delivered**, stock is automatically decremented.
  - When an order transitions to **Returned** or **Cancelled**, stock is safely incremented back.
- **Constraints:** Payment statuses strictly enforce `pending`, `collected`, `returned`, and `refunded` via database CHECK constraints (aligned with the Cash on Delivery model). 
  - *Note*: Due to strict SQLite CHECK constraints on Enums (like `payment_status`), always use proper Laravel Migration conventions if modifying core enum columns, or recreate the tables entirely during local development (`migrate:fresh`).
- **Invoices**: Professional print-ready CSS styling is implemented for dynamic PDF invoice generation.

## AI Extraction & Smart-Draft Engine
- **GPT-4o-mini Integration:** The `AiExtractionService` securely processes incoming chat texts.
- **Data Parsing:** It identifies Name, Phone Number, City, Street Address, and Product Intent using strict JSON output formatting.
- **Zero-Touch Pipeline**: Auto-creates and tags draft orders with AI-generated metadata, providing a "View Chat" link for verifying data.
- **Frontend Hydration:** Extracted data is injected into Inertia's payload, allowing the Vue frontend to auto-fill the "Smart Order" drawer seamlessly.

## Frontend & Real-Time Reactivity
- **UI Components**: A centralized component library is used (e.g., `NexusButton`, `NexusInput`) for consistent, high-performance UI primitives with built-in error handling and state management.
- **Inertia Polling:** The `Inbox` and `Sidebar` use lightweight Inertia partial reloads (`router.reload({ only: ['messages', 'orders', 'stats'] })`) to fetch live updates without refreshing the DOM.
- **Dynamic Stats:** Red/yellow indicator badges (unread conversations, pending orders) in the navigation sidebar are purely driven by backend metrics shared globally via `HandleInertiaRequests`.
- **Landing Page**: Implemented as a high-performance Inertia/Vue component (`Welcome.vue`) unifying the tech stack and providing a polished UI.

## Integrations & Webhooks
- **Meta Integrations:** The platform ingests incoming requests from Meta (Instagram & Facebook Messenger) via `WebhookController`. Verify tokens are validated via standard SHA256 HMAC signatures.
- **WhatsApp Cloud API:** Standardized connection for DMs and outbound templated notifications.
