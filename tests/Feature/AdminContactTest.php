<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証済みユーザーは管理画面を表示できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function test_未認証ユーザーはログイン画面にリダイレクトされる(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_キーワード検索ができる(): void
    {
        $user = User::factory()->create();

        Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => Category::create([
                'content' => '商品について',
            ])->id,
            'detail' => 'お問い合わせです。',
        ]);

        Contact::create([
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '09012345679',
            'address' => '大阪府',
            'category_id' => Category::create([
                'content' => 'その他',
            ])->id,
            'detail' => '別のお問い合わせです。',
        ]);

        $response = $this->actingAs($user)
            ->get('/admin?keyword=山田');

        $response->assertStatus(200);

        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    public function test_7件ごとにページネーションされる(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品について',
        ]);

        for ($i = 1; $i <= 15; $i++) {
            Contact::create([
                'first_name' => '山田',
                'last_name' => "太郎{$i}",
                'gender' => 1,
                'email' => "test{$i}@example.com",
                'tel' => '09012345678',
                'address' => '東京都',
                'category_id' => $category->id,
                'detail' => 'お問い合わせです。',
            ]);
        }

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);

        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->perPage() === 7;
        });
    }

    public function test_お問い合わせ詳細が表示される(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品について',
        ]);

        $contact = Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'お問い合わせです。',
        ]);

        $response = $this->actingAs($user)
            ->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);

        $response->assertViewIs('admin.show');

        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('商品について');
    }

    public function test_お問い合わせを削除できる(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品について',
        ]);

        $contact = Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'お問い合わせです。',
        ]);

        $response = $this->actingAs($user)
            ->delete("/admin/contacts/{$contact->id}");

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
