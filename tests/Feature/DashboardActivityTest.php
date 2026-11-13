<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardActivityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function memberNamed(string $name): Member
    {
        return Member::factory()->create(['name' => $name]);
    }

    private function dashboard()
    {
        return $this->actingAs($this->user)->get(route('dashboard'));
    }

    // ---------- Terlambat ----------

    public function test_overdue_list_contains_only_overdue_borrowings(): void
    {
        Borrowing::factory()->for($this->memberNamed('Anggota Tepat'))->create();
        Borrowing::factory()->for($this->memberNamed('Anggota Telat'))->overdue()->create();
        Borrowing::factory()->for($this->memberNamed('Anggota Selesai'))->returned()->create();

        $this->dashboard()
            ->assertOk()
            ->assertViewHas('jumlahTerlambat', 1)
            ->assertViewHas('terlambat', fn ($list) => $list->pluck('member.name')->all() === ['Anggota Telat'])
            ->assertSee('Terlambat 3 hari');
    }

    public function test_both_lists_describe_the_same_borrowing_in_the_same_format(): void
    {
        $borrowing = Borrowing::factory()->for($this->memberNamed('Anggota Telat'))->overdue()->create();
        $format = 'Jatuh tempo '.$borrowing->due_date->format('d/m/Y').' · 0 buku';

        $content = $this->dashboard()->assertOk()->getContent();

        // Transaksi yang sama muncul di dua daftar dengan keterangan yang persis sama.
        $this->assertSame(2, substr_count($content, $format));
        $this->assertSame(2, substr_count($content, 'Anggota Telat'));
    }

    public function test_overdue_list_is_limited_to_five_and_oldest_comes_first(): void
    {
        foreach (range(1, 7) as $days) {
            Borrowing::factory()
                ->for($this->memberNamed("Anggota {$days}"))
                ->overdue()
                ->create(['due_date' => today()->subDays($days)]);
        }

        $this->dashboard()
            ->assertViewHas('jumlahTerlambat', 7)
            ->assertViewHas('terlambat', fn ($list) => $list->count() === 5
                && $list->first()->member->name === 'Anggota 7')
            ->assertSee(route('borrowings.index', ['status' => 'overdue']));
    }

    public function test_overdue_rows_link_to_the_borrowing_detail(): void
    {
        $borrowing = Borrowing::factory()->overdue()->create();

        $this->dashboard()->assertSee(route('borrowings.show', $borrowing));
    }

    // ---------- Terbaru ----------

    public function test_recent_list_shows_latest_five_including_returned(): void
    {
        foreach (range(1, 6) as $daysAgo) {
            $factory = Borrowing::factory()->for($this->memberNamed("Anggota {$daysAgo}"));

            if ($daysAgo === 2) {
                $factory = $factory->returned();
            }

            $factory->create([
                'borrowed_at' => today()->subDays($daysAgo),
                'due_date' => today()->subDays($daysAgo)->addDays(7),
            ]);
        }

        $this->dashboard()
            ->assertViewHas('terbaru', fn ($list) => $list->count() === 5
                && $list->first()->member->name === 'Anggota 1'
                && $list->pluck('member.name')->contains('Anggota 2')
                && ! $list->pluck('member.name')->contains('Anggota 6'))
            ->assertSee('Dikembalikan');
    }

    // ---------- Kondisi kosong ----------

    public function test_empty_states_are_shown_without_borrowings(): void
    {
        $this->dashboard()
            ->assertOk()
            ->assertSee('Tidak ada peminjaman yang terlambat.')
            ->assertSee('Belum ada peminjaman.')
            ->assertDontSee('Lihat semua');
    }

    // ---------- Hari terlambat ----------

    public function test_days_overdue_is_only_counted_for_overdue_borrowings(): void
    {
        $this->assertSame(3, Borrowing::factory()->overdue()->create()->daysOverdue());
        $this->assertSame(0, Borrowing::factory()->create()->daysOverdue());
        $this->assertSame(0, Borrowing::factory()->returned()->create()->daysOverdue());
    }

    // ---------- Performa ----------

    public function test_dashboard_runs_the_same_number_of_queries_regardless_of_data_size(): void
    {
        Borrowing::factory()->overdue()->create();
        $small = $this->countDashboardQueries();

        Borrowing::factory()->count(4)->overdue()->create();
        Borrowing::factory()->count(4)->create();
        $large = $this->countDashboardQueries();

        $this->assertSame($small, $large, 'Jumlah query dashboard tidak boleh bertambah mengikuti jumlah data (N+1).');
    }

    private function countDashboardQueries(): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->dashboard()->assertOk();

        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }
}
