<?php

namespace App\Livewire\Contacts;

use App\Models\App as ClientApp;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    // Search & Filter
    public string $search = '';
    public string $filter_app_id = '';

    // Modal Visibility
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public bool $showImportModal = false;

    // File Upload for Import
    public $file;
    public ?int $import_app_id = null;

    // Create Form Fields
    public ?int $app_id = null;
    public string $nama = '';
    public string $phone = '';

    // Edit Form Fields
    public ?int $contactId = null;
    public ?int $edit_app_id = null;
    public string $edit_nama = '';
    public string $edit_phone = '';

    // Delete Target
    public ?int $delete_contact_id = null;
    public string $delete_contact_name = '';

    // Flash notification message
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    public function mount(): void
    {
        if (! auth()->check() || (int) auth()->user()->level !== 2) {
            abort(403, 'Akses ditolak. Menu Kontak hanya dapat diakses oleh pengguna dengan Level 2.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAppId(): void
    {
        $this->resetPage();
    }

    /**
     * Get accessible apps for the current user
     */
    public function getAvailableApps(): Collection
    {
        $user = auth()->user();

        if ((int) $user->role === 0) {
            // Admin can see all apps
            return ClientApp::orderBy('name')->get();
        }

        // Regular user only sees their own apps
        return ClientApp::where('user_id', $user->id)->orderBy('name')->get();
    }

    /**
     * Open create modal
     */
    #[On('trigger-open-contact-create-modal')]
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetCreateForm();

        // Pre-select first app if only 1 app available
        $availableApps = $this->getAvailableApps();
        if ($availableApps->count() > 0) {
            $this->app_id = $availableApps->first()->id;
        }

        $this->showCreateModal = true;
    }

    /**
     * Save new contact
     */
    public function store(): void
    {
        $user = auth()->user();
        $allowedAppIds = $this->getAvailableApps()->pluck('id')->toArray();

        $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'app_id' => ['required', 'integer', 'in:' . implode(',', $allowedAppIds)],
        ], [
            'nama.required' => 'Nama kontak wajib diisi.',
            'phone.required' => 'Nomor HP/Telepon kontak wajib diisi.',
            'app_id.required' => 'Pilih aplikasi untuk kontak ini.',
            'app_id.in' => 'Aplikasi yang dipilih tidak valid atau Anda tidak memiliki izin.',
        ]);

        Contact::create([
            'app_id' => $this->app_id,
            'nama' => trim($this->nama),
            'phone' => trim($this->phone),
        ]);

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->setFeedback('Kontak baru berhasil ditambahkan!');
    }

    /**
     * Open import modal
     */
    public function openImportModal(): void
    {
        $this->resetValidation();
        $this->file = null;
        
        $availableApps = $this->getAvailableApps();
        if ($availableApps->count() > 0) {
            $this->import_app_id = $availableApps->first()->id;
        }

        $this->showImportModal = true;
    }

    /**
     * Handle file import
     */
    public function import(): void
    {
        $allowedAppIds = $this->getAvailableApps()->pluck('id')->toArray();

        $this->validate([
            'import_app_id' => ['required', 'integer', 'in:' . implode(',', $allowedAppIds)],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'import_app_id.required' => 'Pilih aplikasi untuk kontak ini.',
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls) atau CSV.',
        ]);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $imported = 0;
            foreach ($rows as $index => $row) {
                // Skip empty rows or header row
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }
                
                if ($index === 0 && (stripos((string)$row[0], 'nama') !== false || stripos((string)$row[1], 'phone') !== false || stripos((string)$row[1], 'telepon') !== false)) {
                    continue;
                }

                $nama = trim((string)($row[0] ?? ''));
                $phone = trim((string)($row[1] ?? ''));

                if (!empty($nama) && !empty($phone)) {
                    Contact::create([
                        'app_id' => $this->import_app_id,
                        'nama' => $nama,
                        'phone' => $phone,
                    ]);
                    $imported++;
                }
            }

            $this->showImportModal = false;
            $this->file = null;
            $this->setFeedback("Berhasil mengimpor {$imported} kontak dari file.");
            
        } catch (\Exception $e) {
            $this->addError('file', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Open edit modal
     */
    public function edit(int $id): void
    {
        $this->resetValidation();
        $contact = $this->getAuthorizedContactQuery()->findOrFail($id);

        $this->contactId = $contact->id;
        $this->edit_app_id = $contact->app_id;
        $this->edit_nama = $contact->nama;
        $this->edit_phone = $contact->phone;

        $this->showEditModal = true;
    }

    /**
     * Update contact
     */
    public function update(): void
    {
        $allowedAppIds = $this->getAvailableApps()->pluck('id')->toArray();

        $this->validate([
            'edit_nama' => ['required', 'string', 'max:255'],
            'edit_phone' => ['required', 'string', 'max:30'],
            'edit_app_id' => ['required', 'integer', 'in:' . implode(',', $allowedAppIds)],
        ], [
            'edit_nama.required' => 'Nama kontak wajib diisi.',
            'edit_phone.required' => 'Nomor HP/Telepon kontak wajib diisi.',
            'edit_app_id.required' => 'Pilih aplikasi untuk kontak ini.',
            'edit_app_id.in' => 'Aplikasi yang dipilih tidak valid.',
        ]);

        $contact = $this->getAuthorizedContactQuery()->findOrFail($this->contactId);

        $contact->update([
            'app_id' => $this->edit_app_id,
            'nama' => trim($this->edit_nama),
            'phone' => trim($this->edit_phone),
        ]);

        $this->showEditModal = false;
        $this->setFeedback('Kontak berhasil diperbarui!');
    }

    /**
     * Confirm delete modal
     */
    public function confirmDelete(int $id): void
    {
        $contact = $this->getAuthorizedContactQuery()->findOrFail($id);
        $this->delete_contact_id = $contact->id;
        $this->delete_contact_name = $contact->nama;
        $this->showDeleteModal = true;
    }

    /**
     * Delete contact
     */
    public function destroy(): void
    {
        if ($this->delete_contact_id) {
            $contact = $this->getAuthorizedContactQuery()->find($this->delete_contact_id);

            if ($contact) {
                $contact->delete();
                $this->setFeedback("Kontak '{$this->delete_contact_name}' berhasil dihapus!");
            }
        }

        $this->showDeleteModal = false;
        $this->delete_contact_id = null;
        $this->delete_contact_name = '';
    }

    public function resetCreateForm(): void
    {
        $this->nama = '';
        $this->phone = '';
        $this->app_id = null;
    }

    public function setFeedback(string $message, string $type = 'success'): void
    {
        $this->feedbackMessage = $message;
        $this->feedbackType = $type;
    }

    /**
     * Query scoped to authorized apps
     */
    protected function getAuthorizedContactQuery(): Builder
    {
        $user = auth()->user();
        $query = Contact::with('app');

        if ((int) $user->role !== 0) {
            $userAppIds = ClientApp::where('user_id', $user->id)->pluck('id');
            $query->whereIn('app_id', $userAppIds);
        }

        return $query;
    }

    public function render()
    {
        $apps = $this->getAvailableApps();

        $contacts = $this->getAuthorizedContactQuery()
            ->when($this->filter_app_id, function ($q) {
                $q->where('app_id', $this->filter_app_id);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhereHas('app', function ($aq) {
                          $aq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.contacts.index', [
            'contacts' => $contacts,
            'apps' => $apps,
        ]);
    }
}
