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
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            // Provider information
            $table->string('provider')->default('meta')->after('status'); // meta, twilio, evolution
            $table->string('phone_number_id')->nullable()->after('provider'); // Meta's phone number ID
            $table->string('waba_id')->nullable()->after('phone_number_id'); // WhatsApp Business Account ID
            $table->text('access_token')->nullable()->after('waba_id'); // Meta access token
            
            // Verification
            $table->boolean('is_verified')->default(false)->after('access_token');
            $table->string('verification_code')->nullable()->after('is_verified');
            $table->timestamp('verification_code_sent_at')->nullable()->after('verification_code');
            
            // External tracking
            $table->string('external_id')->nullable()->after('verification_code_sent_at');
            
            // Remove Evolution-specific fields (make nullable for migration)
            $table->text('session_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'phone_number_id',
                'waba_id',
                'access_token',
                'is_verified',
                'verification_code',
                'verification_code_sent_at',
                'external_id',
            ]);
        });
    }
};
