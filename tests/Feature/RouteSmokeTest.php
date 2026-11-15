<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Jaring pengaman: membaca SEMUA rute GET bernama secara dinamis, sehingga rute baru
 * otomatis ikut diuji (tidak ada halaman rusak, dan hak akses tidak terlewat).
 */
class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<RoutingRoute> */
    private function pageRoutes(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn (RoutingRoute $route) => in_array('GET', $route->methods(), true))
            ->filter(fn (RoutingRoute $route) => $route->getName() !== null)
            // Halaman login khusus tamu, dan rute storage.* berasal dari framework.
            ->reject(fn (RoutingRoute $route) => $route->getName() === 'login' || str_starts_with($route->getName(), 'storage.'))
            ->values()
            ->all();
    }

    private function urlFor(RoutingRoute $route): string
    {
        $records = [
            'book' => Book::firstOrFail(),
            'category' => Category::firstOrFail(),
            'member' => Member::firstOrFail(),
            'user' => User::where('email', 'petugas@example.com')->firstOrFail(),
            'borrowing' => Borrowing::firstOrFail(),
        ];

        $arguments = [];

        foreach ($route->parameterNames() as $name) {
            $this->assertArrayHasKey(
                $name,
                $records,
                "Rute '{$route->getName()}' memakai parameter '{$name}' yang belum dipetakan di RouteSmokeTest."
            );

            $arguments[$name] = $records[$name];
        }

        return route($route->getName(), $arguments);
    }

    private function isAdminOnly(RoutingRoute $route): bool
    {
        return in_array('role:admin', $route->gatherMiddleware(), true);
    }

    public function test_enough_pages_are_covered_and_some_are_admin_only(): void
    {
        $routes = $this->pageRoutes();

        $this->assertGreaterThanOrEqual(15, count($routes));
        $this->assertNotEmpty(array_filter($routes, fn ($route) => $this->isAdminOnly($route)));
    }

    public function test_every_page_renders_for_admin_with_full_demo_data(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());

        foreach ($this->pageRoutes() as $route) {
            $url = $this->urlFor($route);

            $this->assertSame(
                200,
                $this->get($url)->getStatusCode(),
                "Admin: GET {$url} ({$route->getName()}) seharusnya berstatus 200."
            );
        }
    }

    public function test_petugas_is_blocked_only_on_admin_routes(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'petugas@example.com')->firstOrFail());

        foreach ($this->pageRoutes() as $route) {
            $url = $this->urlFor($route);
            $expected = $this->isAdminOnly($route) ? 403 : 200;

            $this->assertSame(
                $expected,
                $this->get($url)->getStatusCode(),
                "Petugas: GET {$url} ({$route->getName()}) seharusnya berstatus {$expected}."
            );
        }
    }

    public function test_guests_are_redirected_to_login_from_every_page(): void
    {
        $this->seed();

        foreach ($this->pageRoutes() as $route) {
            $url = $this->urlFor($route);

            $this->get($url)->assertRedirect(route('login'));
        }
    }
}
