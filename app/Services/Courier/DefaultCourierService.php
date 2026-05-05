<?php

namespace App\Services\Courier;

use App\Models\Order;
use Illuminate\Support\Str;

/**
 * Default courier adapter — generates local tracking numbers and label paths.
 * Replace with real courier API integration (PostaKosovës, DHL, etc.)
 */
class DefaultCourierService implements CourierServiceInterface
{
    public function createShipment(Order $order): array
    {
        $trackingNumber = 'NXC-' . strtoupper(Str::random(10));

        // In production: call the courier API here
        // $response = Http::post('https://courier-api.com/shipments', [...]);

        $order->update([
            'courier_provider' => $this->getProviderName(),
            'tracking_number' => $trackingNumber,
            'fulfillment_status' => 'dispatched',
            'dispatched_at' => now(),
        ]);

        return [
            'tracking_number' => $trackingNumber,
            'label_url' => null, // Will be PDF URL from courier API
        ];
    }

    public function getStatus(string $trackingNumber): array
    {
        // In production: query courier API for real status
        return [
            'status' => 'in_transit',
            'last_update' => now()->toIso8601String(),
            'location' => null,
        ];
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        // In production: call courier cancel API
        return true;
    }

    public function getProviderName(): string
    {
        return 'NEXUS Local';
    }
}
