<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = ['title', 'chapter_number'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
