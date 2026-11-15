<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class BorrowingReturnTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(); // petugas
    }

    /**
     * Peminjaman aktif dengan buku-buku yang stoknya sudah berkurang
     * (nilai $stocks adalah stok SAAT INI, setelah dipinjam).
     *
     * @return array{0: Borrowing, 1: Collection<int, Book>}
     */
    private function activeBorrowing(array $stocks, string $state = 'active'): array
    {
        $factory = Borrowing::factory();
        $borrowing = $state === 'overdue' ? $factory->overdue()->create() : $factory->create();

        $books = collect($stocks)->map(fn (int $stock) => Book::factory()->create(['stock' => $stock]));
        $borrowing->books()->attach($books->pluck('id'));

        return [$borrowing, $books];
    }

    // ---------- Akses ----------

    public function test_guest_cannot_return_books(): void
    {
        [$borrowing, $books] = $this->activeBorrowing([2]);

        $this->patch(route('borrowings.return', $borrowing))->assertRedirect(route('login'));

        $this->assertTrue($borrowing->fresh()->isBorrowed());
        $this->assertSame(2, $books->first()->fresh()->stock);
    }

    public function test_admin_and_petugas_can_return_books(): void
    {
        foreach ([$this->user, User::factory()->admin()->create()] as $actor) {
            [$borrowing] = $this->activeBorrowing([1]);

            $this->actingAs($actor)
                ->patch(route('borrowings.return', $borrowing))
                ->assertRedirect(route('borrowings.show', $borrowing));

            $this->assertFalse($borrowing->fresh()->isBorrowed());
        }
    }

    // ---------- Pengembalian ----------

    public function test_return_updates_status_date_and_increases_stock(): void
    {
        [$borrowing, $books] = $this->activeBorrowing([0, 4]);

        $this->actingAs($this->user)
            ->patch(route('borrowings.return', $borrowing))
            ->assertRedirect(route('borrowings.show', $borrowing))
            ->assertSessionHas('success');

        $borrowing->refresh();

        $this->assertSame(Borrowing::STATUS_RETURNED, $borrowing->status);
        $this->assertTrue($borrowing->returned_at->equalTo(today()));
        $this->assertSame(1, $books[0]->fresh()->stock);
        $this->assertSame(5, $books[1]->fresh()->stock);
    }

    public function test_overdue_borrowing_can_still_be_returned(): void
    {
        [$borrowing, $books] = $this->activeBorrowing([1], 'overdue');

        $this->assertTrue($borrowing->isOverdue());

        $this->actingAs($this->user)->patch(route('borrowings.return', $borrowing));

        $borrowing->refresh();

        $this->assertSame(Borrowing::STATUS_RETURNED, $borrowing->status);
        $this->assertFalse($borrowing->isOverdue());
        $this->assertSame(2, $books->first()->fresh()->stock);
    }

    public function test_returning_twice_does_not_increase_stock_again(): void
    {
        [$borrowing, $books] = $this->activeBorrowing([3]);

        $this->actingAs($this->user)->patch(route('borrowings.return', $borrowing));
        $this->actingAs($this->user)
            ->patch(route('borrowings.return', $borrowing))
            ->assertSessionHas('error');

        $this->assertSame(4, $books->first()->fresh()->stock);
    }

    public function test_returning_one_borrowing_does_not_touch_another_borrowing_of_the_same_book(): void
    {
        $book = Book::factory()->create(['stock' => 0]);
        $first = Borrowing::factory()->create();
        $second = Borrowing::factory()->create();
        $first->books()->attach($book);
        $second->books()->attach($book);

        $this->actingAs($this->user)->patch(route('borrowings.return', $first));

        $this->assertSame(1, $book->fresh()->stock);
        $this->assertTrue($second->fresh()->isBorrowed());
    }

    // ---------- Tampilan ----------

    public function test_return_button_is_only_shown_for_active_borrowings(): void
    {
        [$active] = $this->activeBorrowing([1]);
        $done = Borrowing::factory()->returned()->create();

        $this->actingAs($this->user)
            ->get(route('borrowings.show', $active))
            ->assertSee(route('borrowings.return', $active))
            ->assertSee('Kembalikan buku');

        $this->actingAs($this->user)
            ->get(route('borrowings.show', $done))
            ->assertDontSee('Kembalikan buku');
    }

    public function test_show_page_reflects_the_return(): void
    {
        [$borrowing] = $this->activeBorrowing([1]);

        $this->followingRedirects()
            ->actingAs($this->user)
            ->patch(route('borrowings.return', $borrowing))
            ->assertSee('Pengembalian berhasil dicatat')
            ->assertSee('Dikembalikan')
            ->assertDontSee('Kembalikan buku');
    }

    // ---------- Siklus penuh ----------

    public function test_full_cycle_checkout_then_return_restores_original_stock(): void
    {
        $member = Member::factory()->create();
        $a = Book::factory()->create(['stock' => 3]);
        $b = Book::factory()->create(['stock' => 1]);

        $this->actingAs($this->user)->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_ids' => [$a->id, $b->id],
        ]);

        $this->assertSame(2, $a->fresh()->stock);
        $this->assertSame(0, $b->fresh()->stock);

        $borrowing = Borrowing::firstOrFail();

        $this->actingAs($this->user)->patch(route('borrowings.return', $borrowing));

        $this->assertSame(3, $a->fresh()->stock);
        $this->assertSame(1, $b->fresh()->stock);
        $this->assertSame(Borrowing::STATUS_RETURNED, $borrowing->fresh()->status);
    }
}
