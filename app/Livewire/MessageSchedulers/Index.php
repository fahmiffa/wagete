<?php

namespace App\Livewire\MessageSchedulers;

use App\Models\App as ClientApp;
use App\Models\Contact;
use App\Models\MessageScheduler;
use App\Models\TemplatePesan;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Search & Filter
    public string $search = '';
    public string $filter_status = '';

    // Modal Visibility
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    // Create Form Fields
    public string $template_pesan_id = '';
    public array $contact_ids = [];
    public string $waktu = '';

    // Edit Form Fields
    public ?int $editId = null;
    public string $edit_template_pesan_id = '';
    public string $edit_contact_id = '';
    public string $edit_waktu = '';

    // Delete Target
    public ?int $delete_id = null;
    public string $delete_info = '';

    // Flash notification
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    public function mount(): void
    {
        // Enforce level 2 access only
        if (! auth()->check() || (int) auth()->user()->level !== 2) {
            abort(403, 'Akses ditolak. Menu Pesan Terjadwal hanya dapat diakses oleh pengguna dengan Level 2.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Get user's templates for dropdown
     */
    public function getTemplatesProperty()
    {
        return TemplatePesan::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    /**
     * Get user's contacts for dropdown
     */
    public function getContactsProperty()
    {
        $user = auth()->user();
        $appIds = ClientApp::where('user_id', $user->id)->pluck('id');

        return Contact::whereIn('app_id', $appIds)
            ->orderBy('nama')
            ->get();
    }

    /**
     * Query scoped to authorized schedulers
     */
    protected function getAuthorizedQuery(): Builder
    {
        return MessageScheduler::with(['templatePesan', 'contact'])
            ->where('user_id', auth()->id());
    }

    /**
     * Open create modal
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    /**
     * Save new schedule
     */
    public function store(): void
    {
        $this->validate([
            'template_pesan_id' => ['required', 'exists:template_pesans,id'],
            'contact_ids' => ['required', 'array', 'min:1'],
            'contact_ids.*' => ['exists:contacts,id'],
            'waktu' => ['required', 'date', 'after:now'],
        ], [
            'template_pesan_id.required' => 'Template pesan wajib dipilih.',
            'contact_ids.required' => 'Kontak tujuan wajib dipilih minimal satu.',
            'waktu.required' => 'Waktu kirim wajib diisi.',
            'waktu.after' => 'Waktu kirim harus di masa depan.',
        ]);

        foreach ($this->contact_ids as $cid) {
            MessageScheduler::create([
                'user_id' => auth()->id(),
                'template_pesan_id' => $this->template_pesan_id,
                'contact_id' => $cid,
                'waktu' => $this->waktu,
                'status' => 'pending',
            ]);
        }

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->setFeedback('Jadwal pesan baru berhasil ditambahkan!');
    }

    /**
     * Open edit modal
     */
    public function edit(int $id): void
    {
        $this->resetValidation();
        $scheduler = $this->getAuthorizedQuery()->findOrFail($id);

        // Only allow editing pending schedules
        if ($scheduler->status !== 'pending') {
            $this->setFeedback('Hanya jadwal dengan status "pending" yang dapat diedit.', 'error');
            return;
        }

        $this->editId = $scheduler->id;
        $this->edit_template_pesan_id = (string) $scheduler->template_pesan_id;
        $this->edit_contact_id = (string) $scheduler->contact_id;
        $this->edit_waktu = $scheduler->waktu->format('Y-m-d\TH:i');

        $this->showEditModal = true;
    }

    /**
     * Update schedule
     */
    public function update(): void
    {
        $this->validate([
            'edit_template_pesan_id' => ['required', 'exists:template_pesans,id'],
            'edit_contact_id' => ['required', 'exists:contacts,id'],
            'edit_waktu' => ['required', 'date', 'after:now'],
        ], [
            'edit_template_pesan_id.required' => 'Template pesan wajib dipilih.',
            'edit_contact_id.required' => 'Kontak tujuan wajib dipilih.',
            'edit_waktu.required' => 'Waktu kirim wajib diisi.',
            'edit_waktu.after' => 'Waktu kirim harus di masa depan.',
        ]);

        $scheduler = $this->getAuthorizedQuery()->findOrFail($this->editId);

        if ($scheduler->status !== 'pending') {
            $this->setFeedback('Hanya jadwal dengan status "pending" yang dapat diedit.', 'error');
            $this->showEditModal = false;
            return;
        }

        $scheduler->update([
            'template_pesan_id' => $this->edit_template_pesan_id,
            'contact_id' => $this->edit_contact_id,
            'waktu' => $this->edit_waktu,
        ]);

        $this->showEditModal = false;
        $this->setFeedback('Jadwal pesan berhasil diperbarui!');
    }

    /**
     * Confirm delete modal
     */
    public function confirmDelete(int $id): void
    {
        $scheduler = $this->getAuthorizedQuery()->with(['templatePesan', 'contact'])->findOrFail($id);
        $this->delete_id = $scheduler->id;
        $this->delete_info = $scheduler->templatePesan->name . ' → ' . $scheduler->contact->nama;
        $this->showDeleteModal = true;
    }

    /**
     * Delete schedule
     */
    public function destroy(): void
    {
        if ($this->delete_id) {
            $scheduler = $this->getAuthorizedQuery()->find($this->delete_id);

            if ($scheduler) {
                $scheduler->delete();
                $this->setFeedback("Jadwal pesan '{$this->delete_info}' berhasil dihapus!");
            }
        }

        $this->showDeleteModal = false;
        $this->delete_id = null;
        $this->delete_info = '';
    }

    public function selectAllContacts(): void
    {
        $this->contact_ids = $this->contacts->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    public function clearAllContacts(): void
    {
        $this->contact_ids = [];
    }

    public function resetCreateForm(): void
    {
        $this->template_pesan_id = '';
        $this->contact_ids = [];
        $this->waktu = '';
    }

    public function setFeedback(string $message, string $type = 'success'): void
    {
        $this->feedbackMessage = $message;
        $this->feedbackType = $type;
    }

    public function render()
    {
        $schedulers = $this->getAuthorizedQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('templatePesan', function ($tq) {
                        $tq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('contact', function ($cq) {
                        $cq->where('nama', 'like', '%' . $this->search . '%')
                           ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->filter_status, function ($query) {
                $query->where('status', $this->filter_status);
            })
            ->latest('waktu')
            ->paginate(10);

        return view('livewire.message-schedulers.index', [
            'schedulers' => $schedulers,
        ]);
    }
}
