<?php

namespace Database\Seeders;

use App\Models\Book\BookCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'جامعه‌شناسی',
            'مطالعات زنان',
            'تاریخ',
            'فلسفه',
            'روان‌شناسی',
            'ادبیات',
            'علوم سیاسی',
        ];

        foreach ($categories as $name) {
            BookCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
