<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Banner -->
            <div class="p-6 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-lg text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}! 👋</h3>
                        <p class="mt-1 text-indigo-100 text-sm">
                            Selamat datang di aplikasi Anda. Sistem otentikasi Livewire dan dashboard sudah siap digunakan.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if ((int) auth()->user()->role === 0)
                            <a href="{{ route('apps.index') }}" wire:navigate class="px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 rounded-lg text-sm font-semibold shadow transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Kelola App
                            </a>
                        @endif
                        @if ((int) auth()->user()->level === 2)
                            <a href="{{ route('contacts.index') }}" wire:navigate class="px-4 py-2 bg-emerald-400 text-gray-900 hover:bg-emerald-300 rounded-lg text-sm font-semibold shadow transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Kelola Kontak
                            </a>
                        @endif
                        <a href="{{ route('profile') }}" wire:navigate class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg text-sm font-medium transition">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            @php
                $user = auth()->user();
                $totalApps = (int) $user->role === 0
                    ? \App\Models\App::count()
                    : \App\Models\App::where('user_id', $user->id)->count();
                $totalContacts = (int) $user->role === 0
                    ? \App\Models\Contact::count()
                    : \App\Models\Contact::whereIn('app_id', \App\Models\App::where('user_id', $user->id)->pluck('id'))->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if ((int) $user->role === 0)
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Aplikasi</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">{{ $totalApps }}</p>
                            </div>
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center font-bold">
                                📱
                            </div>
                        </div>
                        <a href="{{ route('apps.index') }}" wire:navigate class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-4 inline-block font-medium">Lihat Semua App &rarr;</a>
                    </div>
                @endif

                @if ((int) $user->level === 2)
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kontak</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">{{ $totalContacts }}</p>
                            </div>
                            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center font-bold">
                                👥
                            </div>
                        </div>
                        <a href="{{ route('contacts.index') }}" wire:navigate class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline mt-4 inline-block font-medium">Buka Menu Kontak &rarr;</a>
                    </div>
                @endif

                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Akun</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">Aktif</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center font-bold">
                            ✓
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Role: {{ $user->role === 0 ? 'Admin (0)' : 'Client (1)' }}</p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Database</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">SQLite</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center font-bold">
                            DB
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">database/database.sqlite</p>
                </div>
            </div>

            <!-- Getting Started Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Langkah Selanjutnya</h4>
                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <span class="text-indigo-600 font-bold">•</span>
                            <span><strong>Halaman Login / Register Livewire:</strong> Berada di <code>resources/views/livewire/pages/auth/</code>.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-indigo-600 font-bold">•</span>
                            <span><strong>Komponen Livewire Baru:</strong> Buat komponen baru dengan perintah <code>php artisan make:livewire &lt;nama-komponen&gt;</code>.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-indigo-600 font-bold">•</span>
                            <span><strong>Jalankan Development Server:</strong> Jalankan <code>php artisan serve</code> dan di terminal lain jalankan <code>npm run dev</code> untuk hot reload asset.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
