<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせフォームが表示される(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertViewHas('categories');
        $response->assertViewHas('tags');

        $response->assertSee('商品について');
        $response->assertSee('質問');
    }

    public function test_サンクスページが表示される(): void
    {
        $response = $this->get('/thanks');

        $response->assertStatus(200);
    }

    public function test_確認画面が表示される(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);

        $response->assertViewIs('contact.confirm');

        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('test@example.com');
        $response->assertSee('商品について');
    }

    public function test_確認画面でバリデーションエラーになる(): void
    {
        $response = $this->post('/contacts/confirm', []);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
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

    public function test_お問い合わせ内容が保存される(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'category_id' => $category->id,
        ]);

        $contact = Contact::where('email', 'test@example.com')->first();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_お問い合わせ保存時にバリデーションエラーになる(): void
    {
        $response = $this->post('/contacts', []);

        $response->assertRedirect();
    }
}
