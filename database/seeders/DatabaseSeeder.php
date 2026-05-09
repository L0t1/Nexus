<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Product;
use App\Models\Tag;
use App\Models\AutoTagRule;
use App\Models\ReplyTemplate;
use App\Models\ScheduledMessage;
use App\Models\FollowUpSequence;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Invoice;
use App\Models\CsatSurvey;
use App\Models\OrderReturn;
use App\Models\ActivityLog;
use App\Models\DemandForecast;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Seed Guard ────────────────────────────────────
        $existingAdmin = User::where('email', 'admin@nexus.test')->first();
        $forceSeed = env('FORCE_SEED', false);

        if ($existingAdmin && !$forceSeed) {
            return;
        }

        // If we are re-seeding (Force Seed), clean up the existing demo users/merchants
        if ($existingAdmin && $forceSeed) {
            if ($existingAdmin->merchant) {
                $existingAdmin->merchant->delete(); // Cascades to all related data
            }
            $existingAdmin->delete();
            User::where('email', 'sara@nexus.test')->delete();
        }

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'agent']);

        // ── Users ─────────────────────────────────────────
        $admin = User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@nexus.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // ── Merchant ──────────────────────────────────────
        $merchant = Merchant::create([
            'user_id' => $admin->id,
            'business_name' => 'Prishtina Style Shop',
            'business_slug' => 'prishtina-style-shop',
            'phone' => '+383 44 123 456',
            'email' => 'shop@prishtina-style.com',
            'country' => 'XK',
            'city' => 'Prishtina',
            'address' => 'Rr. Agim Ramadani, Nr. 15',
            'subscription_tier' => 'ultimate',
            'is_active' => true,
            'onboarding_completed' => true,
            'default_currency' => 'EUR',
            'default_tax_rate' => 18.00,
            'tax_label' => 'VAT',
            'tax_inclusive' => false,
        ]);

        $admin->update(['merchant_id' => $merchant->id]);

        // Create a demo agent
        $agent = User::create([
            'name' => 'Sara Agent',
            'email' => 'sara@nexus.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'merchant_id' => $merchant->id,
        ]);
        $agent->assignRole('agent');

        // ── Products ──────────────────────────────────────
        $dbProducts = [
            ['name' => 'Black Hoodie', 'sku' => 'HOODIE-BLK', 'price' => 39.00, 'stock' => 50, 'is_active' => true, 'description' => 'Premium cotton blend hoodie in jet black. Relaxed fit, kangaroo pocket.'],
            ['name' => 'Classic T-Shirt', 'sku' => 'TSHIRT-CLS', 'price' => 19.00, 'stock' => 100, 'is_active' => true, 'description' => 'Everyday essential tee. 100% organic cotton, available in all sizes.'],
            ['name' => 'Denim Jacket', 'sku' => 'JKT-DNM', 'price' => 69.00, 'stock' => 8, 'is_active' => true, 'description' => 'Vintage-wash denim jacket with brass buttons. Timeless layering piece.'],
            ['name' => 'Sneakers Pro', 'sku' => 'SNK-PRO', 'price' => 89.00, 'stock' => 20, 'is_active' => true, 'description' => 'Lightweight performance sneakers with memory foam insole.'],
            ['name' => 'Baseball Cap', 'sku' => 'CAP-BSB', 'price' => 15.00, 'stock' => 75, 'is_active' => true, 'description' => 'Adjustable cotton cap with embroidered logo.'],
            ['name' => 'Cargo Pants', 'sku' => 'PNT-CRG', 'price' => 49.00, 'stock' => 35, 'is_active' => true, 'description' => 'Relaxed-fit cargo pants with utility pockets. Perfect for streetwear looks.'],
            ['name' => 'Crossbody Bag', 'sku' => 'BAG-CRS', 'price' => 29.00, 'stock' => 3, 'is_active' => true, 'description' => 'Compact crossbody bag in vegan leather. Ideal for daily essentials.'],
        ];

        foreach ($dbProducts as $p) {
            Product::create(array_merge($p, ['merchant_id' => $merchant->id]));
        }

        // ── Contacts ──────────────────────────────────────
        $contacts = [];
        $demoContacts = [
            ['name' => 'Arbnora Krasniqi', 'phone' => '+383 44 111 222', 'city' => 'Prishtina', 'platform' => 'instagram', 'total_orders' => 5, 'total_spent' => 245.00],
            ['name' => 'Besart Hoxha', 'phone' => '+383 44 333 444', 'city' => 'Prizren', 'platform' => 'whatsapp', 'total_orders' => 3, 'total_spent' => 168.00],
            ['name' => 'Drita Berisha', 'phone' => '+383 44 555 666', 'city' => 'Peja', 'platform' => 'instagram', 'total_orders' => 8, 'total_spent' => 520.00],
            ['name' => 'Enis Gashi', 'phone' => '+383 44 777 888', 'city' => 'Gjilan', 'platform' => 'facebook', 'total_orders' => 1, 'total_spent' => 39.00],
            ['name' => 'Fjolla Mustafa', 'phone' => '+383 44 999 000', 'city' => 'Ferizaj', 'platform' => 'whatsapp', 'total_orders' => 2, 'total_spent' => 108.00],
            ['name' => 'Genta Shala', 'phone' => '+383 44 222 333', 'city' => 'Prishtina', 'platform' => 'instagram', 'total_orders' => 6, 'total_spent' => 390.00],
            ['name' => 'Haki Mehmeti', 'phone' => '+383 44 444 555', 'city' => 'Mitrovica', 'platform' => 'whatsapp', 'total_orders' => 0, 'total_spent' => 0],
        ];

        foreach ($demoContacts as $data) {
            $contacts[] = Contact::create([
                'merchant_id' => $merchant->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'platform' => $data['platform'],
                'total_orders' => $data['total_orders'],
                'total_spent' => $data['total_spent'],
                'external_id' => 'demo_' . strtolower(str_replace(' ', '_', $data['name'])),
            ]);
        }

        // ── Conversations & Messages ──────────────────────
        $messages = [
            ['Hi! I want to order the black hoodie, size M', 'Of course! Can you send me your address?'],
            ['Po e du ni bluze, sa kushton?', 'Kushton 29€, a doni me porosi?'],
            ['Can I get 2 of the new t-shirts? Send to Peja', 'Sure! Processing your order now.'],
            ['A keni stock ne Large?', 'Po, e kemi! Doni me porosi?'],
            ['Dua me porosi ni jacket, adresa: Ferizaj', 'Perfect, order is confirmed!'],
            ['Hi, do you ship to Prishtina?', 'Yes! Free shipping over €50.'],
            ['Whats new in store?', 'Check our latest arrivals!'],
        ];

        $conversations = [];
        foreach ($contacts as $i => $contact) {
            $conv = Conversation::create([
                'merchant_id' => $merchant->id,
                'contact_id' => $contact->id,
                'platform' => $contact->platform,
                'status' => $i < 3 ? 'active' : 'pending',
                'last_message_preview' => $messages[$i][0],
                'last_message_at' => now()->subMinutes(rand(5, 300)),
                'is_unread' => $i < 2,
                'unread_count' => $i < 2 ? rand(1, 4) : 0,
            ]);
            $conversations[] = $conv;

            Message::create([
                'conversation_id' => $conv->id,
                'contact_id' => $contact->id,
                'direction' => 'inbound',
                'type' => 'text',
                'body' => $messages[$i][0],
                'status' => 'read',
            ]);
            Message::create([
                'conversation_id' => $conv->id,
                'user_id' => $i % 2 === 0 ? $admin->id : $agent->id,
                'direction' => 'outbound',
                'type' => 'text',
                'body' => $messages[$i][1],
                'status' => 'delivered',
            ]);
        }

        // ── Orders (with discount/tax/currency) ───────────
        $orderData = [
            ['status' => 'draft', 'item' => ['name' => 'Black Hoodie', 'qty' => 1, 'price' => 39.00]],
            ['status' => 'confirmed', 'item' => ['name' => 'Classic T-Shirt', 'qty' => 2, 'price' => 19.00]],
            ['status' => 'dispatched', 'item' => ['name' => 'Denim Jacket', 'qty' => 1, 'price' => 69.00]],
            ['status' => 'in_transit', 'item' => ['name' => 'Sneakers Pro', 'qty' => 1, 'price' => 89.00]],
            ['status' => 'delivered', 'item' => ['name' => 'Baseball Cap', 'qty' => 3, 'price' => 15.00]],
            ['status' => 'delivered', 'item' => ['name' => 'Cargo Pants', 'qty' => 1, 'price' => 49.00]],
            ['status' => 'delivered', 'item' => ['name' => 'Black Hoodie', 'qty' => 2, 'price' => 39.00]],
            ['status' => 'returned', 'item' => ['name' => 'Crossbody Bag', 'qty' => 1, 'price' => 29.00]],
            ['status' => 'delivered', 'item' => ['name' => 'Classic T-Shirt', 'qty' => 3, 'price' => 19.00]],
            ['status' => 'confirmed', 'item' => ['name' => 'Sneakers Pro', 'qty' => 1, 'price' => 89.00]],
        ];

        $orders = [];
        foreach ($orderData as $j => $od) {
            $contact = $contacts[$j % count($contacts)];
            $subtotal = $od['item']['qty'] * $od['item']['price'];
            $shipping = 3.00;
            $taxRate = 18.00;
            $taxAmount = round($subtotal * $taxRate / 100, 2);
            $daysAgo = rand(0, 30);

            $order = Order::create([
                'merchant_id' => $merchant->id,
                'contact_id' => $contact->id,
                'conversation_id' => $conversations[$j % count($conversations)]->id ?? null,
                'created_by' => $j % 2 === 0 ? $admin->id : $agent->id,
                'customer_name' => $contact->name,
                'customer_phone' => $contact->phone,
                'shipping_city' => $contact->city,
                'shipping_street' => 'Rr. Demo ' . ($j + 1),
                'items' => [$od['item']],
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'discount_amount' => $j === 2 ? 5.00 : 0,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total' => $subtotal - ($j === 2 ? 5.00 : 0) + $taxAmount + $shipping,
                'currency' => 'EUR',
                'payment_method' => $j % 3 === 0 ? 'bank_transfer' : 'cod',
                'payment_status' => $od['status'] === 'delivered' ? 'collected' : ($od['status'] === 'returned' ? 'refunded' : 'pending'),
                'fulfillment_status' => $od['status'],
                'dispatched_at' => in_array($od['status'], ['dispatched', 'in_transit', 'delivered']) ? now()->subDays($daysAgo) : null,
                'delivered_at' => $od['status'] === 'delivered' ? now()->subDays(max(0, $daysAgo - 2)) : null,
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays(max(0, $daysAgo - 1)),
            ]);
            $orders[] = $order;

            // Audit events
            OrderEvent::log($order, 'created', "Order created by " . ($j % 2 === 0 ? $admin->name : $agent->name) . ".", [], $j % 2 === 0 ? $admin->id : $agent->id);
            if ($od['status'] !== 'draft') {
                OrderEvent::log($order, 'status_change', "Status changed from draft to {$od['status']}.", ['from' => 'draft', 'to' => $od['status']], $admin->id);
            }
        }

        // ── Tags & Segmentation ───────────────────────────
        $tagVip = Tag::create(['merchant_id' => $merchant->id, 'name' => 'VIP', 'color' => '#10b981']);
        $tagNew = Tag::create(['merchant_id' => $merchant->id, 'name' => 'New Customer', 'color' => '#06b6d4']);
        $tagRepeat = Tag::create(['merchant_id' => $merchant->id, 'name' => 'Repeat Buyer', 'color' => '#8b5cf6']);
        $tagPrishtina = Tag::create(['merchant_id' => $merchant->id, 'name' => 'Prishtina Local', 'color' => '#f59e0b']);

        // Assign tags to contacts
        $contacts[0]->tags()->attach([$tagRepeat->id, $tagPrishtina->id]);
        $contacts[2]->tags()->attach([$tagVip->id, $tagRepeat->id]);
        $contacts[3]->tags()->attach([$tagNew->id]);
        $contacts[5]->tags()->attach([$tagVip->id, $tagPrishtina->id]);

        // Auto-tag rules
        AutoTagRule::create(['merchant_id' => $merchant->id, 'tag_id' => $tagVip->id, 'field' => 'total_spent', 'operator' => '>=', 'value' => '300']);
        AutoTagRule::create(['merchant_id' => $merchant->id, 'tag_id' => $tagRepeat->id, 'field' => 'total_orders', 'operator' => '>=', 'value' => '3']);
        AutoTagRule::create(['merchant_id' => $merchant->id, 'tag_id' => $tagPrishtina->id, 'field' => 'city', 'operator' => '==', 'value' => 'Prishtina']);

        // ── Quick Reply Templates ─────────────────────────
        $templates = [
            ['shortcut' => 'greeting', 'name' => 'Welcome Greeting', 'body' => "Hi {customer_name}! 👋 Thank you for reaching out to Prishtina Style Shop. How can I help you today?", 'category' => 'greeting', 'usage_count' => 23],
            ['shortcut' => 'orderconfirm', 'name' => 'Order Confirmation', 'body' => "Your order #{order_number} has been confirmed! ✅ We'll notify you once it's dispatched.", 'category' => 'order', 'usage_count' => 15],
            ['shortcut' => 'shipping', 'name' => 'Shipping Info', 'body' => "We ship across Kosovo with 1-3 day delivery. Standard shipping is €3.00, free on orders over €50! 🚚", 'category' => 'shipping', 'usage_count' => 8],
            ['shortcut' => 'outofstock', 'name' => 'Out of Stock', 'body' => "Unfortunately, that item is currently out of stock. We expect a restock within 5-7 days. Would you like to be notified?", 'category' => 'product', 'usage_count' => 5],
            ['shortcut' => 'thankyou', 'name' => 'Thank You', 'body' => "Thank you for shopping with us, {customer_name}! 🙏 Enjoy your order and don't hesitate to reach out if you need anything.", 'category' => 'general', 'usage_count' => 19],
        ];
        foreach ($templates as $t) {
            ReplyTemplate::create(array_merge($t, ['merchant_id' => $merchant->id]));
        }

        // ── Follow-Up Sequences ───────────────────────────
        FollowUpSequence::create([
            'merchant_id' => $merchant->id,
            'name' => 'Abandoned Cart Recovery',
            'trigger_condition' => 'no_order',
            'delay_hours' => 24,
            'message_template' => "Hi {customer_name}! 👋 We noticed you were interested in some items. Still thinking about it? Here's 10% off with code COMEBACK10!",
            'is_active' => true,
        ]);
        FollowUpSequence::create([
            'merchant_id' => $merchant->id,
            'name' => 'Post-Delivery Check-in',
            'trigger_condition' => 'no_reply',
            'delay_hours' => 48,
            'message_template' => "Hi {customer_name}! Your order has been delivered. How's everything? We'd love to hear your feedback! ⭐",
            'is_active' => true,
        ]);

        // ── Coupons ───────────────────────────────────────
        $coupon1 = Coupon::create([
            'merchant_id' => $merchant->id,
            'code' => 'WELCOME15',
            'name' => 'Welcome Discount',
            'type' => 'percentage',
            'value' => 15,
            'min_order_amount' => 30,
            'max_discount_amount' => 20,
            'usage_limit' => 100,
            'usage_count' => 12,
            'is_active' => true,
            'starts_at' => now()->subDays(30),
            'expires_at' => now()->addDays(60),
        ]);
        Coupon::create([
            'merchant_id' => $merchant->id,
            'code' => 'SUMMER10',
            'name' => 'Summer Sale',
            'type' => 'fixed',
            'value' => 10,
            'min_order_amount' => 50,
            'usage_limit' => 50,
            'usage_count' => 3,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);
        Coupon::create([
            'merchant_id' => $merchant->id,
            'code' => 'COMEBACK10',
            'name' => 'Cart Recovery',
            'type' => 'percentage',
            'value' => 10,
            'usage_limit' => null,
            'usage_count' => 7,
            'is_active' => true,
        ]);

        // ── Invoices ──────────────────────────────────────
        foreach (array_slice($orders, 0, 4) as $order) {
            Invoice::create([
                'merchant_id' => $merchant->id,
                'order_id' => $order->id,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'total' => $order->total,
                'currency' => 'EUR',
                'merchant_details' => [
                    'name' => $merchant->business_name,
                    'phone' => $merchant->phone,
                    'email' => $merchant->email,
                    'city' => $merchant->city,
                    'country' => $merchant->country,
                ],
                'customer_details' => [
                    'name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'city' => $order->shipping_city,
                ],
                'issued_at' => $order->created_at,
                'due_at' => $order->created_at->copy()->addDays(30),
            ]);
        }

        // ── CSAT Surveys ──────────────────────────────────
        $ratings = [5, 4, 5, 3, 4, 5, 2];
        $feedbacks = [
            'Amazing service! Super fast delivery.',
            'Good quality products, will order again.',
            'Love the hoodie! Perfect fit.',
            'Delivery took a bit long but product is ok.',
            'Great customer support, very responsive.',
            'Best online shop in Kosovo! 🔥',
            'Item was slightly different from the photo.',
        ];
        $deliveredOrders = collect($orders)->filter(fn($o) => $o->fulfillment_status === 'delivered')->values();
        foreach ($deliveredOrders->take(5) as $i => $order) {
            CsatSurvey::create([
                'merchant_id' => $merchant->id,
                'order_id' => $order->id,
                'contact_id' => $order->contact_id,
                'rating' => $ratings[$i],
                'feedback' => $feedbacks[$i],
                'status' => 'completed',
                'sent_at' => $order->delivered_at,
                'completed_at' => $order->delivered_at?->addHours(rand(1, 48)),
            ]);
        }
        // One pending survey
        if ($orders[1]) {
            CsatSurvey::create([
                'merchant_id' => $merchant->id,
                'order_id' => $orders[1]->id,
                'contact_id' => $orders[1]->contact_id,
                'status' => 'pending',
                'sent_at' => now()->subDay(),
            ]);
        }

        // ── Returns ───────────────────────────────────────
        $returnedOrder = collect($orders)->firstWhere('fulfillment_status', 'returned');
        if ($returnedOrder) {
            OrderReturn::create([
                'merchant_id' => $merchant->id,
                'order_id' => $returnedOrder->id,
                'contact_id' => $returnedOrder->contact_id,
                'handled_by' => $admin->id,
                'reason' => 'not_as_described',
                'description' => 'Color looks different from the product photo.',
                'status' => 'refunded',
                'refund_method' => 'original',
                'refund_amount' => $returnedOrder->total,
                'approved_at' => now()->subDays(3),
                'received_at' => now()->subDays(1),
                'refunded_at' => now(),
            ]);
        }
        // A pending return
        if (count($orders) > 3) {
            OrderReturn::create([
                'merchant_id' => $merchant->id,
                'order_id' => $orders[3]->id,
                'contact_id' => $orders[3]->contact_id,
                'reason' => 'changed_mind',
                'description' => 'Ordered wrong size, would like to exchange.',
                'status' => 'requested',
            ]);
        }

        // ── Activity Logs ─────────────────────────────────
        $activities = [
            ['user' => $admin, 'action' => 'order.created', 'desc' => 'Created order NX-20260505-ABC12D'],
            ['user' => $agent, 'action' => 'message.sent', 'desc' => 'Sent message to Arbnora Krasniqi'],
            ['user' => $admin, 'action' => 'order.status_changed', 'desc' => 'Changed order status to dispatched'],
            ['user' => $agent, 'action' => 'message.sent', 'desc' => 'Replied to Besart Hoxha'],
            ['user' => $admin, 'action' => 'coupon.created', 'desc' => 'Created coupon WELCOME15'],
            ['user' => $admin, 'action' => 'invoice.generated', 'desc' => 'Generated invoice for order NX-20260508-XYZ'],
            ['user' => $agent, 'action' => 'contact.updated', 'desc' => 'Updated contact Drita Berisha'],
            ['user' => $admin, 'action' => 'return.created', 'desc' => 'Processed return for order NX-20260501-DEF'],
            ['user' => $agent, 'action' => 'csat.sent', 'desc' => 'Sent CSAT survey to Arbnora Krasniqi'],
            ['user' => $admin, 'action' => 'catalog.synced', 'desc' => 'WhatsApp catalog synced: 7 products'],
        ];
        foreach ($activities as $i => $a) {
            ActivityLog::create([
                'merchant_id' => $merchant->id,
                'user_id' => $a['user']->id,
                'action' => $a['action'],
                'description' => $a['desc'],
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subHours(count($activities) - $i),
            ]);
        }

        // ── Demand Forecasts ──────────────────────────────
        $productNames = ['Black Hoodie', 'Classic T-Shirt', 'Denim Jacket', 'Sneakers Pro'];
        foreach ($productNames as $pName) {
            for ($d = 0; $d < 14; $d++) {
                $base = rand(2, 8);
                DemandForecast::create([
                    'merchant_id' => $merchant->id,
                    'product_name' => $pName,
                    'product_id' => Product::where('merchant_id', $merchant->id)->where('name', $pName)->value('id'),
                    'forecast_date' => now()->addDays($d)->format('Y-m-d'),
                    'predicted_demand' => $base,
                    'confidence' => max(30, 90 - ($d * 4)),
                    'trend' => $pName === 'Black Hoodie' ? 'rising' : ($pName === 'Denim Jacket' ? 'falling' : 'stable'),
                ]);
            }
        }
    }
}
