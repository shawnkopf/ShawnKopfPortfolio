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
        Schema::create('quilts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('shopify_order_id');
            $table->bigInteger('shopify_order_item_id');
            $table->string('pattern');
            $table->string('email');
            $table->boolean('has_binding');
            $table->longText('binding_notes')->nullable();
            $table->bigInteger('binding_order_item_id')->nullable();
            $table->string('thread_color');
            $table->boolean('expedited')->default(false);
            $table->integer('length')->nullable();
            $table->integer('width')->nullable();
            $table->string('batting')->nullable();
            $table->bigInteger('backing_order_item_id')->nullable();
            $table->boolean('backing_included')->default(false);
            $table->longText('backing_notes')->nullable();
            $table->integer('sq_in')->nullable();
            $table->longText('order_note')->nullable();
            $table->longText('internal_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quilts');
    }
};
