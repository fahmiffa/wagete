<?php

namespace Tests\Feature;

use App\Livewire\Contacts\Index as ContactsIndex;
use App\Models\App as ClientApp;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_contacts_page(): void
    {
        $response = $this->get(route('contacts.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_with_level_1_cannot_access_contacts_page(): void
    {
        $user = User::factory()->create(['level' => 1]);

        $response = $this->actingAs($user)->get(route('contacts.index'));
        $response->assertForbidden();
    }

    public function test_authenticated_user_with_level_2_can_access_contacts_page(): void
    {
        $user = User::factory()->create(['level' => 2]);

        $response = $this->actingAs($user)->get(route('contacts.index'));
        $response->assertOk();
    }

    public function test_user_can_create_contact_for_app(): void
    {
        $user = User::factory()->create(['role' => 1, 'level' => 2]);
        $app = ClientApp::create([
            'user_id' => $user->id,
            'name' => 'Toko Kasir App',
            'key' => 'wag_sample_key_123',
        ]);

        Livewire::actingAs($user)
            ->test(ContactsIndex::class)
            ->call('openCreateModal')
            ->set('app_id', $app->id)
            ->set('nama', 'Budi Santoso')
            ->set('phone', '081234567890')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'app_id' => $app->id,
            'nama' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);
    }

    public function test_user_can_update_contact(): void
    {
        $user = User::factory()->create(['role' => 1, 'level' => 2]);
        $app = ClientApp::create([
            'user_id' => $user->id,
            'name' => 'Toko Kasir App',
            'key' => 'wag_sample_key_123',
        ]);
        $contact = Contact::create([
            'app_id' => $app->id,
            'nama' => 'Old Name',
            'phone' => '0811111111',
        ]);

        Livewire::actingAs($user)
            ->test(ContactsIndex::class)
            ->call('edit', $contact->id)
            ->set('edit_nama', 'Updated Name')
            ->set('edit_phone', '0899999999')
            ->call('update')
            ->assertHasNoErrors();

        $contact->refresh();
        $this->assertEquals('Updated Name', $contact->nama);
        $this->assertEquals('0899999999', $contact->phone);
    }

    public function test_user_can_delete_contact(): void
    {
        $user = User::factory()->create(['role' => 1, 'level' => 2]);
        $app = ClientApp::create([
            'user_id' => $user->id,
            'name' => 'Toko Kasir App',
            'key' => 'wag_sample_key_123',
        ]);
        $contact = Contact::create([
            'app_id' => $app->id,
            'nama' => 'To Delete',
            'phone' => '0812222222',
        ]);

        Livewire::actingAs($user)
            ->test(ContactsIndex::class)
            ->call('confirmDelete', $contact->id)
            ->call('destroy');

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_admin_can_manage_contacts_from_any_app(): void
    {
        $admin = User::factory()->create(['role' => 0, 'level' => 2]);
        $otherUser = User::factory()->create(['role' => 1]);
        $otherApp = ClientApp::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Client App',
            'key' => 'wag_other_key_999',
        ]);

        Livewire::actingAs($admin)
            ->test(ContactsIndex::class)
            ->call('openCreateModal')
            ->set('app_id', $otherApp->id)
            ->set('nama', 'Admin Added Contact')
            ->set('phone', '0855555555')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'app_id' => $otherApp->id,
            'nama' => 'Admin Added Contact',
        ]);
    }
}
