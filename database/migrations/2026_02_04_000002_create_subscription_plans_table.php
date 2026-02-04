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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->text('description')->nullable();
            $table->json('features');
            $table->integer('max_accounts')->nullable();
            $table->integer('max_users')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        
        // Insert default plans
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Starter',
                'price' => 500000,
                'description' => 'Perfect for small businesses getting started',
                'features' => json_encode([
                    '1 WhatsApp Account',
                    'Message Scheduling',
                    'Basic Templates',
                    'Basic Analytics',
                    'Email Support',
                ]),
                'max_accounts' => 1,
                'max_users' => 1,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business',
                'price' => 1500000,
                'description' => 'For growing businesses with multiple accounts',
                'features' => json_encode([
                    'Up to 3 WhatsApp Accounts',
                    'Advanced Scheduling',
                    'Template Management',
                    'Advanced Analytics',
                    'Priority Support',
                    'Contact Management',
                ]),
                'max_accounts' => 3,
                'max_users' => 5,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'price' => 5000000,
                'description' => 'For large organizations with unlimited needs',
                'features' => json_encode([
                    'Unlimited WhatsApp Accounts',
                    'All Features Included',
                    'Custom Integrations',
                    'Dedicated Support',
                    'Team Collaboration',
                    'API Access',
                    'Custom Reports',
                ]),
                'max_accounts' => null,
                'max_users' => null,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
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
        Schema::dropIfExists('subscription_plans');
    }
};
