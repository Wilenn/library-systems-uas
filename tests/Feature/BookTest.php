<?php

namespace Tests\Feature;

use App\Models\Book;
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

    public function test_book_index_lists_books(): void
    {
        $this->makeBook();

        $this->actingAs($this->user)
            ->get(route('books.index'))
            ->assertOk()
            ->assertSee('Laskar Pelangi')
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

    public function test_user_can_create_book(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('books.store'), $this->validData());

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('books', ['title' => 'Laskar Pelangi', 'stock' => 3]);
    }

    public function test_create_book_rejects_invalid_data(): void
    {
        $this->makeBook();

        $response = $this->actingAs($this->user)->post(route('books.store'), $this->validData([
            'title' => 'Laskar Pelangi',   // duplikat
            'year' => 1900,                // di luar batas MySQL YEAR
            'category_id' => 9999,         // tidak ada
        ]));

        $response->assertSessionHasErrors(['title', 'year', 'category_id']);
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

    public function test_guest_cannot_modify_books(): void
    {
        $book = $this->makeBook();

        $this->post(route('books.store'), $this->validData(['title' => 'Baru']))
            ->assertRedirect(route('login'));
        $this->delete(route('books.destroy', $book))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('books', 1);
    }
}
