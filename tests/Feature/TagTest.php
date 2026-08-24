<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_タグ編集画面が表示される(): void
    {
        $user = User::factory()->create();

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->actingAs($user)
            ->get("/admin/tags/{$tag->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.edit');
        $response->assertSee('質問');
    }

    public function test_タグを作成できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/admin/tags', [
                'name' => '新しいタグ',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'name' => '新しいタグ',
        ]);
    }

    public function test_タグを更新できる(): void
    {
        $user = User::factory()->create();

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->actingAs($user)
            ->put("/admin/tags/{$tag->id}", [
                'name' => '回答',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '回答',
        ]);
    }

    public function test_タグを削除できる(): void
    {
        $user = User::factory()->create();

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->actingAs($user)
            ->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    public function test_未認証ユーザーはタグ編集画面にアクセスできない(): void
    {
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->get("/admin/tags/{$tag->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_未認証ユーザーはタグを作成できない(): void
    {
        $response = $this->post('/admin/tags', [
            'name' => '新しいタグ',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_未認証ユーザーはタグを更新できない(): void
    {
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->put("/admin/tags/{$tag->id}", [
            'name' => '回答',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_未認証ユーザーはタグを削除できない(): void
    {
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/login');
    }
}
