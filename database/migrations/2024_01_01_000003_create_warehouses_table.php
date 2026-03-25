<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('location', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
