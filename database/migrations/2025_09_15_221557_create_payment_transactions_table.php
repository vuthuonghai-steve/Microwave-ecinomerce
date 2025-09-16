<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->enum('gateway', ['vnpay', 'momo', 'cod']);
            $table->string('txn_ref');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['initiated', 'redirected', 'succeeded', 'failed', 'refunded'])->default('initiated');
            $table->string('response_code')->nullable();
            $table->string('message')->nullable();
            $table->json('raw_request')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'txn_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
