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
    protected $fillable = [
        'title',
        'author',
    ];
}
