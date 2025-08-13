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
        Schema::create('share_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('share_id')->constrained('shares')->cascadeOnDelete();
            $table->morphs('targetable'); // targetable_type, targetable_id
            $table->enum('permission_type', ['read','comment','repost'])->default('read');
            $table->timestamps();

            $table->unique(['share_id','targetable_type','targetable_id','permission_type'], 'share_perm_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_permissions');
    }
};
