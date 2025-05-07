<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pages')->insert([
            ['chapter_id' => 1, 'page_number' => 1, 'content' => 'Content of Chapter 1, Page 1'],
            ['chapter_id' => 1, 'page_number' => 2, 'content' => 'Content of Chapter 1, Page 2'],
            ['chapter_id' => 2, 'page_number' => 1, 'content' => 'Content of Chapter 2, Page 1'],
            ['chapter_id' => 3, 'page_number' => 1, 'content' => 'Time is a mystery...'],
            ['chapter_id' => 4, 'page_number' => 1, 'content' => 'Black holes are regions of spacetime...'],
            ['chapter_id' => 4, 'page_number' => 2, 'content' => 'Their gravity is so strong that nothing can escape.'],
            ['chapter_id' => 5, 'page_number' => 1, 'content' => 'Big Brother is watching every move...'],
            ['chapter_id' => 6, 'page_number' => 1, 'content' => 'Thoughtcrime is the ultimate betrayal...'],
            ['chapter_id' => 7, 'page_number' => 1, 'content' => 'Genes determine traits...'],
            ['chapter_id' => 8, 'page_number' => 1, 'content' => 'Homo sapiens began to dominate...'],
            ['chapter_id' => 9, 'page_number' => 1, 'content' => 'Agriculture transformed human society.'],
            ['chapter_id' => 10, 'page_number' => 1, 'content' => 'Cave paintings reveal early creativity...'],
            ['chapter_id' => 11, 'page_number' => 1, 'content' => 'Choose meaningful names for variables...'],
            ['chapter_id' => 12, 'page_number' => 1, 'content' => 'Clean functions improve code quality.'],
            ['chapter_id' => 13, 'page_number' => 1, 'content' => 'Wilbur is the best pig...'],
            ['chapter_id' => 14, 'page_number' => 1, 'content' => 'Plato describes the ideal state...'],
            ['chapter_id' => 15, 'page_number' => 1, 'content' => 'Surviving without electricity...'],
            ['chapter_id' => 16, 'page_number' => 1, 'content' => 'Secrets hidden in Renaissance art...'],
            ['chapter_id' => 17, 'page_number' => 1, 'content' => 'Mandela reflects on years of captivity.'],
        ]);
    }
}
