<?php

namespace Tests\Unit;

use App\Models\Borrowing;
use Tests\TestCase;

/**
 * Aturan status peminjaman diuji pada model di memori (tanpa database).
 */
class BorrowingStateTest extends TestCase
{
    private function borrowing(string $status, $dueDate): Borrowing
    {
        return new Borrowing(['status' => $status, 'due_date' => $dueDate]);
    }

    public function test_loan_period_is_seven_days(): void
    {
        $this->assertSame(7, Borrowing::LOAN_DAYS);
    }

    public function test_borrowing_due_today_or_later_is_not_overdue(): void
    {
        $this->assertFalse($this->borrowing(Borrowing::STATUS_BORROWED, today())->isOverdue());
        $this->assertFalse($this->borrowing(Borrowing::STATUS_BORROWED, today()->addDays(3))->isOverdue());
    }

    public function test_borrowing_past_due_date_is_overdue_and_counts_days(): void
    {
        $late = $this->borrowing(Borrowing::STATUS_BORROWED, today()->subDays(3));

        $this->assertTrue($late->isOverdue());
        $this->assertSame(3, $late->daysOverdue());
        $this->assertSame(1, $this->borrowing(Borrowing::STATUS_BORROWED, today()->subDay())->daysOverdue());
    }

    public function test_returned_borrowing_is_never_overdue(): void
    {
        $returned = $this->borrowing(Borrowing::STATUS_RETURNED, today()->subDays(10));

        $this->assertFalse($returned->isBorrowed());
        $this->assertFalse($returned->isOverdue());
        $this->assertSame(0, $returned->daysOverdue());
    }

    public function test_days_overdue_is_zero_when_not_overdue(): void
    {
        $this->assertSame(0, $this->borrowing(Borrowing::STATUS_BORROWED, today()->addDays(2))->daysOverdue());
    }
}
