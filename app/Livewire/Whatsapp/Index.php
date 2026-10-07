<?php

namespace App\Livewire\Whatsapp;

use App\Models\App as ClientApp;
use App\Services\WhatsappService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    // Device info
    public ?string $deviceId = null;
    public ?string $appName = null;

    // Status
    public bool $isConnected = false;
    public bool $isLoggedIn = false;
    public string $jid = '';
    public string $statusMessage = 'Memuat status perangkat...';
    public string $statusCode = 'LOADING';

    // QR Login
    public bool $showQr = false;
    public string $qrLink = '';
    public int $qrDuration = 30;
    public int $pollCountdown = 0;

    // Error
    public ?string $errorMessage = null;

    // Loading states
    public bool $isLoading = true;
    public bool $isConnecting = false;

    public function mount(): void
    {
        $this->resolveDeviceId();

        if ($this->deviceId) {
            $this->checkDeviceStatus();
        }
    }

    /**
     * Resolve device_id from the logged-in user's App (nomor_hp)
     */
    protected function resolveDeviceId(): void
    {
        $user = auth()->user();

        // Admin (role 0): use first app or own user's app
        // Regular user (role 1): use their own app
        $app = ClientApp::where('user_id', $user->id)->first();

        if (! $app) {
            $this->statusCode = 'NO_APP';
            $this->statusMessage = 'Belum ada aplikasi yang terkait dengan akun Anda.';
            $this->isLoading = false;
            return;
        }

        if (empty($app->nomor_hp)) {
            $this->statusCode = 'NO_PHONE';
            $this->statusMessage = 'Nomor HP belum diisi pada aplikasi "' . $app->name . '". Silakan update data App terlebih dahulu.';
            $this->isLoading = false;
            return;
        }

        $this->deviceId = $app->nomor_hp;
        $this->appName = $app->name;
    }

    /**
     * Check device status: auto-create device if not found, then update UI state
     */
    public function checkDeviceStatus(): void
    {
        if (! $this->deviceId) {
            return;
        }

        $this->isLoading = true;
        $this->errorMessage = null;
        $this->showQr = false;

        $this->dispatch('stop-qr-polling');

        $service = app(WhatsappService::class);
        $result = $service->ensureDeviceAndGetStatus($this->deviceId);

        if ($result['success']) {
            $results = $result['data']['results'] ?? [];
            $this->isConnected = $results['is_connected'] ?? false;
            $this->isLoggedIn = $results['is_logged_in'] ?? false;
            $this->jid = $results['jid'] ?? '';
            $this->statusCode = $result['data']['code'] ?? 'SUCCESS';

            if ($this->isConnected && $this->isLoggedIn) {
                $this->statusMessage = 'WhatsApp terhubung dan aktif.';
            } else {
                $this->statusMessage = 'WhatsApp belum terhubung. Silakan scan QR Code untuk menghubungkan.';
            }
        } else {
            $this->statusCode = $result['data']['code'] ?? 'ERROR';
            $this->statusMessage = $result['data']['message'] ?? 'Terjadi kesalahan tidak diketahui.';
            $this->errorMessage = $this->statusMessage;
        }

        $this->isLoading = false;
    }

    /**
     * Request QR code for login
     */
    public function requestQrLogin(): void
    {
        if (! $this->deviceId) {
            return;
        }

        $this->isConnecting = true;
        $this->errorMessage = null;

        $service = app(WhatsappService::class);
        $result = $service->login($this->deviceId);

        if ($result['success']) {
            $results = $result['data']['results'] ?? [];
            $this->qrLink = $results['qr_link'] ?? '';
            $this->qrDuration = (int) ($results['qr_duration'] ?? 30);
            $this->pollCountdown = $this->qrDuration;
            $this->showQr = true;

            $this->dispatch('start-qr-polling', duration: $this->qrDuration);
        } else {
            $this->errorMessage = $result['data']['message'] ?? 'Gagal meminta QR login.';
            $this->showQr = false;
        }

        $this->isConnecting = false;
    }

    /**
     * Poll status after QR scan (called by Alpine.js interval)
     */
    public function pollStatus(): void
    {
        if (! $this->deviceId) {
            return;
        }

        $service = app(WhatsappService::class);
        $result = $service->checkStatus($this->deviceId);

        if ($result['success']) {
            $results = $result['data']['results'] ?? [];
            $this->isConnected = $results['is_connected'] ?? false;
            $this->isLoggedIn = $results['is_logged_in'] ?? false;
            $this->jid = $results['jid'] ?? '';

            if ($this->isConnected && $this->isLoggedIn) {
                $this->showQr = false;
                $this->statusMessage = 'WhatsApp terhubung dan aktif.';
                $this->statusCode = 'SUCCESS';

                $this->dispatch('stop-qr-polling');
            }
        }
    }

    public function render()
    {
        return view('livewire.whatsapp.index');
    }
}
