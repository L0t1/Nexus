<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'agent']);

        // Create demo admin user
        $user = User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@nexus.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('admin');

        // Create demo merchant
        $merchant = Merchant::create([
            'user_id' => $user->id,
            'business_name' => 'Prishtina Style Shop',
            'business_slug' => 'prishtina-style-shop',
            'phone' => '+383 44 123 456',
            'email' => 'shop@prishtina-style.com',
            'country' => 'XK',
            'city' => 'Prishtina',
            'subscription_tier' => 'growth',
            'is_active' => true,
        ]);

        // Create demo contacts
        $contacts = [];
        $demoContacts = [
            ['name' => 'Arbnora Krasniqi', 'phone' => '+383 44 111 222', 'city' => 'Prishtina', 'platform' => 'instagram'],
            ['name' => 'Besart Hoxha', 'phone' => '+383 44 333 444', 'city' => 'Prizren', 'platform' => 'whatsapp'],
            ['name' => 'Drita Berisha', 'phone' => '+383 44 555 666', 'city' => 'Peja', 'platform' => 'instagram'],
            ['name' => 'Enis Gashi', 'phone' => '+383 44 777 888', 'city' => 'Gjilan', 'platform' => 'facebook'],
            ['name' => 'Fjolla Mustafa', 'phone' => '+383 44 999 000', 'city' => 'Ferizaj', 'platform' => 'whatsapp'],
        ];

        foreach ($demoContacts as $data) {
            $contacts[] = Contact::create([
                'merchant_id' => $merchant->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'platform' => $data['platform'],
                'external_id' => 'demo_' . strtolower(str_replace(' ', '_', $data['name'])),
            ]);
        }

        // Create demo conversations with messages
        $messages = [
            ['Hi! I want to order the black hoodie, size M', 'Of course! Can you send me your address?'],
            ['Po e du ni bluze, sa kushton?', 'Kushton 29€, a doni me porosi?'],
            ['Can I get 2 of the new t-shirts? Send to Peja, Lagja Dardania', 'Sure! Processing your order now.'],
            ['A keni stock ne Large?', 'Po, e kemi! Doni me porosi?'],
            ['Dua me porosi ni jacket, adresa: Ferizaj, rr. Enver Topalli nr 5', 'Perfect, order is confirmed!'],
        ];

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

            // Inbound message
            Message::create([
                'conversation_id' => $conv->id,
                'contact_id' => $contact->id,
                'direction' => 'inbound',
                'type' => 'text',
                'body' => $messages[$i][0],
                'status' => 'read',
            ]);

            // Outbound reply
            Message::create([
                'conversation_id' => $conv->id,
                'user_id' => $user->id,
                'direction' => 'outbound',
                'type' => 'text',
                'body' => $messages[$i][1],
                'status' => 'delivered',
            ]);
        }

        // Create demo orders
        $statuses = ['draft', 'confirmed', 'dispatched', 'in_transit', 'delivered', 'delivered', 'returned'];
        $products = [
            ['name' => 'Black Hoodie', 'qty' => 1, 'price' => 39.00],
            ['name' => 'Classic T-Shirt', 'qty' => 2, 'price' => 19.00],
            ['name' => 'Denim Jacket', 'qty' => 1, 'price' => 69.00],
            ['name' => 'Sneakers Pro', 'qty' => 1, 'price' => 89.00],
            ['name' => 'Baseball Cap', 'qty' => 3, 'price' => 15.00],
        ];

        foreach ($statuses as $j => $status) {
            $contact = $contacts[$j % count($contacts)];
            $item = $products[$j % count($products)];
            $subtotal = $item['qty'] * $item['price'];
            $shipping = 3.00;

            Order::create([
                'merchant_id' => $merchant->id,
                'contact_id' => $contact->id,
                'created_by' => $user->id,
                'customer_name' => $contact->name,
                'customer_phone' => $contact->phone,
                'shipping_city' => $contact->city,
                'items' => [$item],
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'payment_method' => 'cod',
                'payment_status' => $status === 'delivered' ? 'collected' : 'pending',
                'fulfillment_status' => $status,
                'dispatched_at' => in_array($status, ['dispatched', 'in_transit', 'delivered']) ? now()->subDays(rand(1, 5)) : null,
                'delivered_at' => $status === 'delivered' ? now()->subDays(rand(0, 2)) : null,
                'created_at' => now()->subDays(rand(0, 14)),
            ]);
        }
    }
}
