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
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            // Add direction field (incoming or outgoing)
            $table->enum('direction', ['incoming', 'outgoing'])->default('outgoing')->after('whatsapp_account_id');
            
            // Rename recipient_number to contact_number (works for both directions)
            $table->dropColumn('recipient_number');
            $table->string('contact_number')->after('direction');
            
            // Add sender/receiver info
            $table->string('sender_number')->nullable()->after('contact_number');
            $table->string('receiver_number')->nullable()->after('sender_number');
            
            // Add external Meta message ID
            $table->string('external_id')->nullable()->after('error_message');
            
            // Add message type
            $table->string('message_type')->default('text')->after('message'); // text, image, document, audio, video, etc
            
            // Add timestamp from Meta
            $table->timestamp('received_at')->nullable()->after('sent_at');
            
            // Add metadata (for media info, etc)
            $table->json('metadata')->nullable()->after('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn([
                'direction',
                'contact_number',
                'sender_number',
                'receiver_number',
                'external_id',
                'message_type',
                'received_at',
                'metadata',
            ]);
            $table->string('recipient_number')->after('whatsapp_account_id');
        });
    }
};
