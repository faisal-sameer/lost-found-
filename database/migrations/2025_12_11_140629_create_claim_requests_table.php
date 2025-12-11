<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')
                ->constrained('lost_items')
                ->cascadeOnDelete();

            $table->string('claim_code')->unique();
            $table->string('claimant_name')->nullable();
            $table->string('claimant_id_number')->nullable();
            $table->string('claimant_phone')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_requests');
    }
};
