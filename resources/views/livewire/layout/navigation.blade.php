<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#111827] text-gray-300 transition-transform duration-300 transform lg:translate-x-0 border-r border-gray-800"
           :class="{'translate-x-0': mobileSidebarOpen, '-translate-x-full': !mobileSidebarOpen, 'lg:-translate-x-full': !sidebarOpen, 'lg:translate-x-0': sidebarOpen}">
           
        <!-- Close button for mobile -->
        <div class="flex items-center justify-between p-4 lg:hidden border-b border-gray-800">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                <x-application-logo class="block h-8 w-auto fill-current text-white" />
                <span class="text-xl font-bold text-white">QBIL</span>
            </a>
            <button @click="mobileSidebarOpen = false" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Logo for Desktop -->
        <div class="hidden lg:flex items-center p-4 h-16 border-b border-gray-800">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 w-full">
                <x-application-logo class="block h-8 w-auto fill-current text-indigo-500" />
                <span class="text-xl font-bold text-white tracking-wide">QBIL</span>
            </a>
        </div>

        <!-- Sidebar Links -->
        <div class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-4rem)]">
            
            <a href="{{ route('dashboard') }}" wire:navigate 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600/10 text-indigo-500' : 'hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="font-medium">{{ __('Dashboard') }}</span>
            </a>

            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-3 px-4">Layanan</div>

            @if (auth()->check() && (int) auth()->user()->role === 0)
                <a href="{{ route('apps.index') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('apps.*') ? 'bg-indigo-600/10 text-indigo-500' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="font-medium">{{ __('App') }}</span>
                </a>
            @endif

            @if (auth()->check() && (int) auth()->user()->level === 2)
                <a href="{{ route('contacts.index') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('contacts.*') ? 'bg-indigo-600/10 text-indigo-500' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="font-medium">{{ __('Kontak') }}</span>
                </a>
                <a href="{{ route('templates.index') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('templates.*') ? 'bg-indigo-600/10 text-indigo-500' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-medium">{{ __('Template Pesan') }}</span>
                </a>
                <a href="{{ route('message-schedulers.index') }}" wire:navigate 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('message-schedulers.*') ? 'bg-indigo-600/10 text-indigo-500' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ __('Pesan Terjadwal') }}</span>
                </a>
            @endif

            <a href="{{ route('whatsapp.index') }}" wire:navigate 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('whatsapp.*') ? 'bg-indigo-600/10 text-indigo-500' : 'hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span class="font-medium">{{ __('WhatsApp') }}</span>
            </a>

            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-3 px-4">Sistem</div>

            <!-- Logout -->
            <button wire:click="logout" class="w-full flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="font-medium">{{ __('Logout') }}</span>
            </button>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden"
         x-transition.opacity>
    </div>
</div>
