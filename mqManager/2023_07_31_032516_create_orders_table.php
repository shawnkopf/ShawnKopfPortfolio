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
            $table->timestamps();
            $table->bigInteger('shopify_id');
            $table->string('email');
            $table->bigInteger('customer_id');
            $table->timestamp('shopify_created_at');
            $table->timestamp('shopify_updated_at');
            $table->timestamp('shopify_closed_at')->nullable();
            $table->bigInteger('number');
            $table->longText('note')->nullable();
            $table->boolean('test');
            $table->integer('total_price');
            $table->integer('subtotal_price');
            $table->integer('total_weight');
            $table->integer('total_tax');
            $table->boolean('taxes_included');
            $table->string('financial_status');
            $table->boolean('confirmed');
            $table->integer('total_discounts');
            $table->integer('total_line_items_price');
            $table->string('name');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->string('source_name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->string('tags')->nullable();
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
