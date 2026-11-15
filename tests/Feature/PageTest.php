<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_and_members_pages_list_data_from_database(): void
    {
        Category::create(['name' => 'Romance']);
        Member::create(['name' => 'Dimas']);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('categories.index'))->assertOk()->assertSee('Romance');
        $this->actingAs($user)->get(route('members.index'))->assertOk()->assertSee('Dimas');
    }
}
