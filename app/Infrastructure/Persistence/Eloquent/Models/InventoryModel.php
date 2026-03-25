<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class InventoryModel extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity_available',
        'quantity_reserved',
        'quantity_on_order',
        'average_cost',
        'last_movement_at',
    ];

    protected $casts = [
        'quantity_available' => 'decimal:4',
        'quantity_reserved' => 'decimal:4',
        'quantity_on_order' => 'decimal:4',
        'average_cost' => 'decimal:4',
        'last_movement_at' => 'datetime',
    ];

    protected $attributes = [
        'quantity_available' => 0,
        'quantity_reserved' => 0,
        'quantity_on_order' => 0,
        'average_cost' => 0,
    ];

    protected $appends = ['available_for_sale'];

    protected function availableForSale(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_available - $this->quantity_reserved,
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseModel::class, 'warehouse_id');
    }

    public function scopeBelowReorderPoint($query)
    {
        return $query->whereRaw('(quantity_available - quantity_reserved) <= products.reorder_point')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereColumn('inventories.warehouse_id', 'inventories.warehouse_id');
    }

    public function scopeLowStock($query, int $threshold = 0)
    {
        return $query->whereRaw('(quantity_available - quantity_reserved) <= ?', [$threshold]);
    }
}
