<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.base_url', 'https://gowa.qlabcode.com');
        $this->username = config('services.whatsapp.username', 'ffa');
        $this->password = config('services.whatsapp.password', 'qqffa');
    }

    /**
     * Create an authenticated HTTP client with device header
     */
    protected function client(string $deviceId): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->withHeaders(['X-Device-Id' => $deviceId])
            ->timeout(15)
            ->connectTimeout(10);
    }

    /**
     * Check device connection status
     *
     * @return array{success: bool, status: int, data: array}
     */
    public function checkStatus(string $deviceId): array
    {
        try {
            $response = $this->client($deviceId)->get("{$this->baseUrl}/app/status");

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp checkStatus error', [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 0,
                'data' => [
                    'code' => 'CONNECTION_ERROR',
                    'message' => 'Gagal terhubung ke server WhatsApp Gateway: ' . $e->getMessage(),
                    'results' => ['device_id' => $deviceId],
                ],
            ];
        }
    }

    /**
     * Register a new device
     *
     * @return array{success: bool, status: int, data: array}
     */
    public function addDevice(string $deviceId): array
    {
        try {
            $response = $this->client($deviceId)->post("{$this->baseUrl}/devices", [
                'device_id' => $deviceId,
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp addDevice error', [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 0,
                'data' => [
                    'code' => 'CONNECTION_ERROR',
                    'message' => 'Gagal mendaftarkan device: ' . $e->getMessage(),
                    'results' => ['device_id' => $deviceId],
                ],
            ];
        }
    }

    /**
     * Request QR code login for a device
     *
     * @return array{success: bool, status: int, data: array}
     */
    public function login(string $deviceId): array
    {
        try {
            $response = $this->client($deviceId)->get("{$this->baseUrl}/devices/{$deviceId}/login");

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp login error', [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 0,
                'data' => [
                    'code' => 'CONNECTION_ERROR',
                    'message' => 'Gagal meminta QR login: ' . $e->getMessage(),
                    'results' => ['device_id' => $deviceId],
                ],
            ];
        }
    }

    /**
     * Ensure device exists, auto-create if 404
     * Returns the final status check result
     *
     * @return array{success: bool, status: int, data: array}
     */
    public function ensureDeviceAndGetStatus(string $deviceId): array
    {
        $statusResult = $this->checkStatus($deviceId);

        // If device not found (404), auto-create then re-check
        if ($statusResult['status'] === 404) {
            $addResult = $this->addDevice($deviceId);

            if (! $addResult['success']) {
                return [
                    'success' => false,
                    'status' => $addResult['status'],
                    'data' => $addResult['data'],
                ];
            }

            // Re-check status after adding device
            $statusResult = $this->checkStatus($deviceId);
        }

        return $statusResult;
    }

    /**
     * Format a phone number to WhatsApp JID format (628xxx@s.whatsapp.net)
     *
     * Supports input formats:
     *  - 085173156513    → 6285173156513@s.whatsapp.net
     *  - +6285173156513  → 6285173156513@s.whatsapp.net
     *  - 6285173156513   → 6285173156513@s.whatsapp.net
     *  - 6285173156513@s.whatsapp.net → unchanged
     */
    public function formatPhoneToJid(string $phone): string
    {
        // Strip all non-digit characters except @
        $phone = trim($phone);

        // Already in JID format
        if (str_ends_with($phone, '@s.whatsapp.net')) {
            return $phone;
        }

        // Remove everything except digits
        $digits = preg_replace('/\D/', '', $phone);

        // Convert leading 0 → 62 (Indonesian local format)
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits . '@s.whatsapp.net';
    }

    /**
     * Send a WhatsApp text message
     *
     * @param  string  $deviceId  The X-Device-Id header value (active device)
     * @param  string  $phone     Recipient phone number (any format, will be auto-converted to JID)
     * @param  string  $message   The message text to send
     * @return array{success: bool, status: int, data: array}
     */
    public function sendMessage(string $deviceId, string $phone, string $message): array
    {
        try {
            $response = $this->client($deviceId)->post("{$this->baseUrl}/send/message", [
                'phone'   => $this->formatPhoneToJid($phone),
                'message' => $message,
            ]);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'data'    => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp sendMessage error', [
                'device_id' => $deviceId,
                'phone'     => $phone,
                'error'     => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => 0,
                'data'    => [
                    'code'    => 'CONNECTION_ERROR',
                    'message' => 'Gagal mengirim pesan WhatsApp: ' . $e->getMessage(),
                    'results' => ['device_id' => $deviceId],
                ],
            ];
        }
    }
}
