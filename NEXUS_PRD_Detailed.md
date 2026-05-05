# Product Requirements Document (PRD): NEXUS
## The Chat-to-Cart E-Commerce & Logistics CRM for the Balkan Market

**Version:** 1.0
**Status:** Strategic Draft
**Author:** Reflektimi Software House

---

## 1. Executive Summary
**NEXUS** is a localized, high-end Micro-SaaS platform designed to solve the "Conversational Commerce" bottleneck in Kosovo and the wider Balkan region. It bridges the gap between informal social media sales (Instagram, WhatsApp, Viber) and structured logistics/Cash on Delivery (COD) workflows. 

By automating order extraction, courier dispatch, and customer notifications, NEXUS enables local SMEs to scale from a few daily orders to hundreds without increasing administrative overhead.

---

## 2. The Problem Statement
Despite high internet penetration, e-commerce in Kosovo is fragmented.
1.  **Communication Chaos:** Orders are scattered across DMs. Sellers lose track of customer details in long chat threads.
2.  **Manual Logistics:** Data is manually transcribed from chats into courier portals or physical notebooks, leading to frequent shipping errors.
3.  **High RTO (Return to Origin):** Without automated tracking updates, customers often forget their "Cash on Delivery" orders or are not home during delivery, hurting merchant margins.
4.  **Operational Ceiling:** Small businesses cannot grow because the manual labor required to manage chat-orders becomes a physical limit.

---

## 3. Goals & Strategic Objectives
* **Centralize:** Consolidate all sales channels into one "Single Source of Truth."
* **Automate:** Use AI to turn a messy chat message into a structured shipping label in seconds.
* **Reduce Friction:** Integrate directly with local courier APIs to eliminate manual data entry.
* **Professionalize:** Provide a high-end UI/UX that gives local merchants the same power as global enterprises.

---

## 4. Key Features

### 4.1. Omnichannel Unified Inbox
* **Integration:** Connect Instagram DMs, Facebook Messenger, and WhatsApp Business API.
* **Interface:** A sleek, dark-themed dashboard where all conversations are threaded by customer.

### 4.2. AI-Powered "Smart-Draft" Order Engine
* **Intent Recognition:** Detect when a customer provides an address or phone number.
* **Address Parsing:** Automatically extract City, Neighborhood, Street, and Phone Number from informal Albanian/English text.
* **One-Click Approval:** Convert a chat message into a "Pending Order" with a single click.

### 4.3. Local Logistics Integration
* **Courier Sync:** Direct API hooks into major local and regional couriers.
* **Label Printing:** Generate thermal printer-ready waybills (A6 format) directly from the dashboard.
* **COD Management:** Track the status of Cash on Delivery payments from the courier back to the merchant.

### 4.4. Automated Customer Journey
* **WhatsApp/SMS Notifications:** Automatic triggers for "Order Received," "Dispatched," and "Out for Delivery."
* **Branded Tracking Page:** A cinematic, mobile-optimized page where customers can see their order's progress.

---

## 5. Technical Specifications & Architecture

### 5.1. Tech Stack
* **Backend:** PHP 8.x with **Laravel**. Utilizing Laravel Horizon for managing high-volume webhook queues from Meta and Couriers.
* **Frontend:** **Tailwind CSS** with a focus on Glassmorphism and Cinematic UI. (No heavy legacy frameworks to ensure maximum speed).
* **Database:** MySQL with optimized indexing for rapid order searching.
* **Infrastructure:** **Dockerized** deployment for scalability and security.

### 5.2. Security & Compliance
* **GDPR Alignment:** Since the target market seeks European standards, data encryption at rest and in transit is mandatory.
* **Role-Based Access:** Different permissions for "Admin" (Owners) and "Agents" (Staff answering chats).

---

## 6. Design & User Experience (UX)
* **Aesthetic:** Cinematic, high-contrast dark mode.
* **Performance:** All actions must have <200ms perceived latency.
* **Scrollytelling Elements:** Used in the merchant onboarding flow to explain features visually.

---

## 7. Sales & Business Model

### 7.1. Pricing Tiers
* **Starter (€49/mo):** Up to 250 orders, 2 chat channels.
* **Growth (€129/mo):** Unlimited orders, full AI parsing, and SMS automation.
* **Enterprise (€5,000+):** Custom white-label instances for large retailers with existing ERPs.

### 7.2. Go-to-Market Strategy
1.  **The "Top 50" Campaign:** Direct outreach to the 50 most active Instagram shops in Kosovo.
2.  **Logistics Partnerships:** Partner with local courier companies to offer NEXUS as a "preferred vendor" for their merchants.
3.  **Reflektimi Showcase:** Leverage a high-end scrollytelling landing page to demonstrate technical superiority.

---

## 8. Success Metrics (KPIs)
* **Merchant Efficiency:** Goal of reducing order processing time by 75%.
* **RTO Reduction:** Lowering "Return to Origin" rates for COD by at least 15% through better notifications.
* **Platform Stickiness:** 80% of merchants logging in at least 3 times per day.

---
**Next Steps:** Proceed to UI Wireframing and API mapping for local courier services.
