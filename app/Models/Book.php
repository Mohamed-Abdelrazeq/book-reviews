<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected $fillable = [
        'title',
        'author',
    ];

    protected static function booted()
    {
        static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query
            ->where('title', 'like', '%' . $title . '%');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query
            ->withCount([
                'reviews' => fn(Builder $query) => $this->dateRangeFilter($query, $from, $to)
            ])
            ->orderByDesc('reviews_count');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder
    {
        return $query
            ->withAvg([
                'reviews' => fn(Builder $query) => $this->dateRangeFilter($query, $from, $to)
            ], 'rating')
            ->orderByDesc('reviews_avg_rating');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        return $query
            ->having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $query->where('created_at', '>=', $from);
        } elseif ($to) {
            $query->where('created_at', '<=', $to);
        }
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query
            ->popular(now()->subMonth(), now())
            ->HighestRated(now()->subMonth(), now())
            ->minReviews(2)
        ;
    }

    public function scopePopularLast6Months(Builder $query): Builder
    {
        return $query
            ->popular(now()->subMonths(6), now())
            ->HighestRated(now()->subMonths(6), now())
            ->minReviews(5)
        ;
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query
            ->HighestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2)
        ;
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder
    {
        return $query
            ->HighestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5)
        ;
    }
}
