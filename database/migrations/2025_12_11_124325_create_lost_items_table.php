<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_point_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_type_id')
                ->nullable()
                ->constrained('item_types')
                ->nullOnDelete();
            $table->string('barcode')->nullable();
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('owner_id_number')->nullable();
            $table->string('owner_phone')->nullable();

            $table->enum('status', ['received', 'delivered'])->default('received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};
