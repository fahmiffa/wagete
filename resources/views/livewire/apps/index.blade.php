<div x-data x-on:trigger-open-create-modal.window="$wire.openCreateModal()">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Kelola Aplikasi (Apps)') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Manajemen data aplikasi klien, API Key acak, serta sinkronisasi akun pengguna.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if ($feedbackMessage)
                <div x-data="{ show: true }"
                     x-show="show"
                     class="p-4 rounded-xl flex items-center justify-between shadow-sm border {{ $feedbackType === 'success' ? 'bg-green-50 dark:bg-green-950/40 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300' : 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">{{ $feedbackMessage }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- Table & Search Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="relative w-full sm:w-80">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Cari nama, email, hp, atau key..."
                               class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Total: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $apps->total() }}</span>
                        </div>
                        <button type="button"
                                wire:click="openCreateModal"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold rounded-lg shadow-sm transition duration-150 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah App Baru
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-3.5">ID / Nama App</th>
                                <th class="px-6 py-3.5">Akun User Terkait</th>
                                <th class="px-6 py-3.5">API Key</th>
                                <th class="px-6 py-3.5">Kontak & Alamat</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($apps as $app)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition">
                                    <!-- App Name & ID -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base flex-shrink-0">
                                                {{ strtoupper(substr($app->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $app->name }}
                                                </div>
                                                <div class="text-xs text-gray-400 mt-0.5">
                                                    ID: #{{ $app->id }} • {{ $app->created_at?->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- User Account -->
                                    <td class="px-6 py-4">
                                        @if ($app->user)
                                            <div class="space-y-1">
                                                <div class="font-medium text-gray-800 dark:text-gray-200">
                                                    {{ $app->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-1.5">
                                                    <span>{{ $app->user->email }}</span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $app->user->role === 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' }}">
                                                        Role: {{ $app->user->role }}
                                                    </span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ (int) $app->user->level === 2 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                                        Level: {{ $app->user->level ?? 1 }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs italic text-gray-400">User tidak ditemukan</span>
                                        @endif
                                    </td>

                                    <!-- API Key -->
                                    <td class="px-6 py-4">
                                        <div x-data="{ showKey: false, copied: false }" class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <code class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 font-mono text-xs select-all">
                                                    <span x-show="!showKey">{{ Str::mask($app->key, '*', 6, -6) }}</span>
                                                    <span x-show="showKey" x-cloak>{{ $app->key }}</span>
                                                </code>

                                                <!-- Toggle reveal -->
                                                <button type="button"
                                                        @click="showKey = !showKey"
                                                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded"
                                                        title="Lihat / Sembunyikan Key">
                                                    <svg x-show="!showKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <svg x-show="showKey" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                                </button>

                                                <!-- Copy button -->
                                                <button type="button"
                                                        @click="navigator.clipboard.writeText('{{ $app->key }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                        class="p-1 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded relative"
                                                        title="Salin Key">
                                                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    <span x-show="copied" x-cloak class="text-[11px] font-semibold text-green-600 dark:text-green-400">Disalin!</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Phone & Address -->
                                    <td class="px-6 py-4">
                                        <div class="text-xs space-y-1">
                                            @if ($app->nomor_hp)
                                                <div class="flex items-center gap-1.5 font-medium text-gray-700 dark:text-gray-300">
                                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    {{ $app->nomor_hp }}
                                                </div>
                                            @endif
                                            @if ($app->alamat)
                                                <div class="text-gray-500 dark:text-gray-400 truncate max-w-xs" title="{{ $app->alamat }}">
                                                    {{ $app->alamat }}
                                                </div>
                                            @endif
                                            @if (! $app->nomor_hp && ! $app->alamat)
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Quick Regenerate Key -->
                                            <button wire:click="quickRegenerateKey({{ $app->id }})"
                                                    wire:confirm="Yakin ingin membuat ulang API Key untuk app '{{ $app->name }}'? Key lama tidak akan berlaku lagi."
                                                    type="button"
                                                    class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition"
                                                    title="Regenerate API Key">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>

                                            <!-- Edit -->
                                            <button wire:click="edit({{ $app->id }})"
                                                    type="button"
                                                    class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition"
                                                    title="Edit Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            <!-- Delete -->
                                            <button wire:click="confirmDelete({{ $app->id }})"
                                                    type="button"
                                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition"
                                                    title="Hapus App">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Belum ada aplikasi yang terdaftar.
                                            </p>
                                            <button wire:click="openCreateModal"
                                                    type="button"
                                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                                                + Buat aplikasi pertama sekarang
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($apps->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $apps->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL CREATE APP -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showCreateModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                    <form wire:submit="store">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">
                                    Tambah Aplikasi & Akun Pengguna Baru
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Menginput data aplikasi sekaligus membuat user account terkait.
                                </p>
                            </div>
                            <button type="button" wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Bagian 1: Data Aplikasi -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                                    1. Informasi Aplikasi
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nama Aplikasi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               wire:model="name"
                                               placeholder="Contoh: WhatsApp Gateway Client Toko A"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nomor HP
                                        </label>
                                        <input type="text"
                                               wire:model="nomor_hp"
                                               placeholder="Contoh: 081234567890"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('nomor_hp') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Alamat
                                        </label>
                                        <input type="text"
                                               wire:model="alamat"
                                               placeholder="Contoh: Jl. Sudirman No. 12"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('alamat') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 2: Akun User Pengguna -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                                    2. Akun User (Login Credential)
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nama Pengguna (Opsional)
                                        </label>
                                        <input type="text"
                                               wire:model="user_name"
                                               placeholder="Kosongkan jika sama dengan nama App"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('user_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Email Akun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email"
                                               wire:model="email"
                                               placeholder="client@domain.com"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div x-data="{ showPassword: false }">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Password Akun <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input :type="showPassword ? 'text' : 'password'"
                                                   wire:model="password"
                                                   placeholder="Minimal 6 karakter"
                                                   class="w-full px-3 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button"
                                                    x-on:click="showPassword = !showPassword"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none"
                                                    tabindex="-1">
                                                <!-- Eye Icon (show password) -->
                                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <!-- Eye Off Icon (hide password) -->
                                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592M6.7 6.7A9.965 9.965 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.2M15 12a3 3 0 01-5.997.175M3 3l18 18" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Level User <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="level"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="1">Level 1 (User Standar)</option>
                                            <option value="2">Level 2 (Akses Menu Kontak)</option>
                                        </select>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Hanya Level 2 yang diizinkan mengakses Menu Kontak.</p>
                                        @error('level') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 3: Random API Key -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                                    3. API Key (Otomatis Di-generate Random)
                                </h4>
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                           wire:model="key"
                                           readonly
                                           class="w-full px-3 py-2 rounded-lg font-mono text-xs border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 focus:ring-0">
                                    <button type="button"
                                            wire:click="refreshCreateKey"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition"
                                            title="Buat Acak Lagi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Generate
                                    </button>
                                </div>
                                @error('key') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button"
                                    wire:click="$set('showCreateModal', false)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="store">Simpan</span>
                                <span wire:loading wire:target="store">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL EDIT APP -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showEditModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                    <form wire:submit="update">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    Edit Aplikasi & Akun Pengguna
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Perbarui rincian aplikasi atau data login user terkait.
                                </p>
                            </div>
                            <button type="button" wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Bagian 1: Data Aplikasi -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                                    1. Informasi Aplikasi
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nama Aplikasi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               wire:model="edit_name"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('edit_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nomor HP
                                        </label>
                                        <input type="text"
                                               wire:model="edit_nomor_hp"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('edit_nomor_hp') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Alamat
                                        </label>
                                        <input type="text"
                                               wire:model="edit_alamat"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('edit_alamat') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 2: Akun User Pengguna -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                                    2. Akun User Terkait
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nama Pengguna <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               wire:model="edit_user_name"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('edit_user_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Email Akun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email"
                                               wire:model="edit_email"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('edit_email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div x-data="{ showPassword: false }">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Ubah Password <span class="text-gray-400 font-normal">(Kosongkan jika tidak diubah)</span>
                                        </label>
                                        <div class="relative">
                                            <input :type="showPassword ? 'text' : 'password'"
                                                   wire:model="edit_password"
                                                   placeholder="Masukkan password baru..."
                                                   class="w-full px-3 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="button"
                                                    x-on:click="showPassword = !showPassword"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none"
                                                    tabindex="-1">
                                                <!-- Eye Icon (show password) -->
                                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <!-- Eye Off Icon (hide password) -->
                                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592M6.7 6.7A9.965 9.965 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.043 5.2M15 12a3 3 0 01-5.997.175M3 3l18 18" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('edit_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Level User <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="edit_level"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="1">Level 1 (User Standar)</option>
                                            <option value="2">Level 2 (Akses Menu Kontak)</option>
                                        </select>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Hanya Level 2 yang diizinkan mengakses Menu Kontak.</p>
                                        @error('edit_level') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 3: API Key -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                                    3. API Key
                                </h4>
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                           wire:model="edit_key"
                                           class="w-full px-3 py-2 rounded-lg font-mono text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button"
                                            wire:click="refreshEditKey"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition"
                                            title="Generate Key Baru">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Generate
                                    </button>
                                </div>
                                @error('edit_key') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button"
                                    wire:click="$set('showEditModal', false)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="update">Perbarui Data</span>
                                <span wire:loading wire:target="update">Memperbarui...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL CONFIRM DELETE -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Hapus Aplikasi
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Anda yakin ingin menghapus aplikasi <span class="font-semibold text-gray-800 dark:text-gray-200">"{{ $delete_app_name }}"</span>?
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   wire:model="delete_user_too"
                                   class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500">
                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                Hapus juga akun user terkait dari database
                            </span>
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button"
                                wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="destroy"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="destroy">Ya, Hapus</span>
                            <span wire:loading wire:target="destroy">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
