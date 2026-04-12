<?php

namespace Database\Seeders;

use App\Models\ReviewRejectionTemplate;
use Illuminate\Database\Seeder;

class ReviewRejectionTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Spam / quảng cáo', 'body' => 'Nội dung không phản ánh trải nghiệm mua hàng.', 'sort_order' => 1],
            ['label' => 'Ngôn từ không phù hợp', 'body' => 'Đánh giá chứa từ ngữ không phù hợp với cộng đồng.', 'sort_order' => 2],
            ['label' => 'Không đúng sản phẩm', 'body' => 'Đánh giá không liên quan đến sản phẩm đã mua.', 'sort_order' => 3],
        ];

        foreach ($rows as $row) {
            ReviewRejectionTemplate::query()->firstOrCreate(
                ['label' => $row['label']],
                ['body' => $row['body'], 'sort_order' => $row['sort_order']]
            );
        }
    }
}
