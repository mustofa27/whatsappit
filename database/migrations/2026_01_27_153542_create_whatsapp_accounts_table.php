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
        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone_number')->unique();
            $table->string('name')->nullable();
            $table->string('sender_key')->unique();
            $table->string('sender_secret');
            $table->enum('status', ['pending', 'connected', 'disconnected', 'connecting', 'failed'])->default('pending');
            $table->text('qr_code')->nullable();
            $table->text('session_data')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_accounts');
    }
};
