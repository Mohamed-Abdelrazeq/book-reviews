<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }


    protected static function booted()
    {
        static::updated(function ($review) {
            cache()->forget('book:' . $review->book_id);
        });
        static::deleted(function ($review) {
            cache()->forget('book:' . $review->book_id);
        });
    }
    protected $fillable = [
        'review',
        'rating',
    ];
}
