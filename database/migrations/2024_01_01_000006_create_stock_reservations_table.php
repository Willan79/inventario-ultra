<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('reservation_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity', 15, 4);
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'fulfilled', 'cancelled', 'expired'])->default('active');
            $table->timestamps();

            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('expires_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
