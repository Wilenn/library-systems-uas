<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(); // petugas
        $this->member = Member::factory()->create(['name' => 'Siti Aminah']);
    }

    private function checkout(array $bookIds, ?User $actor = null, array $override = [])
    {
        return $this->actingAs($actor ?? $this->user)->post(route('borrowings.store'), array_merge([
            'member_id' => $this->member->id,
            'book_ids' => $bookIds,
        ], $override));
    }

    // ---------- Akses ----------

    public function test_guest_cannot_open_or_submit_the_form(): void
    {
        $book = Book::factory()->create();

        $this->get(route('borrowings.create'))->assertRedirect(route('login'));
        $this->post(route('borrowings.store'), [
            'member_id' => $this->member->id,
            'book_ids' => [$book->id],
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('borrowings', 0);
    }

    public function test_admin_and_petugas_can_open_the_form(): void
    {
        foreach ([$this->user, User::factory()->admin()->create()] as $actor) {
            $this->actingAs($actor)->get(route('borrowings.create'))->assertOk();
        }
    }

    // ---------- Form ----------

    public function test_form_lists_members_and_only_books_in_stock(): void
    {
        Book::factory()->create(['title' => 'Bumi Manusia', 'stock' => 3]);
        Book::factory()->outOfStock()->create(['title' => 'Buku Kosong']);

        $this->actingAs($this->user)
            ->get(route('borrowings.create'))
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertSee('Bumi Manusia')
            ->assertDontSee('Buku Kosong')
            ->assertSee(today()->addDays(7)->format('d/m/Y'));
    }

    public function test_form_preselects_member_from_query_string(): void
    {
        Book::factory()->create();

        $this->actingAs($this->user)
            ->get(route('borrowings.create', ['member' => $this->member->id]))
            ->assertSee('value="'.$this->member->id.'" selected', false);
    }

    public function test_form_warns_when_there_are_no_books_or_members(): void
    {
        $this->member->delete();

        $this->actingAs($this->user)
            ->get(route('borrowings.create'))
            ->assertSee('Belum ada anggota')
            ->assertSee('Tidak ada buku dengan stok tersedia');
    }

    public function test_member_page_and_list_link_to_the_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('members.show', $this->member))
            ->assertSee('borrowings/create?member='.$this->member->id, false);

        $this->actingAs($this->user)
            ->get(route('borrowings.index'))
            ->assertSee(route('borrowings.create'));
    }

    // ---------- Pencatatan ----------

    public function test_checkout_records_borrowing_and_decreases_stock(): void
    {
        $a = Book::factory()->create(['stock' => 3]);
        $b = Book::factory()->create(['stock' => 1]);

        $response = $this->checkout([$a->id, $b->id]);

        $borrowing = Borrowing::firstOrFail();

        $response->assertRedirect(route('borrowings.show', $borrowing));
        $response->assertSessionHas('success');

        $this->assertSame($this->user->id, $borrowing->user_id);
        $this->assertSame($this->member->id, $borrowing->member_id);
        $this->assertSame(Borrowing::STATUS_BORROWED, $borrowing->status);
        $this->assertNull($borrowing->returned_at);
        $this->assertTrue($borrowing->borrowed_at->equalTo(today()));
        $this->assertTrue($borrowing->due_date->equalTo(today()->addDays(7)));
        $this->assertEqualsCanonicalizing([$a->id, $b->id], $borrowing->books->pluck('id')->all());

        $this->assertSame(2, $a->fresh()->stock);
        $this->assertSame(0, $b->fresh()->stock);
    }

    public function test_checkout_records_the_logged_in_admin_as_the_officer(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();

        $this->checkout([$book->id], $admin);

        $this->assertSame($admin->id, Borrowing::firstOrFail()->user_id);
    }

    public function test_last_copy_can_only_be_borrowed_once(): void
    {
        $book = Book::factory()->create(['title' => 'Bumi Manusia', 'stock' => 1]);
        $other = Member::factory()->create();

        $this->checkout([$book->id])->assertSessionHasNoErrors();

        $this->checkout([$book->id], null, ['member_id' => $other->id])
            ->assertSessionHasErrors(['book_ids' => 'Stok habis untuk: Bumi Manusia.']);

        $this->assertDatabaseCount('borrowings', 1);
        $this->assertSame(0, $book->fresh()->stock);
    }

    public function test_checkout_is_atomic_when_one_book_is_out_of_stock(): void
    {
        $available = Book::factory()->create(['stock' => 5]);
        $empty = Book::factory()->outOfStock()->create(['title' => 'Buku Kosong']);

        $this->checkout([$available->id, $empty->id])
            ->assertSessionHasErrors(['book_ids' => 'Stok habis untuk: Buku Kosong.']);

        $this->assertDatabaseCount('borrowings', 0);
        $this->assertDatabaseCount('book_borrowing', 0);
        $this->assertSame(5, $available->fresh()->stock);
    }

    // ---------- Validasi ----------

    public function test_checkout_requires_member_and_at_least_one_book(): void
    {
        $this->checkout([], null, ['member_id' => ''])
            ->assertSessionHasErrors([
                'member_id' => 'Pilih anggota yang meminjam.',
                'book_ids' => 'Pilih minimal satu buku.',
            ]);

        $this->assertDatabaseCount('borrowings', 0);
    }

    public function test_checkout_rejects_duplicate_and_unknown_books(): void
    {
        $book = Book::factory()->create();

        $this->checkout([$book->id, $book->id])->assertSessionHasErrors('book_ids.*');
        $this->checkout([$book->id, 9999])->assertSessionHasErrors('book_ids.1');
        $this->checkout([$book->id], null, ['member_id' => 9999])->assertSessionHasErrors('member_id');

        $this->assertDatabaseCount('borrowings', 0);
        $this->assertSame($book->stock, $book->fresh()->stock);
    }

    public function test_validation_error_keeps_selected_books_on_the_form(): void
    {
        $book = Book::factory()->create(['title' => 'Bumi Manusia']);

        $this->followingRedirects()
            ->from(route('borrowings.create'))
            ->actingAs($this->user)
            ->post(route('borrowings.store'), ['member_id' => '', 'book_ids' => [$book->id]])
            ->assertSee('Pilih anggota yang meminjam.')
            ->assertSee('value="'.$book->id.'" checked', false);
    }
}
