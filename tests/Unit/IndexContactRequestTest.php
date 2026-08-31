<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいフィルタ条件を受け付ける()
    {
        $category = Category::factory()->create();

        $request = new IndexContactRequest;

        $data = [
            'keyword' => '山田',
            'gender' => 1,
            'category_id' => $category->id,
            'date' => '2026-08-31',
            'per_page' => 10,
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
        $request = new IndexContactRequest;

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
        $request = new IndexContactRequest;

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
    public function 不正な日付を拒否する()
    {
        $request = new IndexContactRequest;

        $data = [
            'date' => 'invalid-date',
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 不正なper_pageを拒否する()
    {
        $request = new IndexContactRequest;

        $data = [
            'per_page' => 0,
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
