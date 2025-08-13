<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notification_counters', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('unseen_count')->default(0);
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamps();

            $table->index(['updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_counters');
    }
};
