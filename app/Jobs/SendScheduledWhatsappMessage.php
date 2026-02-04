<?php

namespace App\Jobs;

use App\Models\ScheduledMessage;
use App\Services\MetaWhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduledWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ScheduledMessage $scheduledMessage
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mark as processing
        $this->scheduledMessage->markAsProcessing();

        try {
            $account = $this->scheduledMessage->whatsappAccount;
            
            // Initialize WhatsApp service
            $whatsappService = new MetaWhatsappService(
                $account->phone_number_id,
                $account->access_token
            );

            // Send the message
            if ($this->scheduledMessage->template_name) {
                // Send template message
                $response = $whatsappService->sendTemplateMessage(
                    $this->scheduledMessage->recipient_number,
                    $this->scheduledMessage->template_name,
                    $this->scheduledMessage->template_params ?? []
                );
            } else {
                // Send text message
                $response = $whatsappService->sendMessage(
                    $this->scheduledMessage->recipient_number,
                    $this->scheduledMessage->message_content
                );
            }

            // Mark as sent
            $metaMessageId = $response['messages'][0]['id'] ?? null;
            $this->scheduledMessage->markAsSent($metaMessageId);

            Log::info("Scheduled message sent successfully", [
                'scheduled_message_id' => $this->scheduledMessage->id,
                'meta_message_id' => $metaMessageId
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send scheduled message", [
                'scheduled_message_id' => $this->scheduledMessage->id,
                'error' => $e->getMessage(),
                'retry_count' => $this->scheduledMessage->retry_count
            ]);

            // Mark as failed
            $this->scheduledMessage->markAsFailed($e->getMessage());

            // Re-throw if we should retry
            if ($this->scheduledMessage->retry_count < $this->scheduledMessage->max_retries) {
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Scheduled message job failed permanently", [
            'scheduled_message_id' => $this->scheduledMessage->id,
            'error' => $exception->getMessage()
        ]);

        $this->scheduledMessage->markAsFailed($exception->getMessage());
    }
}
