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
        Schema::table('political_profiles', function (Blueprint $table) {
            $table->string('pending_entity_type')->nullable()->after('entity_type');
            $table->boolean('entity_type_approved')->default(true)->after('pending_entity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('political_profiles', function (Blueprint $table) {
            $table->dropColumn(['pending_entity_type', 'entity_type_approved']);
        });
    }
};
