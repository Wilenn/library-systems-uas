<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_form_for_guest(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    public function test_login_failure_shows_message_under_email_field(): void
    {
        $user = User::factory()->create();

        $this->followingRedirects()
            ->from(route('login'))
            ->post(route('login.submit'), ['email' => $user->email, 'password' => 'salah-total'])
            ->assertSee('Email atau password tidak sesuai.');
    }

    public function test_layout_shows_current_user_and_main_navigation(): void
    {
        $user = User::factory()->create(['name' => 'Siti Petugas']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Siti Petugas')
            ->assertSee('Petugas')
            ->assertSee(route('books.index'))
            ->assertSee(route('categories.index'))
            ->assertSee(route('members.index'));
    }

    public function test_users_menu_is_visible_for_admin_only(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard'))
            ->assertSee(route('users.index'))
            ->assertSee('Administrasi');

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertDontSee(route('users.index'))
            ->assertDontSee('Administrasi');
    }

    public function test_flash_messages_are_displayed(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession(['success' => 'Berhasil disimpan.', 'error' => 'Terjadi masalah.'])
            ->get(route('dashboard'))
            ->assertSee('Berhasil disimpan.')
            ->assertSee('Terjadi masalah.');
    }
}
