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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('images')->nullable();
            $table->integer('stock_quantity');
            $table->integer('price'); // Saving price as smallest unit (e.g., cents)
            $table->string('currency')->default('bdt');
            $table->boolean('trending')->default(false);
            $table->json('notify_users')->nullable(); // Assuming user IDs will be stored as JSON array

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
