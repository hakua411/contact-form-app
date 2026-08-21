<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::create([
                'category_id' => Category::inRandomOrder()->first()->id,
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->safeEmail(),
                'tel' => $faker->numerify('###########'),
                'address' => $faker->address(),
                'building' => $faker->secondaryAddress(),
                'detail' => $faker->realText(120),
            ]);

            $tags = Tag::inRandomOrder()->limit($faker->numberBetween(1, 3))->get();

            $contact->tags()->attach($tags);
        }
    }
}
