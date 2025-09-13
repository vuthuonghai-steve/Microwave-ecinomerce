<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('stock_on_hand');
            $table->unsignedInteger('stock_reserved');
            $table->timestamps();
            $table->primary('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};

