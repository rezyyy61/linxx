<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
