<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全ての必須項目とタグ入力を受け付ける()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $request = new StoreContactRequest;

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都千代田区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 不正な性別を拒否する()
    {
        $request = new StoreContactRequest;

        $data = [
            'gender' => 4,
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 存在しないカテゴリ_i_dを拒否する()
    {
        $request = new StoreContactRequest;

        $data = [
            'category_id' => 9999,
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 存在しないタグ_i_dを拒否する()
    {
        $request = new StoreContactRequest;

        $data = [
            'tag_ids' => [9999],
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
