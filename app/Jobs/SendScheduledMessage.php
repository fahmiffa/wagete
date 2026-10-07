<?php

namespace App\Jobs;

use App\Models\App as ClientApp;
use App\Models\MessageScheduler;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduledMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $schedulerId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsappService $whatsappService): void
    {
        $scheduler = MessageScheduler::with(['templatePesan', 'contact'])->find($this->schedulerId);

        if (! $scheduler) {
            Log::warning('SendScheduledMessage: Scheduler not found', ['id' => $this->schedulerId]);
            return;
        }

        // Skip if already sent or not pending/processing
        if (! in_array($scheduler->status, ['pending', 'processing'])) {
            Log::info('SendScheduledMessage: Skipping non-pending scheduler', [
                'id' => $scheduler->id,
                'status' => $scheduler->status,
            ]);
            return;
        }

        // Mark as processing
        $scheduler->update(['status' => 'processing']);

        // Resolve device_id from user's App
        $app = ClientApp::where('user_id', $scheduler->user_id)->first();

        if (! $app || empty($app->nomor_hp)) {
            $scheduler->update([
                'status' => 'failed',
                'catatan' => 'Gagal: Tidak ada device/nomor HP yang terhubung untuk user ini.',
            ]);
            Log::error('SendScheduledMessage: No device found for user', [
                'scheduler_id' => $scheduler->id,
                'user_id' => $scheduler->user_id,
            ]);
            return;
        }

        $deviceId = $app->nomor_hp;
        $phone = $scheduler->contact->phone;
        $message = $scheduler->templatePesan->pesan;

        try {
            $result = $whatsappService->sendMessage($deviceId, $phone, $message);

            if ($result['success']) {
                $scheduler->update([
                    'status' => 'sent',
                    'catatan' => 'Pesan berhasil dikirim pada ' . now()->format('d M Y H:i:s'),
                ]);

                Log::info('SendScheduledMessage: Message sent successfully', [
                    'scheduler_id' => $scheduler->id,
                    'phone' => $phone,
                ]);
            } else {
                $errorMsg = $result['data']['message'] ?? 'Unknown error';
                $scheduler->update([
                    'status' => 'failed',
                    'catatan' => 'Gagal: ' . $errorMsg,
                ]);

                Log::error('SendScheduledMessage: API returned error', [
                    'scheduler_id' => $scheduler->id,
                    'error' => $errorMsg,
                ]);
            }
        } catch (\Exception $e) {
            $scheduler->update([
                'status' => 'failed',
                'catatan' => 'Error: ' . $e->getMessage(),
            ]);

            Log::error('SendScheduledMessage: Exception', [
                'scheduler_id' => $scheduler->id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to allow retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        $scheduler = MessageScheduler::find($this->schedulerId);

        if ($scheduler) {
            $scheduler->update([
                'status' => 'failed',
                'catatan' => 'Job gagal setelah ' . $this->tries . ' percobaan: ' . ($exception?->getMessage() ?? 'Unknown error'),
            ]);
        }

        Log::error('SendScheduledMessage: Job failed permanently', [
            'scheduler_id' => $this->schedulerId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
