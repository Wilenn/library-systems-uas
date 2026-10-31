<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_populate_expected_data_and_can_run_twice(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('categories', 8);
        $this->assertDatabaseCount('members', 5);
        $this->assertDatabaseCount('books', 13);
        $this->assertSame(0, Book::whereDoesntHave('category')->count());
        $this->assertTrue(User::where('email', 'admin@example.com')->firstOrFail()->isAdmin());
        $this->assertFalse(User::where('email', 'petugas@example.com')->firstOrFail()->isAdmin());
    }

    public function test_user_role_defaults_to_petugas(): void
    {
        $this->assertSame(User::ROLE_PETUGAS, User::factory()->create()->role);
        $this->assertTrue(User::factory()->admin()->create()->isAdmin());
    }

    public function test_category_and_book_relations(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($book->category->is($category));
        $this->assertCount(1, $category->books);
    }

    public function test_borrowing_relations_and_pivot(): void
    {
        $borrowing = Borrowing::factory()->create();
        $books = Book::factory()->count(2)->create();
        $borrowing->books()->attach($books->pluck('id'));

        $this->assertInstanceOf(User::class, $borrowing->user);
        $this->assertInstanceOf(Member::class, $borrowing->member);
        $this->assertCount(2, $borrowing->books);
        $this->assertCount(1, $books->first()->borrowings);
        $this->assertCount(1, $borrowing->member->borrowings);
        $this->assertCount(1, $borrowing->user->borrowings);
    }

    public function test_same_book_cannot_be_attached_twice_to_one_borrowing(): void
    {
        $borrowing = Borrowing::factory()->create();
        $book = Book::factory()->create();
        $borrowing->books()->attach($book);

        $this->expectException(QueryException::class);

        $borrowing->books()->attach($book);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $book = Book::factory()->create();

        $this->expectException(QueryException::class);

        $book->category->delete();
    }

    public function test_records_with_borrowing_history_cannot_be_deleted(): void
    {
        $borrowing = Borrowing::factory()->create();
        $borrowing->books()->attach(Book::factory()->create());

        foreach ([$borrowing->member, $borrowing->user, $borrowing->books->first()] as $record) {
            try {
                $record->delete();
                $this->fail(class_basename($record).' dengan riwayat peminjaman seharusnya tidak bisa dihapus.');
            } catch (QueryException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_available_scope_excludes_out_of_stock_books(): void
    {
        $ready = Book::factory()->create(['stock' => 2]);
        Book::factory()->outOfStock()->create();

        $this->assertSame([$ready->id], Book::available()->pluck('id')->all());
    }

    public function test_borrowing_status_scope_and_overdue_logic(): void
    {
        $onTime = Borrowing::factory()->create();
        $late = Borrowing::factory()->overdue()->create();
        $returned = Borrowing::factory()->returned()->create();

        $this->assertCount(2, Borrowing::active()->get());
        $this->assertFalse($onTime->isOverdue());
        $this->assertTrue($late->isOverdue());
        $this->assertFalse($returned->isOverdue());
        $this->assertTrue($onTime->due_date->equalTo($onTime->borrowed_at->addDays(Borrowing::LOAN_DAYS)));
    }
}
