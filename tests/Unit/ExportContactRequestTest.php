<?php

namespace Tests\Unit;

use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExportContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい性別を受け付ける()
    {
        $request = new ExportContactRequest;

        $data = [
            'gender' => 1,
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
        $request = new ExportContactRequest;

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
    public function 存在するカテゴリ_i_dを受け付ける()
    {
        $category = Category::factory()->create();

        $request = new ExportContactRequest;

        $data = [
            'category_id' => $category->id,
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 存在しないカテゴリ_i_dを拒否する()
    {
        $request = new ExportContactRequest;

        $data = [
            'category_id' => 9999,
        ];

        $validator = Validator::make(
            $data,
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
