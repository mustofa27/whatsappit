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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('group')->default('general'); // general, whatsapp, analytics, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'cost_per_message',
                'value' => '500',
                'type' => 'integer',
                'group' => 'analytics',
                'description' => 'Estimated cost per outgoing message (in IDR)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_name',
                'value' => 'WhatsApp IT',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Company or application name',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
