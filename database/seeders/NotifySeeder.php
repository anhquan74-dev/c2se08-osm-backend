<?php

namespace Database\Seeders;

use App\Models\Notify;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotifySeeder extends Seeder
{
    public function run()
    {
        $notifies = [
            [
                'id' => 1,
                'customer_id' => 3,
                'provider_id' => 2,
                'content' => 'content of notify 1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'customer_id' => 4,
                'provider_id' => 2,
                'content' => 'content of notify 2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($notifies as $notify) {
            Notify::create($notify);
        }
    }
}
