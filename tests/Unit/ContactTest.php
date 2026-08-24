<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_キーワードで検索できる(): void
    {
        $contact =
        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
        ]);
        Contact::factory()->create([
            'first_name' => '佐藤',
            'last_name' => '花子',
        ]);

        $result = Contact::query()->keywordSearch('山田', null, null, null)->get();

        $this->assertTrue($result->contains($contact));
        $this->assertCount(1, $result);
    }

    public function test_性別で検索できる(): void
    {
        $male =
        Contact::factory()->create([
            'gender' => 1,
        ]);
        Contact::factory()->create([
            'gender' => 2,
        ]);

        $result = Contact::query()->genderSearch(1)->get();

        $this->assertTrue($result->contains($male));
        $this->assertCount(1, $result);
    }

    public function test_カテゴリで検索できる(): void
    {
        $category =
        Category::factory()->create();
        $contact =
        Contact::factory()->create([
            'category_id' => $category->id,
        ]);
        Contact::factory()->create();

        $result = Contact::query()->categorySearch($category->id)->get();

        $this->assertTrue($result->contains($contact));
        $this->assertCount(1, $result);
    }

    public function test_日付で検索できる(): void
    {
        $contact =
        Contact::factory()->create([
            'created_at' => '2026-08-24 10:00:00',
        ]);
        Contact::factory()->create([
            'created_at' => '2026-08-23 10:00:00',
        ]);

        $result = Contact::query()->dateSearch('2026-08-24')->get();

        $this->assertTrue($result->contains($contact));
        $this->assertCount(1, $result);
    }

    public function test_お問い合わせはカテゴリに属する(): void
    {
        $category =
        Category::factory()->create();
        $contact =
        Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertTrue($contact->category->is($category));
    }

    public function test_お問い合わせに複数のタグを同期できる(): void
    {
        $contact =
        Contact::factory()->create();
        $tags =
        Tag::factory()->count(3)->create();

        $contact->tags()->sync($tags->pluck('id'));

        $this->assertCount(3, $contact->fresh()->tags);
    }
}
