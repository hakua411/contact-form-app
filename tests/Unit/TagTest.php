<?php

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_タグから複数のお問い合わせを取得できる(): void
    {
        $tag = Tag::factory()->create();
        $contacts =
        Contact::factory()->count(3)->create();

        foreach ($contacts as $contact) {
            $contact->tags()->attach($tag->id);
        }

        $this->assertCount(3, $tag->fresh()->contacts);
    }
}
