<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity_available', 15, 4)->default(0);
            $table->decimal('quantity_reserved', 15, 4)->default(0);
            $table->decimal('quantity_on_order', 15, 4)->default(0);
            $table->decimal('average_cost', 15, 4)->default(0);
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']);
            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index(['quantity_available', 'quantity_reserved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
