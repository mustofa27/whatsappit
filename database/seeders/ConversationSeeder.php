<?php

namespace Database\Seeders;

use App\Models\WhatsappAccount;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Models\WhatsappContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConversationSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed conversations and messages for testing
     */
    public function run(): void
    {
        // Get or create a WhatsApp account
        $account = WhatsappAccount::firstOrCreate([
            'phone_number' => '628123456789',
        ], [
            'user_id' => 1,
            'name' => 'Test Account',
            'phone_number_id' => '980422438489752',
            'waba_id' => '114567812345678',
            'access_token' => 'test_token_' . uniqid(),
            'sender_key' => 'sk_' . Str::random(32),
            'sender_secret' => 'ss_' . Str::random(40),
            'is_verified' => true,
            'status' => 'connected',
            'provider' => 'meta',
        ]);

        // Sample contacts
        $contacts = [
            [
                'number' => '628765432101',
                'name' => 'John Doe',
                'messages' => 8,
            ],
            [
                'number' => '628765432102',
                'name' => 'Sarah Smith',
                'messages' => 5,
            ],
            [
                'number' => '628765432103',
                'name' => 'Michael Johnson',
                'messages' => 12,
            ],
            [
                'number' => '628765432104',
                'name' => 'Emma Wilson',
                'messages' => 3,
            ],
            [
                'number' => '628765432105',
                'name' => 'David Brown',
                'messages' => 15,
            ],
        ];

        // Sample messages
        $sampleMessages = [
            // Incoming messages
            'Hi, how can I help you?',
            'I\'m interested in your product',
            'Can you send me more details?',
            'What\'s the pricing?',
            'Is it available now?',
            'Thanks for the quick response',
            'I\'ll think about it',
            'When can you deliver?',
            'Do you offer payment plans?',
            'I\'d like to place an order',
            'Great! How do I proceed?',
            'Thank you for your help',
            
            // Outgoing messages
            'Hello! Thank you for contacting us',
            'Sure, I\'d be happy to help',
            'Here are the details you requested',
            'Our current price is $99/month',
            'Yes, it\'s available immediately',
            'You\'re welcome! Let me know if you need anything else',
            'No problem, take your time',
            'We can deliver within 2-3 business days',
            'We offer flexible payment options',
            'Perfect! Let me process that for you',
            'You\'re all set. Check your email for confirmation',
            'Have a great day!',
        ];

        // Create conversations and messages
        foreach ($contacts as $contact) {
            // Create contact record for Contact Management
            WhatsappContact::updateOrCreate(
                [
                    'whatsapp_account_id' => $account->id,
                    'contact_number' => $contact['number'],
                ],
                [
                    'name' => $contact['name'],
                    'email' => strtolower(str_replace(' ', '.', $contact['name'])) . '@example.com',
                    'address' => 'Sample address for ' . $contact['name'],
                    'tags' => ['VIP', 'Support'],
                ]
            );

            $conversation = WhatsappConversation::create([
                'whatsapp_account_id' => $account->id,
                'contact_number' => $contact['number'],
                'contact_name' => $contact['name'],
                'unread_count' => rand(0, 3),
                'is_archived' => false,
            ]);

            // Generate messages
            $now = now();
            for ($i = $contact['messages']; $i > 0; $i--) {
                $direction = rand(0, 1) ? 'incoming' : 'outgoing';
                $timestamp = $now->copy()->subMinutes($i * 5);

                // Randomly select a sample message
                $messageText = $sampleMessages[array_rand($sampleMessages)];

                WhatsappMessage::create([
                    'whatsapp_account_id' => $account->id,
                    'contact_number' => $contact['number'],
                    'direction' => $direction,
                    'sender_number' => $direction === 'incoming' ? $contact['number'] : $account->phone_number,
                    'receiver_number' => $direction === 'incoming' ? $account->phone_number : $contact['number'],
                    'message' => $messageText,
                    'message_type' => 'text',
                    'status' => $direction === 'incoming' ? 'delivered' : $this->randomStatus(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'sent_at' => $direction === 'outgoing' ? $timestamp : null,
                    'received_at' => $direction === 'incoming' ? $timestamp : null,
                ]);
            }

            // Update last_message_at on conversation
            $lastMessage = WhatsappMessage::where('whatsapp_account_id', $account->id)
                ->where('contact_number', $contact['number'])
                ->latest('created_at')
                ->first();

            if ($lastMessage) {
                $conversation->update([
                    'last_message_at' => $lastMessage->created_at,
                ]);
            }
        }

        $this->command->info('Conversation seeder completed!');
        $this->command->info('Created ' . count($contacts) . ' conversations with test data');
    }

    /**
     * Randomly return a message status
     */
    protected function randomStatus(): string
    {
        $statuses = ['sent', 'delivered', 'read', 'pending'];
        return $statuses[array_rand($statuses)];
    }
}
