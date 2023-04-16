<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        $appointments = [
            [
                'id' => 1,
                'package_id' => 1,
                'customer_id' => 3,
                'note_for_provider' => 'note_for_provider 1',
                'location' => 'location 1',
                'date' => date('Y-m-d H:i:s'),
                'price' => 0,
                'price_unit' => 'VND',
                'status' => 'active',
                'complete_date' => date('Y-m-d H:i:s'),
                'cancel_date' => date('Y-m-d H:i:s'),
                'offer_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'package_id' => 1,
                'customer_id' => 3,
                'note_for_provider' => 'note_for_provider 2',
                'location' => 'location 2',
                'date' => date('Y-m-d H:i:s'),
                'price' => 0,
                'price_unit' => 'VND',
                'status' => 'active',
                'complete_date' => date('Y-m-d H:i:s'),
                'cancel_date' => date('Y-m-d H:i:s'),
                'offer_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($appointments as $appointment) {
            Appointment::create($appointment);
        }
    }
}
