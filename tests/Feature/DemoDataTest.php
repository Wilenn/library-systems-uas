<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Database\Seeders\BookSeeder;
use Database\Seeders\BorrowingSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MemberSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoDataTest extends TestCase
{
    use RefreshDatabase;

    private function seedBaseData(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(MemberSeeder::class);
        $this->seed(BookSeeder::class);
    }

    public function test_demo_borrowings_cover_active_overdue_and_returned_cases(): void
    {
        $this->seedBaseData();
        $this->seed(BorrowingSeeder::class);

        $this->assertSame(5, Borrowing::count());
        $this->assertSame(3, Borrowing::active()->count());
        $this->assertSame(2, Borrowing::overdue()->count());
        $this->assertSame(2, Borrowing::returned()->count());

        foreach (Borrowing::with('books')->get() as $borrowing) {
            $this->assertGreaterThanOrEqual(1, $borrowing->books->count());
            $this->assertSame($borrowing->isBorrowed(), $borrowing->returned_at === null);
            $this->assertTrue($borrowing->due_date->equalTo($borrowing->borrowed_at->copy()->addDays(Borrowing::LOAN_DAYS)));
        }
    }

    public function test_demo_borrowings_reduce_stock_only_for_active_transactions(): void
    {
        $this->seedBaseData();
        $stockBefore = Book::sum('stock');

        $this->seed(BorrowingSeeder::class);

        $activeBookRows = DB::table('book_borrowing')
            ->join('borrowings', 'borrowings.id', '=', 'book_borrowing.borrowing_id')
            ->where('borrowings.status', Borrowing::STATUS_BORROWED)
            ->count();

        $this->assertGreaterThan(0, $activeBookRows);
        $this->assertEquals($stockBefore - $activeBookRows, Book::sum('stock'));
    }

    public function test_borrowing_seeder_is_safe_to_run_twice(): void
    {
        $this->seedBaseData();
        $this->seed(BorrowingSeeder::class);
        $stockAfterFirstRun = Book::sum('stock');

        $this->seed(BorrowingSeeder::class);

        $this->assertSame(5, Borrowing::count());
        $this->assertEquals($stockAfterFirstRun, Book::sum('stock'));
    }

    public function test_borrowing_seeder_does_not_touch_existing_borrowings(): void
    {
        $this->seedBaseData();
        Borrowing::factory()->create();

        $this->seed(BorrowingSeeder::class);

        $this->assertSame(1, Borrowing::count());
    }

    public function test_full_seed_gives_the_dashboard_meaningful_numbers(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('jumlahJudul', 13)
            ->assertViewHas('jumlahMember', 5)
            ->assertViewHas('jumlahDipinjam', 3)
            ->assertViewHas('jumlahTerlambat', 2);
    }
}
