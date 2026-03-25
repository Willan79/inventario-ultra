<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'movement_type' => $this->movement_type,
            'movement_type_label' => $this->getMovementTypeLabel(),
            'quantity' => (float) $this->quantity,
            'previous_quantity' => (float) $this->previous_quantity,
            'new_quantity' => (float) $this->new_quantity,
            'quantity_difference' => (float) ($this->new_quantity - $this->previous_quantity),
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'unit_cost' => $this->unit_cost ? (float) $this->unit_cost : null,
            'total_cost' => $this->total_cost ? (float) $this->total_cost : null,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
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
        ];
    }

    private function getMovementTypeLabel(): string
    {
        return match($this->movement_type) {
            'in' => 'Entrada',
            'out' => 'Salida',
            'transfer' => 'Transferencia',
            'adjustment' => 'Ajuste',
            'return' => 'Devolución',
            default => $this->movement_type,
        };
    }
}
