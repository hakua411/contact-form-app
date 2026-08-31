<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET /api/v1/contacts
     * 一覧がJSONで返る
     */
    public function test_contacts_index_returns_json(): void
    {
        $category = Category::factory()->create();

        Contact::factory()->count(10)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    /**
     * GET /api/v1/contacts
     * キーワード検索が機能する
     */
    public function test_contacts_index_can_search_by_keyword(): void
    {
        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => '佐藤',
        ]);

        $response = $this->getJson('/api/v1/contacts?keyword=山田');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'first_name' => '太郎',
            'last_name' => '山田',
        ]);

        $response->assertJsonMissing([
            'first_name' => '花子',
            'last_name' => '佐藤',
        ]);
    }

    /**
     * GET /api/v1/contacts
     * ページネーションが機能する
     */
    public function test_contacts_index_is_paginated(): void
    {
        $category = Category::factory()->create();

        Contact::factory()->count(8)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 7)
            ->assertJsonPath('meta.total', 8);
    }

    /**
     * GET /api/v1/contacts/{id}
     * 詳細がJSONで返る
     */
    public function test_contact_show_returns_json(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ])
            ->assertJsonPath('data.id', $contact->id);
    }

    /**
     * GET /api/v1/contacts/{id}
     * 存在しないIDなら404
     */
    public function test_contact_show_returns_404_when_not_found(): void
    {
        $response = $this->getJson('/api/v1/contacts/99999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No query results for model [App\\Models\\Contact] 99999',
            ]);
    }

    /**
     * POST /api/v1/contacts
     * 正常に作成され201
     */
    public function test_contact_can_be_created(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->postJson('/api/v1/contacts', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data',
            ]);

        $this->assertDatabaseHas('contacts', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'yamada@example.com',
        ]);
    }

    /**
     * POST /api/v1/contacts
     * バリデーションエラーなら422
     */
    public function test_contact_creation_returns_422_when_validation_fails(): void
    {
        $response = $this->postJson('/api/v1/contacts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'category_id',
                'detail',
            ]);
    }

    /**
     * PUT /api/v1/contacts/{id}
     * 正常に更新され200
     */
    public function test_contact_can_be_updated(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '太郎',
        ]);

        $data = [
            'first_name' => '次郎',
            'last_name' => $contact->last_name,
            'gender' => $contact->gender,
            'email' => $contact->email,
            'tel' => $contact->tel,
            'address' => $contact->address,
            'building' => $contact->building,
            'category_id' => $category->id,
            'detail' => $contact->detail,
        ];

        $response = $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            $data
        );

        $response->assertStatus(200)
            ->assertJsonPath('data.first_name', '次郎');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => '次郎',
        ]);
    }

    /**
     * PUT /api/v1/contacts/{id}
     * 存在しないIDなら404
     */
    public function test_contact_update_returns_404_when_not_found(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です',
        ];

        $response = $this->putJson('/api/v1/contacts/99999', $data);

        $response->assertStatus(404);
    }

    /**
     * PUT /api/v1/contacts/{id}
     * バリデーションエラーなら422
     */
    public function test_contact_update_returns_422_when_validation_fails(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'category_id',
                'detail',
            ]);
    }

    /**
     * DELETE /api/v1/contacts/{id}
     * 正常に削除され204
     */
    public function test_contact_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson(
            "/api/v1/contacts/{$contact->id}"
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /**
     * DELETE /api/v1/contacts/{id}
     * 存在しないIDなら404
     */
    public function test_contact_delete_returns_404_when_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/contacts/99999');

        $response->assertStatus(404);
    }
}
