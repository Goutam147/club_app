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
        // 1. Fee Types Table (Fee Heads / Fee Manager)
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['monthly', 'event', 'general'])->default('general');
            $table->decimal('default_amount', 10, 2)->default(0.00);
            $table->date('due_date')->nullable();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Transaction Items Table (Itemized Line Items)
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('fee_type_id')->nullable()->constrained('fee_types')->onDelete('set null');
            $table->unsignedTinyInteger('month')->nullable(); // 1 to 12
            $table->unsignedSmallInteger('year')->nullable(); // e.g. 2026
            $table->string('title')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('fee_types');
    }
};
