<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordEditorModalTest extends TestCase
{
    use RefreshDatabase;

    protected function superAdmin(): User
    {
        $role = Role::create([
            'name' => 'super_admin',
            'label' => 'Super admin',
            'permissions' => ['*'],
            'level' => Role::SUPER_ADMIN_LEVEL,
        ]);

        return User::factory()->create(['role_id' => $role->id, 'is_admin' => true]);
    }

    public function test_users_list_renders_one_inert_editor_modal_per_row(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create(['name' => 'Row Target']);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
        $response->assertSee('id="modal-edit-users-'.$target->id.'"', false);
        $response->assertSee('data-modal-size="xl"', false);
        $response->assertSee("openModal('edit-users-".$target->id."')", false);
        // Every listed row gets one, including the admin's own account.
        $response->assertSee('id="modal-edit-users-'.$admin->id.'"', false);
    }

    public function test_data_browser_rows_open_the_editor_in_a_modal(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/data/users');

        $response->assertOk();
        $response->assertSee('id="modal-edit-users-'.$target->id.'"', false);
    }

    public function test_saving_from_a_modal_returns_to_the_list_it_was_opened_from(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create(['name' => 'Old name']);

        $response = $this->actingAs($admin)->put('/admin/data/users/'.$target->id, [
            'name' => 'New name',
            '_return' => '/admin/users?page=2',
        ]);

        $response->assertRedirect('/admin/users?page=2');
        $this->assertSame('New name', $target->fresh()->name);
    }

    public function test_the_modal_sends_the_list_page_it_was_opened_from(): void
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertSee('name="_return" value="'.url('/admin/users').'"', false);
    }

    public function test_saving_without_a_return_path_goes_to_the_record_page(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->put('/admin/data/users/'.$target->id, [
            'name' => 'No return given',
        ]);

        $response->assertRedirect('/admin/data/users/'.$target->id);
    }

    public function test_return_paths_that_are_not_local_are_ignored(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create();

        foreach (['https://evil.test/steal', '//evil.test/steal'] as $hostile) {
            $response = $this->actingAs($admin)->put('/admin/data/users/'.$target->id, [
                'name' => 'Guarded',
                '_return' => $hostile,
            ]);

            $response->assertRedirect('/admin/data/users/'.$target->id);
        }
    }

    public function test_a_failed_save_reopens_the_same_modal_with_the_typed_values(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create(['name' => 'Before failure']);
        $bystander = User::factory()->create(['name' => 'Bystander']);

        $page = $this->actingAs($admin)
            ->from('/admin/users')
            ->followingRedirects()
            ->put('/admin/data/users/'.$target->id, [
                'name' => 'Typed but rejected',
                'role_id' => 99999,
                '_record' => $target->id,
            ]);

        $page->assertOk();
        $page->assertSee("openModal('edit-users-".$target->id."')", false);
        $page->assertSee('name="_record" value="'.$target->id.'"', false);
        // The typed value replaces the stored one inside THAT modal...
        $page->assertSee('value="Typed but rejected"', false);
        // ...but other rows keep showing their own stored data.
        $page->assertSee('value="Bystander"', false);
        $this->assertSame('Before failure', $target->fresh()->name);
        $this->assertSame('Bystander', $bystander->fresh()->name);
    }

    public function test_the_standalone_editor_page_still_works(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/data/users/'.$target->id);

        $response->assertOk();
        $response->assertSee('Save record');
        $response->assertSee('Delete record');
    }
}
