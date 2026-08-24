<?php

namespace Tests\Unit;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_全ての必須項目とタグを受け付ける(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $request = new ContactRequest;

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => '1',
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_不正な電話番号は拒否される(): void
    {
        $request = new ContactRequest;
        $data = [
            'tel' => 'abc123',
        ];
        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(
            'tel',
            $validator->errors()->toArray()
        );
    }

    public function test_不正な性別値は拒否される(): void
    {
        $request = new ContactRequest;
        $data = [
            'gender' => 4,
        ];
        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(
            'gender',
            $validator->errors()->toArray()
        );
    }
}
