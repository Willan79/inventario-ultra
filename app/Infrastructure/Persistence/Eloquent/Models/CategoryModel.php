<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryModel extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CategoryModel::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
