<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookShelf extends Model
{
    protected $fillable = ['name', 'location'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
