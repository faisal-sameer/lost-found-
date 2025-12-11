<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'حقائب سفر',      'code' => 'BAG'],
            ['name' => 'أوراق ثبوتية',   'code' => 'DOC'],
            ['name' => 'إلكترونيات',     'code' => 'ELEC'],
            ['name' => 'متعلقات شخصية',  'code' => 'PERS'],
        ];

        foreach ($types as $type) {
            ItemType::firstOrCreate(['code' => $type['code']], $type);
        }
    }
}
