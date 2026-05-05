# Nexus — Production & Go-Live Checklist

Everything you need to do before real users can use Nexus in production.
Items are ordered by dependency — complete each section before moving to the next.

---

## 1. Meta App Setup (Facebook Developer Console)

### 1.1 — Business Verification
- [ ] Go to [business.facebook.com](https://business.facebook.com) → **Settings → Business Info**
- [ ] Complete **Business Verification** (upload business registration documents)
- [ ] This is required to unlock advanced permissions and go live. Takes 1–5 business days.

### 1.2 — App Basic Settings
- [ ] Go to [developers.facebook.com](https://developers.facebook.com) → your Nexus app
- [ ] Set **App Display Name** to "Nexus" (or your brand name)
- [ ] Set **App Category** to "Business"
- [ ] Upload an **App Icon** (1024×1024 PNG)
- [ ] Fill in **Privacy Policy URL** (required before app review) — e.g. `https://yourdomain.com/privacy`
- [ ] Fill in **Terms of Service URL** — e.g. `https://yourdomain.com/terms`
- [ ] Add your production domain under **App Domains** — e.g. `yourdomain.com`

### 1.3 — Facebook Login (for merchant OAuth)
- [ ] In your app → **Add Product → Facebook Login → Settings**
- [ ] Under **Valid OAuth Redirect URIs** add: `https://yourdomain.com/auth/meta/callback`
- [ ] Enable **"Login with the JavaScript SDK"**
- [ ] Add your production domain to **Allowed Domains**
- [ ] Submit the following permissions for **App Review**:
  - `pages_messaging` — to send/receive messages on behalf of Pages
  - `pages_manage_metadata` — to subscribe Pages to the webhook
  - `pages_read_engagement` — to read Page info
  - `instagram_basic` — to access linked Instagram accounts
  - `instagram_manage_messages` — to send/receive Instagram messages
- [ ] For each permission in App Review: provide a **screencast** showing the flow, and a **written explanation** of how Nexus uses it

### 1.4 — Webhooks (Messenger / Instagram)
- [ ] In your app → **Messenger → Settings → Webhooks**
- [ ] Set Callback URL: `https://yourdomain.com/api/webhooks/meta`
- [ ] Set Verify Token: the value of `META_VERIFY_TOKEN` in your `.env`
- [ ] Subscribe to these fields:
  - `messages`
  - `messaging_postbacks`
  - `messaging_deliveries`
  - `messaging_reads`
- [ ] Do the same under **Instagram → Settings → Webhooks** if using Instagram
- [ ] Subscribe each merchant's Page to the webhook after they connect (this currently happens automatically via your OAuth flow when permissions are granted)

### 1.5 — WhatsApp Setup
- [ ] In your app → **WhatsApp → API Setup**
- [ ] Complete **Embedded Signup** configuration:
  - Go to **WhatsApp → Embedded Signup**
  - Create a new configuration
  - Add your domain to allowed domains
  - Copy the **Configuration ID** → set as `META_WHATSAPP_CONFIG_ID` in `.env`
- [ ] Set Webhook URL: `https://yourdomain.com/api/webhooks/whatsapp`
- [ ] Subscribe to `messages` field
- [ ] Submit for App Review:
  - `whatsapp_business_management` — to manage WABAs for merchants
  - `whatsapp_business_messaging` — to send messages
- [ ] Note: During App Review, test using your own dev WABA. Real merchants can only onboard after review is approved.

### 1.6 — App Review Submission
- [ ] Switch app from **Development** to **Live** mode (only after all permissions are approved)
- [ ] In Development mode, only users explicitly added as Testers/Developers can use the app
- [ ] Add testers under: App → **Roles → Testers** for pre-launch testing

---

## 2. Domain & Hosting

- [ ] Register a domain (e.g. `nexusapp.io`)
- [ ] Point DNS to your server (A record)
- [ ] Set up **SSL/TLS certificate** — use Let's Encrypt (free) via Certbot or your hosting provider
  - Meta requires HTTPS for all OAuth and webhook URLs — no exceptions
- [ ] Configure Nginx to redirect all HTTP → HTTPS
- [ ] Add SSL certificate paths to `docker/nginx/default.conf`

---

## 3. Server

- [ ] Provision a VPS (recommended: **DigitalOcean, Hetzner, or Vultr**)
  - Minimum: 2 vCPU, 4 GB RAM for a small production load
- [ ] Install Docker and Docker Compose on the server
- [ ] Open firewall ports: **80** (HTTP) and **443** (HTTPS) only
  - Port 3306 (MySQL) and 6379 (Redis) must NOT be publicly accessible — they stay inside the Docker network (already configured in `docker-compose.prod.yml`)
- [ ] Set up a non-root user with sudo access
- [ ] Configure SSH key authentication, disable password login

---

## 4. Environment Configuration (`.env` for production)

Change these from their dev values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database — MySQL (not SQLite)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=nexus
DB_USERNAME=nexus
DB_PASSWORD=STRONG_RANDOM_PASSWORD_HERE
DB_ROOT_PASSWORD=STRONG_RANDOM_ROOT_PASSWORD_HERE

# Session
SESSION_DRIVER=redis
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

# Cache & Queue
CACHE_STORE=redis
QUEUE_CONNECTION=redis          # Jobs run via worker, not sync

# Redis
REDIS_PASSWORD=STRONG_RANDOM_REDIS_PASSWORD_HERE

# Mail — use a real provider (Resend, Postmark, Mailgun, SES)
MAIL_MAILER=resend              # or postmark / mailgun / ses
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="Nexus"
RESEND_API_KEY=re_xxxxx

# Meta
META_APP_ID=your_app_id
META_APP_SECRET=your_app_secret
META_VERIFY_TOKEN=strong_random_string_NOT_nexus-verify-token
META_PAGE_TOKEN=                # Leave empty — merchants connect via OAuth
META_WHATSAPP_CONFIG_ID=your_embedded_signup_config_id

# OpenAI
OPENAI_API_KEY=sk-xxxx
```

---

## 5. Email

- [ ] Set up a transactional email provider (**Resend**, Postmark, or Mailgun — all have free tiers)
- [ ] Verify your sending domain (add SPF, DKIM, DMARC DNS records)
- [ ] This is required for:
  - Email verification when merchants register
  - Password reset
- [ ] Test that registration emails actually arrive (check spam folder first time)

---

## 6. Code — Before Deploying

- [ ] Run full test suite: `php artisan test`
- [ ] Set `APP_DEBUG=false` — never expose stack traces to users in production
- [ ] Generate a fresh `APP_KEY`: `php artisan key:generate`
- [ ] Confirm `META_VERIFY_TOKEN` is a strong random string, not `nexus-verify-token`
- [ ] Review all `abort_unless` authorization checks in controllers
- [ ] Run `php artisan route:list` and confirm no unintended public routes exist
- [ ] Remove or restrict any test/scratch files (`scratch/` directory)

---

## 7. Deployment

```bash
# On the server
git clone https://github.com/youruser/nexus.git /var/www/nexus
cd /var/www/nexus

# Copy and fill production .env
cp .env.example .env
nano .env

# Start with production overrides
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

# Run migrations
docker compose exec app php artisan migrate --force

# Cache config, routes, views for performance
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Start the queue worker (processes outbound messages, AI extraction)
docker compose exec app php artisan queue:work --daemon
```

---

## 8. Queue Worker (Critical)

The `QUEUE_CONNECTION=redis` in production means outbound messages and AI extraction run in background jobs. Without a running worker, nothing gets processed.

- [ ] Confirm the `worker` service is defined in `docker-compose.yml`
- [ ] In `docker-compose.prod.yml`, `worker` replicas is set to 2 — this is your worker process
- [ ] Set up **Supervisor** inside the container to keep the worker alive if it crashes (already configured in `docker/Dockerfile` via the supervisor install)
- [ ] Create `/etc/supervisor/conf.d/nexus-worker.conf`:
  ```ini
  [program:nexus-worker]
  command=php /var/www/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
  autostart=true
  autorestart=true
  user=www-data
  numprocs=2
  ```

---

## 9. Post-Deployment Checks

- [ ] Visit `https://yourdomain.com` — should load with valid SSL (green padlock)
- [ ] Register a test merchant account — confirm verification email arrives
- [ ] Connect Facebook via OAuth — confirm page token saved correctly
- [ ] Send a test message from your phone to the connected Facebook/Instagram page — confirm it appears in Nexus inbox
- [ ] Reply from Nexus — confirm the message arrives on the customer's end
- [ ] Check `storage/logs/laravel.log` for any errors
- [ ] Verify Meta webhook is receiving events: Meta Developer Console → your app → Webhooks → **Send Test**

---

## 10. Ongoing Maintenance

- [ ] **Token refresh**: WhatsApp user tokens expire. For production, create a **System User** token in Meta Business Manager (never expires) and store it as `whatsapp_token` in the merchant's credentials
- [ ] **Database backups**: Set up automated daily backups of the MySQL volume
- [ ] **Log rotation**: Configure `LOG_CHANNEL=daily` in production `.env` so logs don't grow forever
- [ ] **SSL renewal**: Let's Encrypt certificates expire every 90 days — set up auto-renewal via Certbot
- [ ] **Monitor failed jobs**: Check `php artisan queue:failed` periodically or set up `QUEUE_FAILED_DRIVER=database` and alert on failures

---

## Summary — Critical Path to Go Live

```
Business Verification → SSL + Domain → Meta App Review (permissions) 
→ Production .env → Deploy with Docker → Test webhook end-to-end → Go Live
```

The single biggest blocker is **Meta App Review** — it typically takes **3–7 business days** and requires business verification to be complete first. Start that process as early as possible.
