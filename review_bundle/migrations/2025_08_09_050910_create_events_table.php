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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();

            $table->morphs('creator');
            $table->foreignId('created_by_user_id')->constrained('users');

            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();

            $table->string('type', 64)->nullable();
            $table->string('mode', 32)->nullable();

            $table->string('visibility', 16)->default('public');
            $table->string('status', 16)->default('draft');

            $table->boolean('allow_comments')->default(true);

            $table->string('timezone', 64)->default('UTC');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('rsvp_deadline')->nullable();

            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('requires_approval')->default(false);

            $table->string('venue_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->unsignedInteger('going_count')->default(0);
            $table->unsignedInteger('interested_count')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['starts_at']);
            $table->index(['status','visibility']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
