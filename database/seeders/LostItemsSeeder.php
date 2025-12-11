<?php

namespace Database\Seeders;

use App\Models\LostItem;
use App\Models\PickupPoint;
use Illuminate\Database\Seeder;

class LostItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // لازم تكون نقاط الاستلام موجودة أول
        $pickupPointIds = PickupPoint::pluck('id')->all();

        if (empty($pickupPointIds)) {
            // لو ما فيه نقاط، نطلع بدون ما نسوي شيء
            return;
        }

        $items = [
            [
                'barcode'          => 'HJJ-2025-0001',
                'title'            => 'حقيبة سفر سوداء متوسطة',
                'description'      => 'حقيبة متوسطة الحجم، عليها ملصق شركة الطيران وخيط أحمر في المقبض.',
                'owner_id_number'  => '1020304050',
                'owner_phone'      => '0550000001',
                'status'           => 'received',
            ],
            [
                'barcode'          => 'HJJ-2025-0002',
                'title'            => 'محفظة بنية جلد',
                'description'      => 'محفظة رجالية بنية تحتوي على عدة بطاقات بنكية وبطاقة هوية تركية.',
                'owner_id_number'  => null,
                'owner_phone'      => '0550000002',
                'status'           => 'received',
            ],
            [
                'barcode'          => 'HJJ-2025-0003',
                'title'            => 'هاتف آيفون 13 أبيض',
                'description'      => 'هاتف آيفون 13 أبيض مع غطاء شفاف، الخلفية صورة للكعبة.',
                'owner_id_number'  => '2011223344',
                'owner_phone'      => null,
                'status'           => 'received',
            ],
            [
                'barcode'          => 'HJJ-2025-0004',
                'title'            => 'حقيبة يد صغيرة',
                'description'      => 'حقيبة يد نسائية صغيرة لونها كحلي، تحتوي على بعض الأدوية وأدوات شخصية.',
                'owner_id_number'  => null,
                'owner_phone'      => '0550000003',
                'status'           => 'delivered',
            ],
            [
                'barcode'          => 'HJJ-2025-0005',
                'title'            => 'جواز سفر إندونيسي',
                'description'      => 'جواز سفر إندونيسي داخل ملف بلاستيكي شفاف.',
                'owner_id_number'  => null,
                'owner_phone'      => '0550000004',
                'status'           => 'received',
            ],
        ];

        foreach ($items as $item) {
            LostItem::create([
                'pickup_point_id'  => $pickupPointIds[array_rand($pickupPointIds)], // اختيار نقطة استلام عشوائية
                'barcode'          => $item['barcode'],
                'title'            => $item['title'],
                'description'      => $item['description'],
                'owner_id_number'  => $item['owner_id_number'],
                'owner_phone'      => $item['owner_phone'],
                'status'           => $item['status'],
            ]);
        }
    }
}
