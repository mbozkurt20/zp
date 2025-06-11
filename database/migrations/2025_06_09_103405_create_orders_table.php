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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('basket_id')->constrained('baskets');
            $table->decimal('total',15,2)->default(0.00);
            $table->decimal('discount',15,2)->default(0.00);
            $table->float('tax')->default(0);
            $table->boolean('is_paid')->default(false);
            $table->enum('payment_type', ['cash', 'card'])->default('cash');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
