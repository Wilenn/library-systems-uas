<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'name' => 'Petugas Baru',
            'email' => 'baru@example.com',
            'role' => User::ROLE_PETUGAS,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ], $override);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_petugas_cannot_access_user_management(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->get(route('users.index'))->assertForbidden();
        $this->actingAs($petugas)->get(route('users.create'))->assertForbidden();
        $this->actingAs($petugas)->post(route('users.store'), $this->validData())->assertForbidden();
        $this->actingAs($petugas)->delete(route('users.destroy', $this->admin))->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
        $this->assertModelExists($this->admin);
    }

    public function test_petugas_still_has_access_to_shared_modules(): void
    {
        $petugas = User::factory()->create();

        foreach (['dashboard', 'books.index', 'categories.index', 'members.index'] as $route) {
            $this->actingAs($petugas)->get(route($route))->assertOk();
        }
    }

    public function test_admin_sees_user_list_and_users_menu(): void
    {
        $other = User::factory()->create(['name' => 'Petugas Satu']);

        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($other->name)
            ->assertSee($other->email)
            ->assertSee(route('users.index'));
    }

    public function test_admin_can_create_user_with_hashed_password(): void
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), $this->validData())
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $user = User::where('email', 'baru@example.com')->firstOrFail();

        $this->assertSame(User::ROLE_PETUGAS, $user->role);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_create_user_rejects_invalid_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), $this->validData([
            'email' => $this->admin->email, // sudah terdaftar
            'role' => 'superadmin',         // bukan role yang tersedia
            'password' => 'pendek',
            'password_confirmation' => 'beda',
        ]));

        $response->assertSessionHasErrors(['email', 'role', 'password']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_admin_can_update_user_and_keep_password_when_left_blank(): void
    {
        $user = User::factory()->create();
        $oldHash = $user->password;

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), $this->validData([
                'name' => 'Nama Diubah',
                'email' => $user->email, // email sendiri tidak dianggap duplikat
                'role' => User::ROLE_ADMIN,
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertRedirect(route('users.index'));

        $user->refresh();

        $this->assertSame('Nama Diubah', $user->name);
        $this->assertTrue($user->isAdmin());
        $this->assertSame($oldHash, $user->password);
    }

    public function test_admin_can_change_another_users_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), $this->validData([
                'email' => $user->email,
                'password' => 'passwordbaru1',
                'password_confirmation' => 'passwordbaru1',
            ]))
            ->assertRedirect(route('users.index'));

        $this->assertTrue(Hash::check('passwordbaru1', $user->fresh()->password));
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $this->actingAs($this->admin)
            ->put(route('users.update', $this->admin), $this->validData([
                'email' => $this->admin->email,
                'role' => User::ROLE_PETUGAS,
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertRedirect(route('users.index'));

        $this->assertTrue($this->admin->fresh()->isAdmin());
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->admin))
            ->assertSessionHas('error');

        $this->assertModelExists($this->admin);
    }

    public function test_user_with_borrowing_history_cannot_be_deleted(): void
    {
        $borrowing = Borrowing::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $borrowing->user))
            ->assertSessionHas('error');

        $this->assertModelExists($borrowing->user);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($user);
    }
}
