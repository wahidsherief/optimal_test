<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chapters')->insert([
            ['book_id' => 1, 'title' => 'Chapter One', 'chapter_number' => 1],
            ['book_id' => 1, 'title' => 'Chapter Two', 'chapter_number' => 2],
            ['book_id' => 2, 'title' => 'The Beginning of Time', 'chapter_number' => 1],
            ['book_id' => 2, 'title' => 'Black Holes and Beyond', 'chapter_number' => 2],
            ['book_id' => 3, 'title' => 'Big Brother is Watching', 'chapter_number' => 1],
            ['book_id' => 3, 'title' => 'Doublethink and Thoughtcrime', 'chapter_number' => 2],
            ['book_id' => 4, 'title' => 'Genes and Evolution', 'chapter_number' => 1],
            ['book_id' => 5, 'title' => 'Cognitive Revolution', 'chapter_number' => 1],
            ['book_id' => 6, 'title' => 'Farming and Civilization', 'chapter_number' => 1],
            ['book_id' => 7, 'title' => 'Early Art Forms', 'chapter_number' => 1],
            ['book_id' => 8, 'title' => 'Meaningful Names', 'chapter_number' => 1],
            ['book_id' => 8, 'title' => 'Functions and Clean Code', 'chapter_number' => 2],
            ['book_id' => 9, 'title' => 'Wilbur', 'chapter_number' => 1],
            ['book_id' => 10, 'title' => 'Justice and the Ideal State', 'chapter_number' => 1],
            ['book_id' => 11, 'title' => 'Off the Grid', 'chapter_number' => 1],
            ['book_id' => 12, 'title' => 'Secrets of the Priory', 'chapter_number' => 1],
            ['book_id' => 13, 'title' => 'Robben Island', 'chapter_number' => 1],
        ]);
    }
}
