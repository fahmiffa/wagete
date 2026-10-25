<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsappService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsappGatewayController extends Controller
{
    public function __construct(
        protected WhatsappService $whatsapp,
    ) {}

    // ─── helpers ────────────────────────────────────────────────────

    /**
     * Resolve the device_id (nomor_hp) from the authenticated App.
     */
    protected function deviceId(Request $request): ?string
    {
        return $request->attributes->get('app')?->nomor_hp;
    }

    /**
     * Guard: return a 422 response when the App has no phone number configured.
     */
    protected function requireDevice(Request $request): ?JsonResponse
    {
        if (! $this->deviceId($request)) {
            return response()->json([
                'success' => false,
                'code'    => 'NO_PHONE',
                'message' => 'Nomor HP belum diisi pada aplikasi. Silakan update data App terlebih dahulu.',
            ], 422);
        }

        return null;
    }

    // ─── endpoints ──────────────────────────────────────────────────

    /**
     * GET /api/whatsapp/status
     *
     * Cek status koneksi WhatsApp (auto-create device jika belum ada).
     */
    public function status(Request $request): JsonResponse
    {
        if ($error = $this->requireDevice($request)) {
            return $error;
        }

        $result = $this->whatsapp->ensureDeviceAndGetStatus($this->deviceId($request));

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /**
     * GET /api/whatsapp/login
     *
     * Minta QR code untuk login WhatsApp.
     */
    public function login(Request $request): JsonResponse
    {
        if ($error = $this->requireDevice($request)) {
            return $error;
        }

        $result = $this->whatsapp->login($this->deviceId($request));

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /**
     * POST /api/whatsapp/send
     *
     * Kirim pesan WhatsApp.
     *
     * Body JSON:
     *  - phone   (string, required) — nomor tujuan
     *  - message (string, required) — isi pesan
     */
    public function send(Request $request): JsonResponse
    {
        if ($error = $this->requireDevice($request)) {
            return $error;
        }

        $validated = $request->validate([
            'phone'   => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $result = $this->whatsapp->sendMessage(
            $this->deviceId($request),
            $validated['phone'],
            $validated['message'],
        );

        return response()->json($result, $result['success'] ? 200 : 502);
    }
}
