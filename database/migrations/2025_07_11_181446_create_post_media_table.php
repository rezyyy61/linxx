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
        Schema::create('post_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')
                ->constrained()
                ->onDelete('cascade');

            $table->enum('type', ['image', 'video', 'file', 'audio']);
            $table->string('path')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedInteger('duration')->nullable();

            $table->json('meta')->nullable();
            $table->json('meta_tmp')->nullable();

            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->text('error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_media');
    }
};
