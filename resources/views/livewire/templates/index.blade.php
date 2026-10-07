<div x-data x-on:trigger-open-template-create-modal.window="$wire.openCreateModal()">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Template Pesan') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Kelola format pesan cepat untuk mempermudah pengiriman pesan WhatsApp.
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
                    <!-- Search Box -->
                    <div class="relative w-full sm:w-80">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Cari template atau isi pesan..."
                               class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Total Template: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $templates->total() }}</span>
                        </div>
                        <button type="button"
                                wire:click="openCreateModal"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold rounded-lg shadow-sm transition duration-150 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Template Baru
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-3.5 w-16">#</th>
                                <th class="px-6 py-3.5 w-64">Nama Template</th>
                                <th class="px-6 py-3.5">Isi Pesan</th>
                                @if ((int) auth()->user()->role === 0)
                                    <th class="px-6 py-3.5 w-48">Pemilik (User)</th>
                                @endif
                                <th class="px-6 py-3.5 w-36">Dibuat</th>
                                <th class="px-6 py-3.5 text-right w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($templates as $index => $item)
                                <tr class="transition" wire:key="template-{{ $item->id }}">
                                    <!-- Number -->
                                    <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                        {{ $templates->firstItem() + $index }}
                                    </td>

                                    <!-- Nama Template -->
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                            <span>{{ $item->name }}</span>
                                        </div>
                                    </td>

                                    <!-- Isi Pesan -->
                                    <td class="px-6 py-4">
                                        <div x-data="{ copied: false }" class="relative group">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-lg border border-gray-200/70 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300 font-sans whitespace-pre-wrap break-words max-h-32 overflow-y-auto">
                                                {{ $item->pesan }}
                                            </div>
                                            <button type="button"
                                                    x-on:click="navigator.clipboard.writeText(@js($item->pesan)); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="absolute top-2 right-2 px-2 py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-[11px] font-medium text-gray-600 dark:text-gray-300 shadow-sm opacity-0 group-hover:opacity-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center gap-1"
                                                    title="Salin isi pesan">
                                                <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <svg x-show="copied" x-cloak class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                            </button>
                                        </div>
                                    </td>

                                    <!-- User (Admin view only) -->
                                    @if ((int) auth()->user()->role === 0)
                                        <td class="px-6 py-4">
                                            @if ($item->user)
                                                <div class="text-xs">
                                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $item->user->name }}</div>
                                                    <div class="text-gray-400">{{ $item->user->email }}</div>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">-</span>
                                            @endif
                                        </td>
                                    @endif

                                    <!-- Date -->
                                    <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <button type="button"
                                                    wire:click="openSendModal({{ $item->id }})"
                                                    class="p-1.5 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                                                    title="Simulasi Kirim Pesan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    wire:click="edit({{ $item->id }})"
                                                    class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                                                    title="Edit Template">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    wire:click="confirmDelete({{ $item->id }})"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                                                    title="Hapus Template">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ (int) auth()->user()->role === 0 ? 6 : 5 }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 flex items-center justify-center">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                </svg>
                                            </div>
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                Belum ada template pesan
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                                                @if ($search)
                                                    Tidak ditemukan template pesan dengan kata kunci "{{ $search }}".
                                                @else
                                                    Mulai buat template pesan agar Anda dapat mengirim pesan berulang secara praktis.
                                                @endif
                                            </p>
                                            @if (! $search)
                                                <button type="button"
                                                        wire:click="openCreateModal"
                                                        class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow transition">
                                                    Buat Template Pertama
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($templates->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $templates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL: Tambah Template Baru -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                 wire:click="$set('showCreateModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 z-10 transition-all">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                    Tambah Template Pesan
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Simpan format pesan baru untuk digunakan berulang kali.
                                </p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="$set('showCreateModal', false)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit="store">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nama Template <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="name"
                                       placeholder="Contoh: Ucapan Selamat Datang / Promo Bulanan"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Isi Pesan <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-gray-400">
                                        Maks. 5000 karakter
                                    </span>
                                </div>
                                <textarea wire:model="pesan"
                                          rows="6"
                                          placeholder="Halo {nama}, terima kasih telah menghubungi kami..."
                                          class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                @error('pesan') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button"
                                    wire:click="$set('showCreateModal', false)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg shadow transition">
                                Simpan Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Edit Template -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                 wire:click="$set('showEditModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 z-10 transition-all">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                    Edit Template Pesan
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Perbarui nama atau isi pesan template.
                                </p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="$set('showEditModal', false)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit="update">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nama Template <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="edit_name"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('edit_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Isi Pesan <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-gray-400">
                                        Maks. 5000 karakter
                                    </span>
                                </div>
                                <textarea wire:model="edit_pesan"
                                          rows="6"
                                          class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                @error('edit_pesan') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button"
                                    wire:click="$set('showEditModal', false)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg shadow transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Hapus Template -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                 wire:click="$set('showDeleteModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 z-10 transition-all">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400 mx-auto flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>

                        <h3 class="text-base font-bold text-center text-gray-900 dark:text-white mb-2">
                            Konfirmasi Hapus Template
                        </h3>

                        <p class="text-xs text-center text-gray-500 dark:text-gray-400 mb-6">
                            Apakah Anda yakin ingin menghapus template pesan <strong class="text-gray-800 dark:text-gray-200">"{{ $delete_template_name }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <div class="flex items-center justify-center gap-3">
                            <button type="button"
                                    wire:click="$set('showDeleteModal', false)"
                                    class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                Batal
                            </button>
                            <button type="button"
                                    wire:click="destroy"
                                    class="w-full px-4 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-lg shadow transition">
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Simulasi Kirim Pesan -->
    @if ($showSendModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                 wire:click="$set('showSendModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 z-10 transition-all">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                    Simulasi Kirim Pesan
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Kirim pesan template via WhatsApp ke nomor tujuan.
                                </p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="$set('showSendModal', false)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit="submitSendMessage">
                        <div class="p-6 space-y-4">
                            <!-- Template Name (read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Template
                                </label>
                                <div class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                    {{ $send_template_name }}
                                </div>
                            </div>

                            <!-- Phone Number Input -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nomor WhatsApp Tujuan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="send_phone"
                                       placeholder="Contoh: 085173156513"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:border-green-500 focus:ring-green-500">
                                <p class="text-[11px] text-gray-400 mt-1">Format: 08xx, +62xx, atau 62xx — akan dikonversi otomatis.</p>
                                @error('send_phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Message Preview -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Isi Pesan
                                </label>
                                <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-lg border border-gray-200/70 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300 font-sans whitespace-pre-wrap break-words max-h-40 overflow-y-auto">
                                    {{ $send_pesan }}
                                </div>
                            </div>

                            <!-- Send Result -->
                            @if ($sendResultMessage)
                                <div class="p-3 rounded-lg flex items-start gap-2 text-sm border {{ $sendResultType === 'success' ? 'bg-green-50 dark:bg-green-950/40 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300' : 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300' }}">
                                    @if ($sendResultType === 'success')
                                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    @endif
                                    <span class="text-xs">{{ $sendResultMessage }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button"
                                    wire:click="$set('showSendModal', false)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                Tutup
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="submitSendMessage"
                                    class="px-4 py-2 text-sm font-semibold bg-green-600 hover:bg-green-700 active:bg-green-800 disabled:opacity-50 text-white rounded-lg shadow transition inline-flex items-center gap-2">
                                <svg wire:loading wire:target="submitSendMessage" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="submitSendMessage">Kirim Pesan</span>
                                <span wire:loading wire:target="submitSendMessage">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
