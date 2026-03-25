<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseModel extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'location',
        'is_active',
        'manager_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'manager_id' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(InventoryModel::class, 'warehouse_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(MovementModel::class, 'warehouse_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
