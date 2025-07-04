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
        Schema::create('profile_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('political_profile_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['website', 'email', 'phone', 'telegram', 'instagram', 'facebook', 'twitter', 'custom']);
            $table->string('title')->nullable();
            $table->string('url');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_links');
    }
};
