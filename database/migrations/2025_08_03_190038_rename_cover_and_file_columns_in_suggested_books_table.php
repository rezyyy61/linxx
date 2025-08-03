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
        Schema::table('suggested_books', function (Blueprint $table) {
            $table->renameColumn('cover', 'cover_path');
            $table->renameColumn('file', 'file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suggested_books', function (Blueprint $table) {
            $table->renameColumn('cover_path', 'cover');
            $table->renameColumn('file_path', 'file');
        });
    }
};
