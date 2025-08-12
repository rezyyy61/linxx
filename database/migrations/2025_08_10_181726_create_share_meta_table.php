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
        Schema::create('share_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_id')->constrained('shares')->cascadeOnDelete();

            $table->string('key', 100);
            $table->text('value')->nullable();

            $table->timestamps();

            $table->unique(['share_id','key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_meta');
    }
};
