<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('sku', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('unit_of_measure', ['unit', 'kg', 'liter', 'meter', 'box'])->default('unit');
            $table->string('barcode', 50)->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('min_stock_level')->default(0);
            $table->unsignedInteger('max_stock_level')->nullable();
            $table->unsignedInteger('reorder_point')->default(0);
            $table->enum('cost_method', ['fifo', 'lifo', 'average', 'standard'])->default('average');
            $table->timestamps();
            $table->softDeletes();

            $table->index('sku');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('reorder_point');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
