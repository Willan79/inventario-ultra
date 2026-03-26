<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'sku',
        'name',
        'description',
        'category_id',
        'unit_of_measure',
        'barcode',
        'is_active',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'cost_method',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'reorder_point' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'unit_of_measure' => 'unit',
        'cost_method' => 'average',
        'min_stock_level' => 0,
        'reorder_point' => 0,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(InventoryModel::class, 'product_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(MovementModel::class, 'product_id');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(SupplierModel::class, 'product_supplier', 'product_id', 'supplier_id')
            ->withPivot(['supplier_sku', 'cost_price', 'lead_time_days', 'is_preferred'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%");
        });
    }
}
