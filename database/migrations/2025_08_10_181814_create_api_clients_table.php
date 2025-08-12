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
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('client_id', 80)->unique();
            $table->string('client_secret_hash', 255);
            $table->string('callback_url', 2048)->nullable();

            $table->json('scopes')->nullable();
            $table->boolean('active')->default(true);

            $table->unsignedInteger('rate_limit_per_minute')->default(60);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};
