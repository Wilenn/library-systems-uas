<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Borrowing extends Model
{
    use HasFactory;

    public const STATUS_BORROWED = 'borrowed';

    public const STATUS_RETURNED = 'returned';

    /** Lama peminjaman dalam hari (jatuh tempo = tanggal pinjam + 7 hari). */
    public const LOAN_DAYS = 7;

    protected $fillable = [
        'user_id',
        'member_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'borrowed_at' => 'date',
            'due_date' => 'date',
            'returned_at' => 'date',
        ];
    }

    /** Petugas/admin yang mencatat transaksi. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_borrowing');
    }

    /** Transaksi yang bukunya belum dikembalikan. */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', self::STATUS_BORROWED);
    }

    public function isBorrowed(): bool
    {
        return $this->status === self::STATUS_BORROWED;
    }

    public function isOverdue(): bool
    {
        return $this->isBorrowed() && $this->due_date->lt(today());
    }
}
