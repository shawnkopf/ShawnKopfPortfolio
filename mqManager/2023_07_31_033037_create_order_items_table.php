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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('shopify_order_id');
            $table->timestamp('order_date');
            $table->bigInteger('shopify_order_item_id');
            $table->string('variant_id')->nullable();
            $table->string('title');
            $table->integer('quantity');
            $table->string('sku');
            $table->string('variant_title')->nullable();
            $table->string('vendor')->nullable();
            $table->string('fulfillment_service')->nullable();
            $table->bigInteger('product_id');
            $table->boolean('gift_card');
            $table->string('name')->nullable();
            $table->string('properties')->nullable();
            $table->integer('grams');
            $table->integer('price');
            $table->integer('total_discount');
            $table->string('fulfillment_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
