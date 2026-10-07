<div
    x-data="{
        countdown: @entangle('pollCountdown'),
        polling: false,
        timer: null,

        startPolling(duration) {
            this.stopPolling();
            this.countdown = duration;
            this.polling = true;

            this.timer = setInterval(() => {
                this.countdown--;

                if (this.countdown <= 0) {
                    this.stopPolling();
                    // Re-check status after QR expired
                    $wire.checkDeviceStatus();
                    return;
                }

                // Poll status every 3 seconds
                if (this.countdown % 3 === 0) {
                    $wire.pollStatus();
                }
            }, 1000);
        },

        stopPolling() {
            this.polling = false;
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        }
    }"

    x-on:start-qr-polling.window="startPolling($event.detail.duration)"
    x-on:stop-qr-polling.window="stopPolling()"

    {{-- Auto-stop polling when device gets connected --}}
    x-effect="if ($wire.isConnected && $wire.isLoggedIn) stopPolling()"
>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    {{-- WhatsApp Icon --}}
                    <svg class="w-6 h-6 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    {{ __('WhatsApp') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Hubungkan dan kelola koneksi WhatsApp Gateway perangkat Anda.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ========================================== --}}
            {{-- LOADING STATE --}}
            {{-- ========================================== --}}
            @if ($isLoading)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 flex flex-col items-center justify-center space-y-4">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-full border-4 border-gray-200 dark:border-gray-700"></div>
                            <div class="absolute inset-0 w-16 h-16 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Memeriksa status perangkat...</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Menghubungi server WhatsApp Gateway</p>
                        </div>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- NO APP / NO PHONE --}}
            {{-- ========================================== --}}
            @elseif ($statusCode === 'NO_APP')
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Belum Ada Aplikasi</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $statusMessage }}</p>
                        </div>
                        @if ((int) auth()->user()->role === 0)
                            <a href="{{ route('apps.index') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Buat Aplikasi
                            </a>
                        @endif
                    </div>
                </div>

            @elseif ($statusCode === 'NO_PHONE')
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nomor HP Belum Diisi</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $statusMessage }}</p>
                        </div>
                        @if ((int) auth()->user()->role === 0)
                            <a href="{{ route('apps.index') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Update Data App
                            </a>
                        @endif
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- ERROR STATE --}}
            {{-- ========================================== --}}
            @elseif ($errorMessage)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-950/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Terjadi Kesalahan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $errorMessage }}</p>
                        </div>
                        <button wire:click="checkDeviceStatus"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span wire:loading.remove wire:target="checkDeviceStatus">Coba Lagi</span>
                            <span wire:loading wire:target="checkDeviceStatus">Memeriksa...</span>
                        </button>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- CONNECTED & LOGGED IN --}}
            {{-- ========================================== --}}
            @elseif ($isConnected && $isLoggedIn)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- Device Info Header --}}
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center flex-shrink-0 border-2 border-emerald-200 dark:border-emerald-800">
                                <svg class="w-7 h-7 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">WhatsApp Terhubung</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Perangkat aktif dan siap mengirim pesan.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Online</span>
                            </div>
                        </div>
                    </div>

                    {{-- Device Details --}}
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Device ID --}}
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Device ID</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white font-mono">{{ $deviceId }}</p>
                            </div>

                            {{-- App Name --}}
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aplikasi</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $appName }}</p>
                            </div>

                            {{-- JID --}}
                            @if ($jid)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 sm:col-span-2">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">WhatsApp JID</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white font-mono">{{ $jid }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Status badges --}}
                        <div class="flex flex-wrap gap-2 pt-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Terhubung
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Login Aktif
                            </span>
                        </div>

                        {{-- Refresh --}}
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                            <button wire:click="checkDeviceStatus"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition disabled:opacity-50">
                                <svg class="w-3.5 h-3.5" wire:loading.class="animate-spin" wire:target="checkDeviceStatus" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span wire:loading.remove wire:target="checkDeviceStatus">Refresh Status</span>
                                <span wire:loading wire:target="checkDeviceStatus">Memeriksa...</span>
                            </button>
                        </div>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- NOT CONNECTED / NOT LOGGED IN --}}
            {{-- ========================================== --}}
            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- Header --}}
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 border-2 border-gray-200 dark:border-gray-600">
                                <svg class="w-7 h-7 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">WhatsApp Tidak Terhubung</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Scan QR Code untuk menghubungkan perangkat.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-400"></span>
                                </span>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Offline</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Device Info --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Device ID</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white font-mono">{{ $deviceId }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aplikasi</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $appName }}</p>
                            </div>
                        </div>

                        {{-- Status badges --}}
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $isConnected ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
                                @if ($isConnected)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                                {{ $isConnected ? 'Terhubung' : 'Tidak Terhubung' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $isLoggedIn ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
                                @if ($isLoggedIn)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @endif
                                {{ $isLoggedIn ? 'Login Aktif' : 'Belum Login' }}
                            </span>
                        </div>

                        {{-- QR Code Section --}}
                        @if ($showQr && $qrLink)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Scan QR Code</span>
                                    </div>
                                    <div class="flex items-center gap-2" x-show="polling">
                                        <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400"
                                              x-text="countdown + 's'"></span>
                                    </div>
                                </div>

                                <div class="p-6 flex flex-col items-center space-y-4">
                                    {{-- QR Image --}}
                                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                        <img src="{{ $qrLink }}"
                                             alt="QR Code WhatsApp"
                                             class="w-64 h-64 object-contain"
                                             loading="eager">
                                    </div>

                                    {{-- Instructions --}}
                                    <div class="text-center space-y-2 max-w-sm">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Buka WhatsApp di ponsel Anda
                                        </p>
                                        <ol class="text-xs text-gray-500 dark:text-gray-400 text-left space-y-1 list-decimal list-inside">
                                            <li>Buka <span class="font-semibold">WhatsApp</span> di ponsel</li>
                                            <li>Ketuk <span class="font-semibold">Menu (⋮)</span> atau <span class="font-semibold">Pengaturan</span></li>
                                            <li>Ketuk <span class="font-semibold">Perangkat Tertaut</span></li>
                                            <li>Ketuk <span class="font-semibold">Tautkan Perangkat</span></li>
                                            <li>Arahkan ponsel ke layar ini untuk scan QR Code</li>
                                        </ol>
                                    </div>

                                    {{-- Countdown Progress --}}
                                    <div class="w-full max-w-xs" x-show="polling">
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                            <span>Waktu tersisa</span>
                                            <span x-text="countdown + ' detik'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                            <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000"
                                                 :style="'width: ' + (countdown / {{ $qrDuration }} * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Connect Button --}}
                            <div class="flex flex-col items-center space-y-3 py-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Klik tombol di bawah untuk mendapatkan QR Code dan menghubungkan WhatsApp.
                                </p>
                                <button wire:click="requestQrLogin"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-lg shadow-sm transition disabled:opacity-50">
                                    <svg class="w-5 h-5" wire:loading.class="animate-spin" wire:target="requestQrLogin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path wire:loading.remove wire:target="requestQrLogin" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        <path wire:loading wire:target="requestQrLogin" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="requestQrLogin">Hubungkan WhatsApp</span>
                                    <span wire:loading wire:target="requestQrLogin">Memuat QR Code...</span>
                                </button>
                            </div>
                        @endif

                        {{-- Refresh --}}
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $statusMessage }}
                            </p>
                            <button wire:click="checkDeviceStatus"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition disabled:opacity-50">
                                <svg class="w-3.5 h-3.5" wire:loading.class="animate-spin" wire:target="checkDeviceStatus" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span wire:loading.remove wire:target="checkDeviceStatus">Refresh</span>
                                <span wire:loading wire:target="checkDeviceStatus">Memeriksa...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
