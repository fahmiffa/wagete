<?php

namespace App\Livewire\Templates;

use App\Models\App as ClientApp;
use App\Models\TemplatePesan;
use App\Services\WhatsappService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Search
    public string $search = '';

    // Modal Visibility
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    // Create Form Fields
    public string $name = '';
    public string $pesan = '';

    // Edit Form Fields
    public ?int $templateId = null;
    public string $edit_name = '';
    public string $edit_pesan = '';

    // Delete Target
    public ?int $delete_template_id = null;
    public string $delete_template_name = '';

    // Flash notification message
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    // Send Message Modal
    public bool $showSendModal = false;
    public ?int $send_template_id = null;
    public string $send_template_name = '';
    public string $send_pesan = '';
    public string $send_phone = '';
    public ?string $sendResultMessage = null;
    public string $sendResultType = 'success';

    public function mount(): void
    {
        // Enforce level 2 access only
        if (! auth()->check() || (int) auth()->user()->level !== 2) {
            abort(403, 'Akses ditolak. Menu Template Pesan hanya dapat diakses oleh pengguna dengan Level 2.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Query scoped to authorized templates
     * (Admin role 0 can view all, regular users can only see their own)
     */
    protected function getAuthorizedQuery(): Builder
    {
        $user = auth()->user();
        $query = TemplatePesan::with('user');

        if ((int) $user->role !== 0) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Open create modal
     */
    #[On('trigger-open-template-create-modal')]
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    /**
     * Save new template
     */
    public function store(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'Nama template pesan wajib diisi.',
            'pesan.required' => 'Isi pesan template wajib diisi.',
        ]);

        TemplatePesan::create([
            'user_id' => auth()->id(),
            'name' => trim($this->name),
            'pesan' => trim($this->pesan),
        ]);

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->setFeedback('Template pesan baru berhasil ditambahkan!');
    }

    /**
     * Open edit modal
     */
    public function edit(int $id): void
    {
        $this->resetValidation();
        $template = $this->getAuthorizedQuery()->findOrFail($id);

        $this->templateId = $template->id;
        $this->edit_name = $template->name;
        $this->edit_pesan = $template->pesan;

        $this->showEditModal = true;
    }

    /**
     * Update template
     */
    public function update(): void
    {
        $this->validate([
            'edit_name' => ['required', 'string', 'max:255'],
            'edit_pesan' => ['required', 'string', 'max:5000'],
        ], [
            'edit_name.required' => 'Nama template pesan wajib diisi.',
            'edit_pesan.required' => 'Isi pesan template wajib diisi.',
        ]);

        $template = $this->getAuthorizedQuery()->findOrFail($this->templateId);

        $template->update([
            'name' => trim($this->edit_name),
            'pesan' => trim($this->edit_pesan),
        ]);

        $this->showEditModal = false;
        $this->setFeedback('Template pesan berhasil diperbarui!');
    }

    /**
     * Confirm delete modal
     */
    public function confirmDelete(int $id): void
    {
        $template = $this->getAuthorizedQuery()->findOrFail($id);
        $this->delete_template_id = $template->id;
        $this->delete_template_name = $template->name;
        $this->showDeleteModal = true;
    }

    /**
     * Delete template
     */
    public function destroy(): void
    {
        if ($this->delete_template_id) {
            $template = $this->getAuthorizedQuery()->find($this->delete_template_id);

            if ($template) {
                $template->delete();
                $this->setFeedback("Template pesan '{$this->delete_template_name}' berhasil dihapus!");
            }
        }

        $this->showDeleteModal = false;
        $this->delete_template_id = null;
        $this->delete_template_name = '';
    }

    public function resetCreateForm(): void
    {
        $this->name = '';
        $this->pesan = '';
    }

    /**
     * Open the send message simulator modal
     */
    public function openSendModal(int $id): void
    {
        $template = $this->getAuthorizedQuery()->findOrFail($id);

        $this->send_template_id = $template->id;
        $this->send_template_name = $template->name;
        $this->send_pesan = $template->pesan;
        $this->send_phone = '';
        $this->sendResultMessage = null;
        $this->sendResultType = 'success';
        $this->resetValidation();

        $this->showSendModal = true;
    }

    /**
     * Submit send message via WhatsApp API
     */
    public function submitSendMessage(): void
    {
        $this->validate([
            'send_phone' => ['required', 'string', 'min:8', 'max:20'],
        ], [
            'send_phone.required' => 'Nomor WhatsApp tujuan wajib diisi.',
            'send_phone.min' => 'Nomor WhatsApp minimal 8 digit.',
        ]);

        // Resolve device_id from user's App
        $user = auth()->user();
        $app = ClientApp::where('user_id', $user->id)->first();

        if (! $app || empty($app->nomor_hp)) {
            $this->sendResultMessage = 'Gagal: Belum ada device/nomor HP yang terhubung. Silakan setup WhatsApp terlebih dahulu.';
            $this->sendResultType = 'error';
            return;
        }

        $deviceId = $app->nomor_hp;

        try {
            $service = app(WhatsappService::class);
            $result = $service->sendMessage($deviceId, $this->send_phone, $this->send_pesan);

            if ($result['success']) {
                $this->sendResultMessage = 'Pesan berhasil dikirim ke ' . $this->send_phone . '!';
                $this->sendResultType = 'success';
            } else {
                $errorMsg = $result['data']['message'] ?? 'Gagal mengirim pesan.';
                $this->sendResultMessage = 'Gagal: ' . $errorMsg;
                $this->sendResultType = 'error';
            }
        } catch (\Exception $e) {
            $this->sendResultMessage = 'Error: ' . $e->getMessage();
            $this->sendResultType = 'error';
        }
    }

    public function setFeedback(string $message, string $type = 'success'): void
    {
        $this->feedbackMessage = $message;
        $this->feedbackType = $type;
    }

    public function render()
    {
        $templates = $this->getAuthorizedQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('pesan', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($uq) {
                          $uq->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.templates.index', [
            'templates' => $templates,
        ]);
    }
}
