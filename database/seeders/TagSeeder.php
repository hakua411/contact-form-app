<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::updateOrCreate([
            'name' => '質問',
        ]);

        Tag::updateOrCreate([
            'name' => '要望',
        ]);

        Tag::updateOrCreate([
            'name' => '不具合報告',
        ]);

        Tag::updateOrCreate([
            'name' => 'ご意見',
        ]);

        Tag::updateOrCreate([
            'name' => 'その他',
        ]);
    }
}
