<?php

namespace Tests\Feature;

use App\Livewire\Apps\Index as AppsIndex;
use App\Models\App as ClientApp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_apps_page(): void
    {
        $response = $this->get(route('apps.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_users_without_role_zero_get_403_forbidden(): void
    {
        $regularUser = User::factory()->create([
            'role' => 1,
        ]);

        $response = $this->actingAs($regularUser)->get(route('apps.index'));
        $response->assertStatus(403);
    }

    public function test_users_with_role_zero_can_access_apps_page(): void
    {
        $admin = User::factory()->create([
            'role' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('apps.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_app_and_it_creates_user_account(): void
    {
        $admin = User::factory()->create([
            'role' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(AppsIndex::class)
            ->call('openCreateModal')
            ->set('name', 'WhatsApp Gateway Client 1')
            ->set('nomor_hp', '081234567890')
            ->set('alamat', 'Jl. Merdeka No. 45')
            ->set('user_name', 'Client Admin')
            ->set('email', 'client1@example.com')
            ->set('password', 'secret123')
            ->set('level', 2)
            ->call('store')
            ->assertHasNoErrors();

        // Verify User was created
        $createdUser = User::where('email', 'client1@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals('Client Admin', $createdUser->name);
        $this->assertEquals(1, $createdUser->role);
        $this->assertEquals(2, $createdUser->level);

        // Verify App was created and linked to the user
        $createdApp = ClientApp::where('user_id', $createdUser->id)->first();
        $this->assertNotNull($createdApp);
        $this->assertEquals('WhatsApp Gateway Client 1', $createdApp->name);
        $this->assertEquals('081234567890', $createdApp->nomor_hp);
        $this->assertEquals('Jl. Merdeka No. 45', $createdApp->alamat);
        $this->assertNotEmpty($createdApp->key);
        $this->assertStringStartsWith('wag_', $createdApp->key);
    }

    public function test_admin_can_update_app_and_user_account(): void
    {
        $admin = User::factory()->create(['role' => 0]);
        $clientUser = User::factory()->create([
            'email' => 'old@example.com',
            'role' => 1,
        ]);
        $app = ClientApp::create([
            'user_id' => $clientUser->id,
            'name' => 'Old App Name',
            'key' => 'wag_old_key_12345',
            'alamat' => 'Old Address',
            'nomor_hp' => '0811111111',
        ]);

        Livewire::actingAs($admin)
            ->test(AppsIndex::class)
            ->call('edit', $app->id)
            ->set('edit_name', 'Updated App Name')
            ->set('edit_nomor_hp', '0899999999')
            ->set('edit_alamat', 'New Address')
            ->set('edit_user_name', 'Updated User Name')
            ->set('edit_email', 'new@example.com')
            ->set('edit_level', 2)
            ->call('update')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertEquals('Updated App Name', $app->name);
        $this->assertEquals('0899999999', $app->nomor_hp);
        $this->assertEquals('New Address', $app->alamat);

        $clientUser->refresh();
        $this->assertEquals('Updated User Name', $clientUser->name);
        $this->assertEquals('new@example.com', $clientUser->email);
        $this->assertEquals(2, $clientUser->level);
    }

    public function test_admin_can_regenerate_api_key(): void
    {
        $admin = User::factory()->create(['role' => 0]);
        $clientUser = User::factory()->create(['role' => 1]);
        $app = ClientApp::create([
            'user_id' => $clientUser->id,
            'name' => 'API Test App',
            'key' => 'wag_initial_key_12345',
        ]);

        Livewire::actingAs($admin)
            ->test(AppsIndex::class)
            ->call('quickRegenerateKey', $app->id);

        $app->refresh();
        $this->assertNotEquals('wag_initial_key_12345', $app->key);
        $this->assertStringStartsWith('wag_', $app->key);
    }

    public function test_admin_can_delete_app_and_associated_user(): void
    {
        $admin = User::factory()->create(['role' => 0]);
        $clientUser = User::factory()->create(['role' => 1]);
        $app = ClientApp::create([
            'user_id' => $clientUser->id,
            'name' => 'To Delete App',
            'key' => 'wag_delete_key_12345',
        ]);

        Livewire::actingAs($admin)
            ->test(AppsIndex::class)
            ->call('confirmDelete', $app->id)
            ->call('destroy');

        $this->assertDatabaseMissing('apps', ['id' => $app->id]);
        $this->assertDatabaseMissing('users', ['id' => $clientUser->id]);
    }
}
