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
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_account_id')->constrained()->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('category')->default('MARKETING'); // MARKETING, UTILITY, AUTHENTICATION
            $table->string('language')->default('en');
            $table->text('header_content')->nullable();
            $table->string('header_type')->nullable(); // TEXT, IMAGE, VIDEO, DOCUMENT
            $table->text('body_content');
            $table->text('footer_content')->nullable();
            $table->json('buttons')->nullable(); // Call to action, Quick reply buttons
            $table->json('variables')->nullable(); // Track variable placeholders {{1}}, {{2}}, etc.
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->string('meta_template_id')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['whatsapp_account_id', 'status']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
