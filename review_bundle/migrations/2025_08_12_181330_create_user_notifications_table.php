<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 64);
            $table->morphs('subject');
            $table->string('group_key', 191)->nullable();
            $table->string('title', 160)->nullable();
            $table->text('body')->nullable();
            $table->string('action_url', 2048)->nullable();
            $table->json('actors')->nullable();
            $table->unsignedInteger('count')->default(1);
            $table->string('level', 16)->default('info');
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'seen_at']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'group_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
