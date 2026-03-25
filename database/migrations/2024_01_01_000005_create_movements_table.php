<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->enum('movement_type', ['in', 'out', 'transfer', 'adjustment', 'return']);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 4)->nullable();
            $table->decimal('previous_quantity', 15, 4);
            $table->decimal('new_quantity', 15, 4);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at');

            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('movement_type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
            $table->index('created_by');
            $table->index(['product_id', 'warehouse_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
