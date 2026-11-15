<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create(['name' => 'Fiksi']);
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'year' => 2005,
            'stock' => 3,
            'category_id' => $this->category->id,
        ], $override);
    }

    private function makeBook(array $override = []): Book
    {
        return Book::create($this->validData($override));
    }

    // ---------- Daftar, pencarian, filter, pagination ----------

    public function test_book_index_lists_books(): void
    {
        $this->makeBook();

        $this->actingAs($this->user)
            ->get(route('books.index'))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertSee('Andrea Hirata')
            ->assertSee('Fiksi');
    }

    public function test_book_index_is_paginated(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->makeBook(['title' => "Buku nomor {$i}"]);
        }

        $this->actingAs($this->user)
            ->get(route('books.index'))
            ->assertOk()
            ->assertSee('Halaman 1 dari 2');
    }

    public function test_book_index_can_search_by_title_and_author(): void
    {
        $this->makeBook();
        $this->makeBook(['title' => 'Negeri Lima Menara', 'author' => 'Ahmad Fuadi']);

        $this->actingAs($this->user)
            ->get(route('books.index', ['q' => 'laskar']))
            ->assertSee('Laskar Pelangi')
            ->assertDontSee('Negeri Lima Menara');

        $this->actingAs($this->user)
            ->get(route('books.index', ['q' => 'Fuadi']))
            ->assertSee('Negeri Lima Menara')
            ->assertDontSee('Laskar Pelangi');
    }

    public function test_book_index_can_filter_by_category(): void
    {
        $other = Category::create(['name' => 'Komik']);
        $this->makeBook();
        $this->makeBook(['title' => 'One Piece Vol. 1', 'author' => 'Eiichiro Oda', 'category_id' => $other->id]);

        $this->actingAs($this->user)
            ->get(route('books.index', ['category' => $other->id]))
            ->assertSee('One Piece Vol. 1')
            ->assertDontSee('Laskar Pelangi');
    }

    public function test_pagination_links_keep_search_filter(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->makeBook(['title' => "Buku nomor {$i}"]);
        }

        $this->actingAs($this->user)
            ->get(route('books.index', ['q' => 'Buku']))
            ->assertSee('q=Buku', false);
    }

    public function test_book_index_shows_empty_states(): void
    {
        $this->actingAs($this->user)
            ->get(route('books.index'))
            ->assertSee('Belum ada buku');

        $this->makeBook();

        $this->actingAs($this->user)
            ->get(route('books.index', ['q' => 'tidak-ada-yang-cocok']))
            ->assertSee('Buku tidak ditemukan');
    }

    // ---------- Detail dan form ----------

    public function test_book_detail_page_shows_book_data(): void
    {
        $book = $this->makeBook();

        $this->actingAs($this->user)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
            ->assertSee('Andrea Hirata')
            ->assertSee('Fiksi')
            ->assertSee('2005');
    }

    public function test_create_form_lists_categories_or_warns_when_empty(): void
    {
        $this->actingAs($this->user)
            ->get(route('books.create'))
            ->assertOk()
            ->assertSee('Fiksi');

        $this->category->delete();

        $this->actingAs($this->user)
            ->get(route('books.create'))
            ->assertSee('Belum ada kategori');
    }

    // ---------- Tambah, ubah, hapus ----------

    public function test_user_can_create_book(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('books.store'), $this->validData());

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('books', ['title' => 'Laskar Pelangi', 'stock' => 3]);
    }

    public function test_create_book_rejects_invalid_data_with_indonesian_messages(): void
    {
        $this->makeBook();

        $response = $this->actingAs($this->user)->post(route('books.store'), $this->validData([
            'title' => 'Laskar Pelangi',   // duplikat
            'year' => 1900,                // di luar batas MySQL YEAR
            'category_id' => 9999,         // tidak ada
        ]));

        $response->assertSessionHasErrors(['title', 'year', 'category_id']);
        $response->assertSessionHasErrors([
            'year' => 'Tahun terbit harus antara 1901 dan '.date('Y').'.',
            'title' => 'Judul sudah terdaftar.',
        ]);
        $this->assertDatabaseCount('books', 1);
    }

    public function test_user_can_update_book_and_keep_its_own_title(): void
    {
        $book = $this->makeBook();

        $response = $this->actingAs($this->user)
            ->put(route('books.update', $book), $this->validData(['stock' => 10]));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 10]);
    }

    public function test_user_can_delete_book(): void
    {
        $book = $this->makeBook();

        $this->actingAs($this->user)
            ->delete(route('books.destroy', $book))
            ->assertRedirect(route('books.index'));

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_book_with_borrowing_history_cannot_be_deleted(): void
    {
        $book = $this->makeBook();
        Borrowing::factory()->create()->books()->attach($book);

        $this->actingAs($this->user)
            ->delete(route('books.destroy', $book))
            ->assertSessionHas('error');

        $this->assertModelExists($book);
    }

    public function test_guest_cannot_access_books(): void
    {
        $book = $this->makeBook();

        $this->get(route('books.index'))->assertRedirect(route('login'));
        $this->post(route('books.store'), $this->validData(['title' => 'Baru']))
            ->assertRedirect(route('login'));
        $this->delete(route('books.destroy', $book))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('books', 1);
    }
}
