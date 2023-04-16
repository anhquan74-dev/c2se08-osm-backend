<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Category name 1',
                'logo' => 'category-logo.png',
                'total_provider' => 0,
                'is_valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'Category name 2',
                'logo' => 'category-logo.png',
                'total_provider' => 0,
                'is_valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
