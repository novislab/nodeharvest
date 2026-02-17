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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('node_id')->unique();
            $table->string('name');
            $table->text('api_key');
            $table->string('status')->default('offline');
            $table->string('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->decimal('total_earnings', 20, 8)->default(0);
            $table->decimal('unsettled_earnings', 20, 8)->default(0);
            $table->integer('sessions')->default(0);
            $table->string('session_time')->default('0d. 00:00:00');
            $table->string('transferred')->default('0 MB');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
