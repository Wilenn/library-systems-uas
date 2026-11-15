<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_url_shows_custom_404_page_even_for_guests(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('Kesalahan 404')
            ->assertSee('Ke beranda');
    }

    public function test_missing_record_shows_404_without_leaking_internal_details(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('books.show', 9999))
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertDontSee('No query results')
            ->assertDontSee('App\\Models');
    }

    public function test_forbidden_page_shows_the_reason_without_the_app_sidebar(): void
    {
        $this->actingAs(User::factory()->create()) // petugas
            ->get(route('users.index'))
            ->assertForbidden()
            ->assertSee('Akses ditolak')
            ->assertSee('Anda tidak memiliki akses ke halaman ini.')
            ->assertDontSee('Administrasi');
    }

    public function test_server_error_page_hides_the_exception_message(): void
    {
        config(['app.debug' => false]);
        Route::get('/__uji-error', fn () => throw new \RuntimeException('rahasia-internal'));

        $this->get('/__uji-error')
            ->assertStatus(500)
            ->assertSee('Terjadi kesalahan')
            ->assertSee('Kesalahan 500')
            ->assertDontSee('rahasia-internal');
    }

    public function test_other_http_errors_use_the_same_custom_layout(): void
    {
        Route::get('/__uji-419', fn () => abort(419));
        Route::get('/__uji-429', fn () => abort(429));
        Route::get('/__uji-503', fn () => abort(503));

        $this->get('/__uji-419')->assertStatus(419)->assertSee('Sesi berakhir');
        $this->get('/__uji-429')->assertStatus(429)->assertSee('Terlalu banyak percobaan');
        $this->get('/__uji-503')->assertStatus(503)->assertSee('Sedang dalam pemeliharaan');
    }

    public function test_error_pages_do_not_depend_on_login_and_link_back_home(): void
    {
        Route::get('/__uji-429', fn () => abort(429));

        $this->get('/__uji-429')
            ->assertStatus(429)
            ->assertSee(url('/'))
            ->assertSee('Kembali');
    }
}
