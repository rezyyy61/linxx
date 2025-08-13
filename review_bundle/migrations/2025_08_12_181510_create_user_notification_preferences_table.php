<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->boolean('inapp_enabled')->default(true);
            $table->boolean('toast_enabled')->default(true);
            $table->json('types')->nullable();
            $table->timestamp('mute_until')->nullable();
            $table->time('quiet_hours_from')->nullable();
            $table->time('quiet_hours_to')->nullable();
            $table->string('timezone', 64)->nullable();
            $table->timestamps();

            $table->index(['mute_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
