<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $messages = [
            [
                'id' => 1,
                'customer_id' => 3,
                'provider_id' => 2,
                'content' => 'hi 1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'customer_id' => 4,
                'provider_id' => 2,
                'content' => 'hi 2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($messages as $message) {
            Message::create($message);
        }
    }
}