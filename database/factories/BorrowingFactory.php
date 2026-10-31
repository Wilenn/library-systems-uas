<?php

namespace Database\Factories;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Default: peminjaman baru hari ini, belum dikembalikan.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'member_id' => Member::factory(),
            'borrowed_at' => today(),
            'due_date' => today()->addDays(Borrowing::LOAN_DAYS),
            'returned_at' => null,
            'status' => Borrowing::STATUS_BORROWED,
        ];
    }

    /** Belum dikembalikan dan sudah lewat jatuh tempo. */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'borrowed_at' => today()->subDays(10),
            'due_date' => today()->subDays(3),
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'borrowed_at' => today()->subDays(3),
            'due_date' => today()->addDays(4),
            'returned_at' => today(),
            'status' => Borrowing::STATUS_RETURNED,
        ]);
    }
}
