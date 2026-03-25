<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'parent' => $this->whenLoaded('parent', fn() => [
                'id' => $this->parent->id,
                'name' => $this->parent->name,
            ]),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
