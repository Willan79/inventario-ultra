<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovementModel extends Model
{
    use HasFactory;

    protected $table = 'movements';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'product_id',
        'warehouse_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'previous_quantity',
        'new_quantity',
        'notes',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'warehouse_id' => 'integer',
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'previous_quantity' => 'decimal:4',
        'new_quantity' => 'decimal:4',
        'reference_id' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WarehouseModel::class, 'warehouse_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeInbound($query)
    {
        return $query->whereIn('movement_type', ['in', 'return']);
    }

    public function scopeOutbound($query)
    {
        return $query->where('movement_type', 'out');
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
