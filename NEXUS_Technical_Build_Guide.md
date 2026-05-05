# Technical Implementation Guide: Building NEXUS Step-by-Step
## Engineering Roadmap for Reflektimi Software House

This document provides a sequential, technical breakdown of the development process for the NEXUS Chat-to-Cart CRM.

---

## Phase 1: Environment & Core Infrastructure (Week 1)
**Goal:** Establish a scalable, production-ready foundation.

1.  **Docker Orchestration:** * Set up a `docker-compose` environment with services for: `app` (PHP 8.2-fpm), `web` (Nginx), `db` (MySQL 8.0), and `cache` (Redis).
    * Configure a `worker` container specifically for Laravel Horizon to handle background processes.
2.  **Laravel Scaffolding:**
    * Install Laravel 11.
    * Implement **Laravel Sanctum** for API authentication.
    * Set up **Spatie Laravel Permission** for Role-Based Access Control (Admin vs. Staff).
3.  **Database Architecture:**
    * `merchants`: Profiles, subscription status, courier API keys.
    * `conversations`: External chat IDs, platform source (IG/WA), status.
    * `orders`: Relational table linking customers, products, and courier tracking numbers.

---

## Phase 2: The Connectivity Layer (Weeks 2-3)
**Goal:** Ingesting real-time data from social platforms.

1.  **Meta API Integration (IG/FB):**
    * Create a Meta App and configure Webhooks for `messages` and `messaging_postbacks`.
    * Build a `WebhookController` that validates signatures and dispatches jobs to the Laravel queue.
2.  **WhatsApp Business API:**
    * Integrate via official providers (Twilio or 360dialog) or direct Meta WhatsApp Cloud API.
3.  **Unified Message Processor:**
    * Develop a service that normalizes different payloads (IG vs. WA) into a single `Message` model to be displayed in the dashboard.

---

## Phase 3: The Intelligence Engine (Weeks 4-5)
**Goal:** Turning raw text into actionable data.

1.  **AI Extraction Logic:**
    * Build a service class that sends message snippets to an LLM (via Beam or direct OpenAI API).
    * **Prompt Engineering:** Train the model to return a JSON object containing: `customer_name`, `phone_number`, `city`, `street_address`, and `product_intent`.
2.  **Smart-Draft UI:**
    * In the sidebar of the chat view, create a "Draft Order" component using **Livewire** or **Vue.js**.
    * When a message is received, the AI pre-fills this form. The merchant just clicks "Confirm Order."

---

## Phase 4: Logistics & Courier Integration (Weeks 6-7)
**Goal:** Eliminating manual data entry into courier portals.

1.  **Courier Adapter Pattern:**
    * Create an Interface `CourierServiceInterface`.
    * Implement specific classes for local providers (e.g., `PostaKosovesService`, `PrivateCourierService`).
    * Method: `createShipment(Order $order)` which returns a PDF label and tracking code.
2.  **Label Generation:**
    * Integrate `barryvdh/laravel-dompdf` or a ZPL generator for thermal printing (A6 format).

---

## Phase 5: The "Reflektimi" Aesthetic (Weeks 8-9)
**Goal:** Polishing the UI/UX to a high-end standard.

1.  **Tailwind Cinematic UI:**
    * Apply a dark-themed, glassmorphic design system.
    * Use custom CSS for smooth transitions and "scrollytelling" elements in the onboarding flow.
2.  **Real-Time Feedback:**
    * Use **Laravel Reverb** (or Pusher) to update the inbox and order status in real-time without page refreshes.

---

## Phase 6: Testing, DevOps & Launch (Week 10)
1.  **Automated Testing:** Write Pest/PHPUnit tests for the Webhook logic and Order state transitions.
2.  **Staging Deployment:** Deploy to a staging server (Hetzner/DigitalOcean) using **Laravel Forge** or custom CI/CD pipelines.
3.  **Beta Launch:** Onboard 3-5 local "Power Sellers" to stress-test the AI parsing with real Balkan-slang addresses.

---
**Technical Stack Summary:**
* **Engine:** PHP 8.2 / Laravel 11
* **Frontend:** Tailwind CSS / Livewire
* **Intelligence:** Beam / OpenAI API
* **Ops:** Docker / Redis / MySQL
