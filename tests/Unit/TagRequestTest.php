<?php

namespace Tests\Unit;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TagRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_タグ名は必須(): void
    {
        $request = new TagRequest;
        $validator = Validator::make(
            ['name' => ''],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(
            'name',
            $validator->errors()->toArray()
        );
    }

    public function test_タグ名の文字数制限を超えると拒否される(): void
    {
        $request = new TagRequest;
        $validator = Validator::make(
            ['name' => str_repeat('あ', 51)],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }

    public function test_既に存在するタグ名は登録できない(): void
    {
        Tag::factory()->create([
            'name' => '質問',
        ]);

        $request = new TagRequest;
        $validator = Validator::make(
            ['name' => '質問'],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
