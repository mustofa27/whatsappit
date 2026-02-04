<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledWhatsappMessage;
use App\Models\ScheduledMessage;
use Illuminate\Console\Command;

class ProcessScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:process-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Process scheduled WhatsApp messages that are ready to send';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing scheduled messages...');

        // Get messages ready to send
        $messages = ScheduledMessage::readyToSend()->get();

        if ($messages->isEmpty()) {
            $this->info('No messages ready to send.');
            return 0;
        }

        $count = 0;
        foreach ($messages as $message) {
            // Dispatch job with rate limiting (1 message per account per second)
            SendScheduledWhatsappMessage::dispatch($message)
                ->delay(now()->addSeconds($count));
            
            $count++;
        }

        $this->info("Dispatched {$count} scheduled messages to queue.");
        return 0;
    }
}
