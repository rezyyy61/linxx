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
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('shareable');
            $table->enum('scope', ['public','friends','private','link'])->default('public');
            $table->string('slug', 64)->unique();
            $table->string('password_hash', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedInteger('max_clicks')->nullable();
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->boolean('allow_repost')->default(true);
            $table->json('extra')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['scope', 'expires_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
