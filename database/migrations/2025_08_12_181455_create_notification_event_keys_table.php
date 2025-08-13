<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_event_keys', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('event_uid', 191);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 64);
            $table->timestamps();

            $table->unique(['event_uid', 'user_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_event_keys');
    }
};
