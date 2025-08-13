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
        Schema::create('suggested_books', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('author')->nullable();
            $table->string('translator')->nullable();
            $table->text('description')->nullable();

            $table->string('link')->nullable();
            $table->string('cover')->nullable();
            $table->string('file')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggested_books');
    }
};
