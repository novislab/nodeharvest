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
        Schema::create('vps', function (Blueprint $table) {
            $table->id();
            $table->string('server_id')->nullable()->unique();
            $table->string('name');
            $table->string('status')->default('pending');
            $table->string('datacenter');
            $table->string('disk_src_0');
            $table->string('cpu');
            $table->integer('ram');
            $table->text('password');
            $table->string('billing');
            $table->integer('traffic');
            $table->integer('disk_size_0');
            $table->string('network_name_0');
            $table->string('network_ip_0')->nullable();
            $table->integer('network_bits_0')->nullable();
            $table->boolean('managed')->default(false);
            $table->boolean('backup')->default(false);
            $table->boolean('power')->default(true);
            $table->text('selected_ssh_key_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps');
    }
};
