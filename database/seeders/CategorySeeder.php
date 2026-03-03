<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Content\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Science', 'icon' => '🔬'],
            ['name' => 'Technology', 'icon' => '💻'],
            ['name' => 'Engineering', 'icon' => '⚙️'],
            ['name' => 'Mathematics', 'icon' => '📐'],
            ['name' => 'Medicine', 'icon' => '⚕️'],
            ['name' => 'Arts', 'icon' => '🎨'],
            ['name' => 'Business', 'icon' => '💼'],
            ['name' => 'Law', 'icon' => '⚖️'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
            ]);
        }
    }
}
