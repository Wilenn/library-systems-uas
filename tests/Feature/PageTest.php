<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_real_counts(): void
    {
        $category = Category::create(['name' => 'Fiksi']);
        Book::create(['title' => 'Buku A', 'author' => 'Penulis A', 'year' => 2020, 'stock' => 1, 'category_id' => $category->id]);
        Member::create(['name' => 'Wisnu']);
        Member::create(['name' => 'Yuda']);

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Jumlah Buku: 1', false)
            ->assertSee('Jumlah Member: 2', false)
            ->assertSee('Jumlah Kategori: 1', false);
    }

    public function test_categories_and_members_pages_list_data_from_database(): void
    {
        Category::create(['name' => 'Romance']);
        Member::create(['name' => 'Dimas']);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('categories.index'))->assertOk()->assertSee('Romance');
        $this->actingAs($user)->get(route('members.index'))->assertOk()->assertSee('Dimas');
    }
}
