<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            [
                ['book_shelf_id' => 1, 'title' => 'The Time Machine', 'author' => 'H.G. Wells', 'published_year' => 2000],
                ['book_shelf_id' => 2, 'title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'published_year' => 2015],
                ['book_shelf_id' => 1, 'title' => '1984', 'author' => 'George Orwell', 'published_year' => 1949],
                ['book_shelf_id' => 2, 'title' => 'The Selfish Gene', 'author' => 'Richard Dawkins', 'published_year' => 1976],
                ['book_shelf_id' => 3, 'title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'published_year' => 2011],
                ['book_shelf_id' => 3, 'title' => 'Guns, Germs, and Steel', 'author' => 'Jared Diamond', 'published_year' => 1997],
                ['book_shelf_id' => 4, 'title' => 'The Story of Art', 'author' => 'E.H. Gombrich', 'published_year' => 1950],
                ['book_shelf_id' => 5, 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'published_year' => 2008],
                ['book_shelf_id' => 6, 'title' => 'Charlotte\'s Web', 'author' => 'E.B. White', 'published_year' => 1952],
                ['book_shelf_id' => 7, 'title' => 'The Republic', 'author' => 'Plato', 'published_year' => -380],
                ['book_shelf_id' => 8, 'title' => 'Into the Wild', 'author' => 'Jon Krakauer', 'published_year' => 1996],
                ['book_shelf_id' => 9, 'title' => 'The Da Vinci Code', 'author' => 'Dan Brown', 'published_year' => 2003],
                ['book_shelf_id' => 10, 'title' => 'Long Walk to Freedom', 'author' => 'Nelson Mandela', 'published_year' => 1994],
            ]
        ]);
    }
}
