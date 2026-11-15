<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
        $this->get(route('categories.create'))->assertRedirect(route('login'));
    }

    public function test_petugas_can_view_list_but_cannot_manage(): void
    {
        $petugas = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Fiksi']);

        $this->actingAs($petugas)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Fiksi')
            ->assertDontSee(route('categories.create'));

        $this->actingAs($petugas)->get(route('categories.create'))->assertForbidden();
        $this->actingAs($petugas)->post(route('categories.store'), ['name' => 'Baru'])->assertForbidden();
        $this->actingAs($petugas)->get(route('categories.edit', $category))->assertForbidden();
        $this->actingAs($petugas)->put(route('categories.update', $category), ['name' => 'Diubah'])->assertForbidden();
        $this->actingAs($petugas)->delete(route('categories.destroy', $category))->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Fiksi']);
        $this->assertDatabaseMissing('categories', ['name' => 'Baru']);
    }

    public function test_admin_sees_list_with_book_counts_and_actions(): void
    {
        $category = Category::factory()->create(['name' => 'Fiksi']);
        Book::factory()->count(2)->create(['category_id' => $category->id]);

        $this->actingAs($this->admin)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Fiksi')
            ->assertSee(route('categories.create'))
            ->assertViewHas('categories', fn ($categories) => $categories->first()->books_count === 2);
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin)
            ->post(route('categories.store'), ['name' => 'Sains'])
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', ['name' => 'Sains']);
    }

    public function test_create_category_rejects_empty_and_duplicate_names(): void
    {
        Category::factory()->create(['name' => 'Fiksi']);

        $this->actingAs($this->admin)
            ->post(route('categories.store'), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'Nama wajib diisi.']);

        $this->actingAs($this->admin)
            ->post(route('categories.store'), ['name' => 'Fiksi'])
            ->assertSessionHasErrors(['name' => 'Nama sudah terdaftar.']);

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_admin_can_update_category_and_keep_its_own_name(): void
    {
        $category = Category::factory()->create(['name' => 'Fiksi']);

        $this->actingAs($this->admin)
            ->put(route('categories.update', $category), ['name' => 'Fiksi'])
            ->assertRedirect(route('categories.index'));

        $this->actingAs($this->admin)
            ->put(route('categories.update', $category), ['name' => 'Fiksi Populer'])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Fiksi Populer']);
    }

    public function test_admin_can_delete_unused_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($category);
    }

    public function test_category_used_by_books_cannot_be_deleted(): void
    {
        $book = Book::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $book->category))
            ->assertSessionHas('error');

        $this->assertModelExists($book->category);
    }
}
