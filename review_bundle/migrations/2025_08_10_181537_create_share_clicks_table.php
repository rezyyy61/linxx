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
        Schema::create('share_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_id')->constrained('shares')->cascadeOnDelete();

            // زمان کلیک
            $table->timestamp('clicked_at')->useCurrent();

            // داده‌های خام
            $table->ipAddress('ip_address')->nullable()->index();
            $table->string('user_agent', 1024)->nullable();

            // نرمال‌سازی Referrer
            $table->string('referrer_host', 255)->nullable()->index(); // example.com
            $table->text('referrer_path')->nullable();                  // /foo?bar=baz
            $table->binary('referrer_hash', 16)->nullable()->index();   // md5(url, true)

            // UTMها
            $table->string('utm_source', 64)->nullable()->index();
            $table->string('utm_medium', 64)->nullable()->index();
            $table->string('utm_campaign', 64)->nullable()->index();

            // تفکیک اختیاری
            $table->string('country_code', 2)->nullable()->index();
            $table->string('device_type', 32)->nullable(); // mobile/desktop/tablet/...
            $table->string('fingerprint', 128)->nullable(); //

            $table->timestamps();

            $table->index(['share_id', 'clicked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_clicks');
    }
};
