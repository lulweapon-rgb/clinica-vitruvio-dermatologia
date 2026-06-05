<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('trusted_device_hash')->nullable();
            $table->timestamp('trusted_device_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret', 
                'two_factor_enabled', 
                'trusted_device_hash', 
                'trusted_device_expires_at'
            ]);
        });
    }
};