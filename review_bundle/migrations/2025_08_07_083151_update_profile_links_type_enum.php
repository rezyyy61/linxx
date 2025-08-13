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
        DB::statement("
            ALTER TABLE profile_links 
            MODIFY COLUMN type 
            ENUM(
                'website', 'email', 'phone',
                'telegram', 'instagram', 'facebook',
                'twitter', 'youtube', 'custom'
            ) 
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE profile_links 
            MODIFY COLUMN type 
            ENUM(
                'website', 'email', 'phone',
                'telegram', 'instagram', 'facebook',
                'twitter', 'custom'
            ) 
            NOT NULL
        ");

    }
};
