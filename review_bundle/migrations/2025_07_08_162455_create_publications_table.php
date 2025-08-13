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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('political_profile_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('title');
            $table->string('issue')->nullable();
            $table->text('description')->nullable();
            $table->date('published_at')->nullable();

            $table->string('file_path');
            $table->string('file_type');
            $table->string('cover_path')->nullable();

            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
