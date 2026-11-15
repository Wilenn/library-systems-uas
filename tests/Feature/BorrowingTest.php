<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(); // petugas
    }

    private function borrowingFor(string $memberName, string $state = 'active'): Borrowing
    {
        $factory = Borrowing::factory()->for(Member::factory()->create(['name' => $memberName]));

        return match ($state) {
            'overdue' => $factory->overdue()->create(),
            'returned' => $factory->returned()->create(),
            default => $factory->create(),
        };
    }

    // ---------- Akses ----------

    public function test_guest_is_redirected_to_login(): void
    {
        $borrowing = Borrowing::factory()->create();

        $this->get(route('borrowings.index'))->assertRedirect(route('login'));
        $this->get(route('borrowings.show', $borrowing))->assertRedirect(route('login'));
    }

    public function test_admin_and_petugas_can_view_borrowings(): void
    {
        $borrowing = Borrowing::factory()->create();

        foreach ([$this->user, User::factory()->admin()->create()] as $actor) {
            $this->actingAs($actor)->get(route('borrowings.index'))->assertOk();
            $this->actingAs($actor)->get(route('borrowings.show', $borrowing))->assertOk();
        }
    }

    public function test_sidebar_links_to_borrowings(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertSee(route('borrowings.index'));
    }

    // ---------- Daftar ----------

    public function test_index_lists_borrowings_with_status_and_book_count(): void
    {
        $this->borrowingFor('Anggota Aktif');
        $this->borrowingFor('Anggota Telat', 'overdue');
        $this->borrowingFor('Anggota Selesai', 'returned');

        $this->actingAs($this->user)
            ->get(route('borrowings.index'))
            ->assertOk()
            ->assertSee('Anggota Aktif')
            ->assertSee('Anggota Telat')
            ->assertSee('Anggota Selesai')
            ->assertSee('Dipinjam')
            ->assertSee('Dikembalikan')
            ->assertViewHas('borrowings', fn ($borrowings) => $borrowings->total() === 3);
    }

    public function test_index_can_filter_by_status(): void
    {
        $this->borrowingFor('Anggota Aktif');
        $this->borrowingFor('Anggota Telat', 'overdue');
        $this->borrowingFor('Anggota Selesai', 'returned');

        $this->actingAs($this->user)
            ->get(route('borrowings.index', ['status' => 'borrowed']))
            ->assertSee('Anggota Aktif')
            ->assertSee('Anggota Telat')
            ->assertDontSee('Anggota Selesai');

        $this->actingAs($this->user)
            ->get(route('borrowings.index', ['status' => 'overdue']))
            ->assertSee('Anggota Telat')
            ->assertDontSee('Anggota Aktif')
            ->assertDontSee('Anggota Selesai');

        $this->actingAs($this->user)
            ->get(route('borrowings.index', ['status' => 'returned']))
            ->assertSee('Anggota Selesai')
            ->assertDontSee('Anggota Aktif')
            ->assertDontSee('Anggota Telat');
    }

    public function test_index_can_search_by_member_name(): void
    {
        $this->borrowingFor('Siti Aminah');
        $this->borrowingFor('Budi Santoso');

        $this->actingAs($this->user)
            ->get(route('borrowings.index', ['q' => 'siti']))
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Santoso');
    }

    public function test_index_is_paginated_and_keeps_filters(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->borrowingFor("Anggota {$i}");
        }

        $this->actingAs($this->user)
            ->get(route('borrowings.index', ['status' => 'borrowed']))
            ->assertSee('Halaman 1 dari 2')
            ->assertSee('status=borrowed', false);
    }

    public function test_index_shows_empty_states(): void
    {
        $this->actingAs($this->user)
            ->get(route('borrowings.index'))
            ->assertSee('Belum ada peminjaman');

        $this->borrowingFor('Siti Aminah');

        $this->actingAs($this->user)
            ->get(route('borrowings.index', ['q' => 'tidak-ada-yang-cocok']))
            ->assertSee('Peminjaman tidak ditemukan');
    }

    // ---------- Detail ----------

    public function test_show_displays_borrowing_details_and_books(): void
    {
        $petugas = User::factory()->create(['name' => 'Petugas Rina']);
        $borrowing = Borrowing::factory()
            ->for(Member::factory()->create(['name' => 'Siti Aminah']))
            ->for($petugas)
            ->create();
        $borrowing->books()->attach([
            Book::factory()->create(['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer'])->id,
            Book::factory()->create(['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata'])->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('borrowings.show', $borrowing))
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertSee('Petugas Rina')
            ->assertSee('Bumi Manusia')
            ->assertSee('Pramoedya Ananta Toer')
            ->assertSee('Laskar Pelangi')
            ->assertSee($borrowing->borrowed_at->format('d/m/Y'))
            ->assertSee($borrowing->due_date->format('d/m/Y'));
    }

    public function test_show_marks_overdue_and_returned_status(): void
    {
        $late = $this->borrowingFor('Anggota Telat', 'overdue');
        $done = $this->borrowingFor('Anggota Selesai', 'returned');

        $this->actingAs($this->user)
            ->get(route('borrowings.show', $late))
            ->assertSee('Terlambat');

        $this->actingAs($this->user)
            ->get(route('borrowings.show', $done))
            ->assertSee('Dikembalikan')
            ->assertSee($done->returned_at->format('d/m/Y'));
    }
}
