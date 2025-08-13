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
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('api_client_id')
                ->nullable()
                ->constrained('api_clients')
                ->nullOnDelete();

            $table->string('url', 2048);

            $table->binary('url_hash', 16)->storedAs('(unhex(md5(`url`)))');

            $table->json('events'); // ["shares.created","shares.clicked","shares.revoked",...]
            $table->string('secret', 255)->nullable();

            $table->boolean('active')->default(true);

            $table->unsignedSmallInteger('last_status_code')->nullable();
            $table->timestamp('last_delivered_at')->nullable();
            $table->unsignedInteger('failure_count')->default(0);

            $table->timestamps();

            $table->unique(['api_client_id','url_hash'], 'webhook_endpoints_client_urlhash_unique');

            $table->index(['active']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};
