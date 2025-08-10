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
        Schema::table('events', function (Blueprint $table) {
            $table->string('type_custom')->nullable()->after('type');
            $table->string('platform')->nullable()->after('mode');
            $table->string('meeting_link', 512)->nullable()->after('platform');
            $table->string('access_code', 128)->nullable()->after('meeting_link');
            $table->dropColumn('cover_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['type_custom','platform','meeting_link','access_code']);
            $table->string('cover_image_url')->nullable();
        });
    }
};
