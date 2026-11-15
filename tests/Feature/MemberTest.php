<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(); // petugas
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5',
        ], $override);
    }

    // ---------- Akses ----------

    public function test_guest_is_redirected_to_login(): void
    {
        $member = Member::factory()->create();

        $this->get(route('members.index'))->assertRedirect(route('login'));
        $this->get(route('members.show', $member))->assertRedirect(route('login'));
        $this->post(route('members.store'), $this->validData())->assertRedirect(route('login'));
        $this->delete(route('members.destroy', $member))->assertRedirect(route('login'));
    }

    public function test_petugas_and_admin_can_manage_members(): void
    {
        foreach ([$this->user, User::factory()->admin()->create()] as $actor) {
            $this->actingAs($actor)->get(route('members.index'))->assertOk();
            $this->actingAs($actor)->get(route('members.create'))->assertOk();
        }
    }

    // ---------- Daftar, pencarian, pagination ----------

    public function test_index_lists_members_with_contact_info(): void
    {
        Member::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'phone' => '081234567890']);

        $this->actingAs($this->user)
            ->get(route('members.index'))
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertSee('siti@example.com')
            ->assertSee('081234567890');
    }

    public function test_index_shows_number_of_active_borrowings(): void
    {
        $member = Member::factory()->create();
        Borrowing::factory()->for($member)->create();
        Borrowing::factory()->for($member)->returned()->create();

        $this->actingAs($this->user)
            ->get(route('members.index'))
            ->assertViewHas('members', fn ($members) => $members->first()->active_borrowings_count === 1);
    }

    public function test_index_can_search_by_name_email_and_phone(): void
    {
        Member::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'phone' => '081111111111']);
        Member::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '082222222222']);

        $this->actingAs($this->user)
            ->get(route('members.index', ['q' => 'siti']))
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Santoso');

        $this->actingAs($this->user)
            ->get(route('members.index', ['q' => 'budi@example']))
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah');

        $this->actingAs($this->user)
            ->get(route('members.index', ['q' => '0811']))
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Santoso');
    }

    public function test_index_is_paginated_and_keeps_search_filter(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Member::factory()->create(['name' => "Anggota {$i}"]);
        }

        $this->actingAs($this->user)
            ->get(route('members.index', ['q' => 'Anggota']))
            ->assertSee('Halaman 1 dari 2')
            ->assertSee('q=Anggota', false);
    }

    public function test_index_shows_empty_states(): void
    {
        $this->actingAs($this->user)
            ->get(route('members.index'))
            ->assertSee('Belum ada anggota');

        Member::factory()->create();

        $this->actingAs($this->user)
            ->get(route('members.index', ['q' => 'tidak-ada-yang-cocok']))
            ->assertSee('Anggota tidak ditemukan');
    }

    // ---------- Detail dan riwayat ----------

    public function test_show_displays_member_details_and_borrowing_history(): void
    {
        $member = Member::factory()->create(['name' => 'Siti Aminah', 'address' => 'Jl. Melati No. 5']);
        $petugas = User::factory()->create(['name' => 'Petugas Rina']);

        $active = Borrowing::factory()->for($member)->for($petugas)->create();
        $active->books()->attach(Book::factory()->create(['title' => 'Bumi Manusia']));

        $late = Borrowing::factory()->for($member)->overdue()->create();
        $late->books()->attach(Book::factory()->create(['title' => 'Laskar Pelangi']));

        $done = Borrowing::factory()->for($member)->returned()->create();
        $done->books()->attach(Book::factory()->create(['title' => 'Negeri Lima Menara']));

        $this->actingAs($this->user)
            ->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertSee('Jl. Melati No. 5')
            ->assertSee('Bumi Manusia')
            ->assertSee('Laskar Pelangi')
            ->assertSee('Negeri Lima Menara')
            ->assertSee('Petugas Rina')
            ->assertSee('Dipinjam')
            ->assertSee('Terlambat')
            ->assertSee('Dikembalikan');
    }

    public function test_show_without_history_displays_empty_state(): void
    {
        $member = Member::factory()->create();

        $this->actingAs($this->user)
            ->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Belum ada peminjaman');
    }

    // ---------- Tambah, ubah, hapus ----------

    public function test_user_can_create_member(): void
    {
        $this->actingAs($this->user)
            ->post(route('members.store'), $this->validData())
            ->assertRedirect(route('members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', ['name' => 'Siti Aminah', 'email' => 'siti@example.com']);
    }

    public function test_only_name_is_required(): void
    {
        $this->actingAs($this->user)
            ->post(route('members.store'), ['name' => 'Tanpa Kontak'])
            ->assertRedirect(route('members.index'));

        $this->assertDatabaseHas('members', ['name' => 'Tanpa Kontak', 'email' => null, 'phone' => null]);
    }

    public function test_create_member_rejects_invalid_data(): void
    {
        Member::factory()->create(['email' => 'siti@example.com']);

        $this->actingAs($this->user)
            ->post(route('members.store'), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'Nama wajib diisi.']);

        $this->actingAs($this->user)
            ->post(route('members.store'), $this->validData(['email' => 'bukan-email']))
            ->assertSessionHasErrors('email');

        $this->actingAs($this->user)
            ->post(route('members.store'), $this->validData(['email' => 'siti@example.com']))
            ->assertSessionHasErrors(['email' => 'Email sudah terdaftar.']);

        $this->actingAs($this->user)
            ->post(route('members.store'), $this->validData(['email' => 'lain@example.com', 'phone' => 'abc123']))
            ->assertSessionHasErrors(['phone' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda +, -, dan tanda kurung.']);

        $this->assertDatabaseCount('members', 1);
    }

    public function test_user_can_update_member_and_keep_its_own_email(): void
    {
        $member = Member::factory()->create(['email' => 'siti@example.com']);

        $this->actingAs($this->user)
            ->put(route('members.update', $member), $this->validData(['name' => 'Siti Diubah']))
            ->assertRedirect(route('members.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Siti Diubah']);
    }

    public function test_user_can_delete_member_without_history(): void
    {
        $member = Member::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('members.destroy', $member))
            ->assertRedirect(route('members.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($member);
    }

    public function test_member_with_borrowing_history_cannot_be_deleted(): void
    {
        $borrowing = Borrowing::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('members.destroy', $borrowing->member))
            ->assertSessionHas('error');

        $this->assertModelExists($borrowing->member);
    }
}
