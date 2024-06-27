<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ez_cater_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('entity_id');
            $table->string('entity_type');
            $table->timestamp('occurred_at');
            $table->string('uuid');
            $table->string('order_source_type')->nullable();
            $table->string('event_order_type')->nullable();
            $table->timestamp('event_handoff_time')->nullable();
            $table->string('caterer_uuid')->nullable();
            $table->boolean('is_live')->nullable();
            $table->integer('order_total')->nullable();
            $table->integer('order_tax')->nullable();
            $table->integer('order_tip')->nullable();
            $table->json('fees_and_discounts')->nullable();
            $table->string('item_name')->nullable();
            $table->string('item_uuid')->nullable();
            $table->integer('item_cost')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ez_cater_orders');
    }
};
