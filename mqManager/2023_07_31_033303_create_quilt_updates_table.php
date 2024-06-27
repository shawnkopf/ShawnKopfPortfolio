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
        Schema::create('quilt_updates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('status');
            $table->string('location');
            $table->longText('notes');
            $table->string('img')->nullable();
            $table->string('path')->nullable();
            $table->integer('quilt_id');
            $table->integer('admin_id');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quilt_updates');
    }
};
