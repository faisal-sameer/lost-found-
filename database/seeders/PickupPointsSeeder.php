<?php

namespace Database\Seeders;

use App\Models\PickupPoint;
use Illuminate\Database\Seeder;

class PickupPointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $points = [
            [
                'name'    => 'مركز المفقودات - المنطقة المركزية (مكة)',
                'city'    => 'مكة المكرمة',
                'address' => 'المنطقة المركزية بجوار الحرم المكي',
                'map_url' => 'https://maps.google.com/?q=21.4225,39.8262', // تقريبًا موقع الحرم
            ],
            [
                'name'    => 'مركز المفقودات - مسجد نمرة',
                'city'    => 'مكة المكرمة',
                'address' => 'مشعر عرفات - مسجد نمرة',
                'map_url' => 'https://maps.google.com/?q=21.4095,39.8940',
            ],
            [
                'name'    => 'مركز المفقودات - منى (الجسر الجنوبي)',
                'city'    => 'مكة المكرمة',
                'address' => 'مشعر منى - قرب جسر الجمرات الجنوبي',
                'map_url' => 'https://maps.google.com/?q=21.4143,39.8923',
            ],
            [
                'name'    => 'مركز المفقودات - المسجد النبوي',
                'city'    => 'المدينة المنورة',
                'address' => 'الساحة الشمالية للمسجد النبوي',
                'map_url' => 'https://maps.google.com/?q=24.4684,39.6104',
            ],
            [
                'name'    => 'مكتب المفقودات - محطة قطار الحرمين (مكة)',
                'city'    => 'مكة المكرمة',
                'address' => 'محطة قطار الحرمين - بوابة المسافرين',
                'map_url' => 'https://maps.google.com/?q=21.3589,39.8941',
            ],
            [
                'name'    => 'مكتب المفقودات - محطة قطار الحرمين (المدينة)',
                'city'    => 'المدينة المنورة',
                'address' => 'محطة قطار الحرمين - صالة الوصول',
                'map_url' => 'https://maps.google.com/?q=24.4473,39.7046',
            ],
        ];

        foreach ($points as $point) {
            PickupPoint::firstOrCreate(
                ['name' => $point['name']], // عشان ما تتكرر لو شغلت seeder أكثر من مرة
                $point
            );
        }
    }
}
