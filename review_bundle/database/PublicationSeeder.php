<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fa_IR');

        $topics = [
            'تحلیل وضعیت سیاسی ایران',
            'جنبش‌های کارگری و حقوق کارگران',
            'نقد امپریالیسم جهانی',
            'وضعیت زندانیان سیاسی',
            'جایگاه زنان در سیاست',
            'تبعیض قومی و نژادی',
            'مهاجرت و سرکوب اقلیت‌ها',
            'آزادی بیان و رسانه‌های مستقل',
            'نقش سرمایه‌داری در بحران اقتصادی',
            'اعتراضات مردمی و نافرمانی مدنی'
        ];

        foreach (range(1, 50) as $i) {
            $descPoints = $faker->randomElements($topics, rand(3, 5));

            DB::table('publications')->insert([
                'political_profile_id' => 17,
                'title' => 'کمونیست هفتگی',
                'issue' => (700 + $i),
                'description' => "در این شماره:\n• " . implode("\n• ", $descPoints) . "\n\n" .
                    "همچنین تحلیل: " . $faker->sentence(6),
                'published_at' => $faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
                'file_path' => 'public/publications/' . now()->timestamp . '_' . Str::random(10) . '.pdf',
                'file_type' => 'application/pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
