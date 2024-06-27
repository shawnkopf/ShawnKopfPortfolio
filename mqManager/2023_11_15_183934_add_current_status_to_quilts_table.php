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
        Schema::table('quilts', function (Blueprint $table) {
            $table->string('last_update')->nullable();
            $table->integer('last_update_id')->nullable();
            $table->timestamp('received')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quilts', function (Blueprint $table) {
            //
        });
    }
};
