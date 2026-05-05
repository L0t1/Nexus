<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rules = [
    'customer_name' => 'required|string|max:255',
    'customer_phone' => ['required', 'string', 'regex:/^[\d\s\+\-\(\)]+$/', 'max:50'],
    'shipping_city' => 'required|string|max:255',
    'shipping_neighborhood' => 'nullable|string|max:255',
    'shipping_street' => 'required|string|max:500',
    'shipping_notes' => 'nullable|string|max:1000',
    'items' => 'required|array|min:1',
    'items.*.name' => 'required|string|max:255',
    'items.*.qty' => 'required|integer|min:1',
    'items.*.price' => 'required|numeric|min:0.01',
    'shipping_cost' => 'nullable|numeric|min:0',
    'payment_method' => 'required|in:cod,bank_transfer,online',
    'contact_id' => 'nullable|exists:contacts,id',
    'conversation_id' => 'nullable|exists:conversations,id',
    'internal_notes' => 'nullable|string|max:2000',
];

$messages = [
    'items.*.name.required' => 'Product name is required.',
];

$payload = [
    'customer_name' => 'Test',
    'customer_phone' => '123456',
    'shipping_city' => 'City',
    'shipping_street' => 'Street',
    'shipping_cost' => 3,
    'payment_method' => 'cod',
    'items' => [['name' => '', 'qty' => 1, 'price' => 10]],
];

$validator = \Illuminate\Support\Facades\Validator::make($payload, $rules, $messages);
echo json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT);
