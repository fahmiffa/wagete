<?php

namespace App\Livewire\Apps;

use App\Models\App as ClientApp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Search filter
    public string $search = '';

    // Modal Visibility
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    // Create Form Fields
    public string $name = '';
    public string $nomor_hp = '';
    public string $alamat = '';
    public string $user_name = '';
    public string $email = '';
    public string $password = '';
    public int $level = 1;
    public string $key = '';

    // Edit Form Fields
    public ?int $appId = null;
    public ?int $edit_user_id = null;
    public string $edit_name = '';
    public string $edit_nomor_hp = '';
    public string $edit_alamat = '';
    public string $edit_user_name = '';
    public string $edit_email = '';
    public string $edit_password = '';
    public int $edit_level = 1;
    public string $edit_key = '';

    // Delete Target
    public ?int $delete_app_id = null;
    public string $delete_app_name = '';
    public bool $delete_user_too = true;

    // Flash notification message
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    public function mount(): void
    {
        // Enforce role 0 access only
        if (! auth()->check() || (int) auth()->user()->role !== 0) {
            abort(403, 'Akses ditolak. Menu ini hanya dapat diakses oleh pengguna dengan Role 0.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Generate random API Key
     */
    public function generateRandomKey(): string
    {
        // Format: wag_ + 32 random alphanumeric characters
        return 'wag_' . Str::random(32);
    }

    /**
     * Open create modal and initialize random key
     */
    #[On('trigger-open-create-modal')]
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetCreateForm();
        $this->key = $this->generateRandomKey();
        $this->showCreateModal = true;
    }

    /**
     * Regenerate key in create form
     */
    public function refreshCreateKey(): void
    {
        $this->key = $this->generateRandomKey();
    }

    /**
     * Save new App and corresponding User account
     */
    public function store(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'user_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'level' => ['required', 'integer', 'in:1,2'],
            'key' => ['required', 'string', 'max:64', 'unique:apps,key'],
        ], [
            'name.required' => 'Nama aplikasi wajib diisi.',
            'email.required' => 'Email pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password pengguna wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'level.required' => 'Level user wajib dipilih.',
            'level.in' => 'Level user tidak valid.',
            'key.required' => 'API Key wajib diisi.',
            'key.unique' => 'API Key ini sudah digunakan, silakan buat ulang.',
        ]);

        DB::transaction(function () {
            // 1. Create User account with role 1 (client / regular user)
            $user = User::create([
                'name' => !empty(trim($this->user_name)) ? trim($this->user_name) : trim($this->name),
                'email' => trim($this->email),
                'password' => Hash::make($this->password),
                'role' => 1,
                'status' => 1,
                'level' => (int) $this->level,
            ]);

            // 2. Create App record associated with the new User
            ClientApp::create([
                'user_id' => $user->id,
                'name' => trim($this->name),
                'key' => trim($this->key),
                'alamat' => $this->alamat ? trim($this->alamat) : null,
                'nomor_hp' => $this->nomor_hp ? trim($this->nomor_hp) : null,
            ]);
        });

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->setFeedback('App dan akun user berhasil dibuat!');
    }

    /**
     * Open edit modal with loaded data
     */
    public function edit(int $id): void
    {
        $this->resetValidation();
        $app = ClientApp::with('user')->findOrFail($id);

        $this->appId = $app->id;
        $this->edit_name = $app->name;
        $this->edit_nomor_hp = $app->nomor_hp ?? '';
        $this->edit_alamat = $app->alamat ?? '';
        $this->edit_key = $app->key;

        $this->edit_user_id = $app->user_id;
        $this->edit_user_name = $app->user?->name ?? '';
        $this->edit_email = $app->user?->email ?? '';
        $this->edit_password = '';
        $this->edit_level = (int) ($app->user?->level ?? 1);

        $this->showEditModal = true;
    }

    /**
     * Regenerate key in edit form
     */
    public function refreshEditKey(): void
    {
        $this->edit_key = $this->generateRandomKey();
    }

    /**
     * Update App and user details
     */
    public function update(): void
    {
        $this->validate([
            'edit_name' => ['required', 'string', 'max:255'],
            'edit_nomor_hp' => ['nullable', 'string', 'max:25'],
            'edit_alamat' => ['nullable', 'string', 'max:1000'],
            'edit_user_name' => ['required', 'string', 'max:255'],
            'edit_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->edit_user_id],
            'edit_password' => ['nullable', 'string', 'min:6'],
            'edit_level' => ['required', 'integer', 'in:1,2'],
            'edit_key' => ['required', 'string', 'max:64', 'unique:apps,key,' . $this->appId],
        ], [
            'edit_name.required' => 'Nama aplikasi wajib diisi.',
            'edit_user_name.required' => 'Nama user wajib diisi.',
            'edit_email.required' => 'Email wajib diisi.',
            'edit_email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'edit_password.min' => 'Password minimal 6 karakter jika ingin diganti.',
            'edit_level.required' => 'Level user wajib dipilih.',
            'edit_level.in' => 'Level user tidak valid.',
            'edit_key.required' => 'API Key wajib diisi.',
            'edit_key.unique' => 'API Key sudah digunakan.',
        ]);

        $app = ClientApp::findOrFail($this->appId);

        DB::transaction(function () use ($app) {
            // Update User
            if ($app->user) {
                $userData = [
                    'name' => trim($this->edit_user_name),
                    'email' => trim($this->edit_email),
                    'level' => (int) $this->edit_level,
                ];

                if (!empty($this->edit_password)) {
                    $userData['password'] = Hash::make($this->edit_password);
                }

                $app->user->update($userData);
            }

            // Update App
            $app->update([
                'name' => trim($this->edit_name),
                'key' => trim($this->edit_key),
                'alamat' => $this->edit_alamat ? trim($this->edit_alamat) : null,
                'nomor_hp' => $this->edit_nomor_hp ? trim($this->edit_nomor_hp) : null,
            ]);
        });

        $this->showEditModal = false;
        $this->setFeedback('App dan akun user berhasil diperbarui!');
    }

    /**
     * Direct one-click quick regenerate API key
     */
    public function quickRegenerateKey(int $id): void
    {
        $app = ClientApp::findOrFail($id);
        $newKey = $this->generateRandomKey();
        $app->update(['key' => $newKey]);

        $this->setFeedback("API Key untuk app '{$app->name}' berhasil diperbarui!");
    }

    /**
     * Confirm delete modal
     */
    public function confirmDelete(int $id): void
    {
        $app = ClientApp::findOrFail($id);
        $this->delete_app_id = $app->id;
        $this->delete_app_name = $app->name;
        $this->delete_user_too = true;
        $this->showDeleteModal = true;
    }

    /**
     * Execute deletion
     */
    public function destroy(): void
    {
        if ($this->delete_app_id) {
            $app = ClientApp::with('user')->find($this->delete_app_id);

            if ($app) {
                DB::transaction(function () use ($app) {
                    $user = $app->user;
                    $app->delete();

                    if ($this->delete_user_too && $user) {
                        $user->delete();
                    }
                });

                $this->setFeedback("App '{$this->delete_app_name}' berhasil dihapus!");
            }
        }

        $this->showDeleteModal = false;
        $this->delete_app_id = null;
        $this->delete_app_name = '';
    }

    public function resetCreateForm(): void
    {
        $this->name = '';
        $this->nomor_hp = '';
        $this->alamat = '';
        $this->user_name = '';
        $this->email = '';
        $this->password = '';
        $this->level = 1;
        $this->key = '';
    }

    public function setFeedback(string $message, string $type = 'success'): void
    {
        $this->feedbackMessage = $message;
        $this->feedbackType = $type;
    }

    public function render()
    {
        $apps = ClientApp::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('key', 'like', '%' . $this->search . '%')
                      ->orWhere('nomor_hp', 'like', '%' . $this->search . '%')
                      ->orWhere('alamat', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($uq) {
                          $uq->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.apps.index', [
            'apps' => $apps,
        ]);
    }
}
