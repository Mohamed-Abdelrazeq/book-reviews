<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        Book::factory(33)
            ->create()->each(function (Book $book) {
                $reviewsNumber = random_int(5, 30);
                Review::factory()
                    ->count($reviewsNumber)
                    ->good()
                    ->for($book)
                    ->create();
            });

        Book::factory(33)
            ->create()->each(function (Book $book) {
                $reviewsNumber = random_int(5, 30);
                Review::factory()
                    ->count($reviewsNumber)
                    ->bad()
                    ->for($book)
                    ->create();
            });

        Book::factory(34)
            ->create()->each(function (Book $book) {
                $reviewsNumber = random_int(5, 30);
                Review::factory()
                    ->count($reviewsNumber)
                    ->average()
                    ->for($book)
                    ->create();
            });
    }
}
