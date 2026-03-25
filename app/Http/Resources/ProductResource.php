<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'unit_of_measure' => $this->unit_of_measure,
            'barcode' => $this->barcode,
            'is_active' => $this->is_active,
            'min_stock_level' => $this->min_stock_level,
            'max_stock_level' => $this->max_stock_level,
            'reorder_point' => $this->reorder_point,
            'cost_method' => $this->cost_method,
            'inventories' => InventoryResource::collection($this->whenLoaded('inventories')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
