<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookshelfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('book_shelves')->insert([
            [
                ['name' => 'Fiction Shelf', 'location' => '1st Floor'],
                ['name' => 'Science Shelf', 'location' => '2nd Floor'],
                ['name' => 'History Shelf', 'location' => '3rd Floor'],
                ['name' => 'Art Shelf', 'location' => '1st Floor'],
                ['name' => 'Technology Shelf', 'location' => '2nd Floor'],
                ['name' => 'Children\'s Shelf', 'location' => 'Ground Floor'],
                ['name' => 'Philosophy Shelf', 'location' => '3rd Floor'],
                ['name' => 'Travel Shelf', 'location' => 'Basement'],
                ['name' => 'Mystery Shelf', 'location' => '1st Floor'],
                ['name' => 'Biography Shelf', 'location' => '2nd Floor'],
            ]
        ]);
    }
}
