# NEXUS CRM

> Chat-to-Cart e-commerce platform designed for the Balkan market. Turns Instagram, WhatsApp, and Facebook DMs into trackable orders with AI-powered address extraction.

Nexus CRM is built on a modern monolithic architecture using the VILT stack, emphasizing performance, real-time reactivity, and multi-tenant security.

## Documentation

Comprehensive documentation for both Users and Developers can be found in the `docs` directory:

- 📖 **[User Guide](./docs/USER_GUIDE.md)**: Details on getting started, managing orders, using the AI Inbox, and inventory management.
- 💻 **[Developer Guide](./docs/DEVELOPER_GUIDE.md)**: Information on system architecture, local setup, multi-tenancy, AI extraction pipelines, and frontend reactivity.

---

## Quick Start (Local Development)

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment Configuration
cp .env.example .env
php artisan key:generate

# 3. Database & Seeding (Includes roles & demo user)
php artisan migrate --seed
```

Start the development servers (requires 2 separate terminals):
```bash
npx vite           # Terminal 1 - Frontend HMR & Tailwind Compilation
php artisan serve  # Terminal 2 - Backend Server
```

**Demo Account:**
- **Email:** `admin@nexus.test`
- **Password:** `password`
