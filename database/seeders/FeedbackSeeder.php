<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run()
    {
        $feedbacks = [
            [
                'id' => 1,
                'appointment_id' => 1,
                'comment' => 'comment 1',
                'reply' => 'reply 1',
                'star' => 0,
                'reply_at' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'appointment_id' => 1,
                'comment' => 'comment 2',
                'reply' => 'reply 2',
                'star' => 0,
                'reply_at' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($feedbacks as $feedback) {
            Feedback::create($feedback);
        }
    }
}
