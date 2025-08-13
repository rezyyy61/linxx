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
        Schema::create('political_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('group_name');
            $table->string('tagline')->nullable();
            $table->enum('entity_type', ['party', 'collective', 'media', 'individual']);
            $table->string('location')->nullable();
            $table->year('founded_year')->nullable();
            $table->string('logo_path')->nullable();

            $table->text('about')->nullable();
            $table->text('goals')->nullable();
            $table->text('activities')->nullable();
            $table->text('structure')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('political_profiles');
    }
};
