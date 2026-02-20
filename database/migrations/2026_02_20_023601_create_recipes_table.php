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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('preset_id');
            $table->string('location', 2);
            $table->integer('os_id');
            $table->integer('software_id')->nullable();
            $table->integer('traffic_plan_id');
            $table->enum('deploy_period', ['monthly', 'quarterly', 'semi-annually', 'annually'])->default('monthly');
            $table->text('ssh_key')->nullable();
            $table->text('post_install_script')->nullable();
            $table->string('post_install_callback')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
