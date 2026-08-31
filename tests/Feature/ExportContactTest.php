<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済み管理者がCSVをダウンロードできる
     */
    public function test_authenticated_admin_can_download_csv(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/contacts/export');

        $response->assertStatus(200);

        $response->assertHeader(
            'Content-Type',
            'text/csv; charset=UTF-8'
        );

        $response->assertHeader(
            'Content-Disposition',
            'attachment; filename="contacts.csv"'
        );

        $response->assertSee('ID');
        $response->assertSee('氏名');
        $response->assertSee('メール');
    }

    /**
     * フィルタ条件がCSVに反映される
     */
    public function test_csv_export_applies_filter(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $target = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
        ]);

        $response = $this->actingAs($user)
            ->get('/contacts/export?gender=1');

        $response->assertStatus(200);

        $response->assertSee('山田');
        $response->assertSee('太郎');

        $response->assertDontSee('佐藤');
        $response->assertDontSee('花子');
    }

    /**
     * フィルタ指定なしの場合、新着順でCSVが出力される
     */
    public function test_csv_export_is_sorted_by_newest_when_no_filter_is_specified(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $oldContact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '古い',
            'last_name' => '問い合わせ',
            'created_at' => now()->subDays(2),
        ]);

        $newContact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '新しい',
            'last_name' => '問い合わせ',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/contacts/export');

        $response->assertStatus(200);

        $csv = $response->getContent();

        $newPosition = strpos($csv, '新しい');
        $oldPosition = strpos($csv, '古い');

        $this->assertNotFalse($newPosition);
        $this->assertNotFalse($oldPosition);

        $this->assertLessThan(
            $oldPosition,
            $newPosition
        );
    }
}
