<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity_available' => (float) $this->quantity_available,
            'quantity_reserved' => (float) $this->quantity_reserved,
            'quantity_on_order' => (float) $this->quantity_on_order,
            'available_for_sale' => (float) ($this->quantity_available - $this->quantity_reserved),
            'average_cost' => (float) $this->average_cost,
            'total_value' => (float) ($this->quantity_available * $this->average_cost),
            'last_movement_at' => $this->last_movement_at?->toISOString(),
            'product' => $this->whenLoaded('product', fn() => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
            ]),
            'warehouse' => $this->whenLoaded('warehouse', fn() => [
                'id' => $this->warehouse->id,
                'name' => $this->warehouse->name,
                'code' => $this->warehouse->code,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
