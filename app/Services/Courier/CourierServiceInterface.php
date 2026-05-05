<?php

namespace App\Services\Courier;

use App\Models\Order;

interface CourierServiceInterface
{
    /**
     * Create a shipment with the courier and get tracking info.
     * @return array{tracking_number: string, label_url: string|null}
     */
    public function createShipment(Order $order): array;

    /**
     * Get the current status of a shipment.
     * @return array{status: string, last_update: string|null, location: string|null}
     */
    public function getStatus(string $trackingNumber): array;

    /**
     * Cancel a shipment.
     */
    public function cancelShipment(string $trackingNumber): bool;

    /**
     * Get the courier provider name.
     */
    public function getProviderName(): string;
}
