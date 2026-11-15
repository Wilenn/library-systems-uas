<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Siti Petugas']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_and_petugas_can_open_the_dashboard(): void
    {
        foreach ([$this->user, User::factory()->admin()->create()] as $actor) {
            $this->actingAs($actor)->get(route('dashboard'))->assertOk();
        }
    }

    public function test_dashboard_shows_the_four_required_metrics(): void
    {
        Book::factory()->create(['stock' => 2]);
        Book::factory()->create(['stock' => 3]);
        Book::factory()->create(['stock' => 5]);

        $members = Member::factory()->count(2)->create();
        Borrowing::factory()->for($members[0])->create();                 // dipinjam
        Borrowing::factory()->for($members[0])->overdue()->create();      // dipinjam, terlambat
        Borrowing::factory()->for($members[1])->returned()->create();     // sudah kembali

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('jumlahJudul', 3)
            ->assertViewHas('jumlahStok', 10)
            ->assertViewHas('jumlahMember', 2)
            ->assertViewHas('jumlahDipinjam', 2)
            ->assertSeeInOrder(['Judul buku', 'Total stok buku', 'Anggota', 'Sedang dipinjam']);
    }

    public function test_dashboard_shows_zeros_on_an_empty_database(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('jumlahJudul', 0)
            ->assertViewHas('jumlahStok', 0)
            ->assertViewHas('jumlahMember', 0)
            ->assertViewHas('jumlahDipinjam', 0);
    }

    public function test_large_numbers_use_thousand_separators(): void
    {
        Book::factory()->create(['stock' => 1000]);
        Book::factory()->create(['stock' => 234]);

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertSee('1.234');
    }

    public function test_metrics_link_to_the_related_pages(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertSee(route('books.index'))
            ->assertSee(route('members.index'))
            ->assertSee(route('borrowings.index', ['status' => 'borrowed']))
            ->assertSee('Halo, Siti Petugas');
    }
}
