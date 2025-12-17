<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\CategoryModel;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $data=[
            [
                'title' => 'Programming',
                'description' => 'Programming related quizzes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mathematics',
                'description' => 'Math quizzes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Science',
                'description' => 'Science quizzes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach($data as $d){
            CategoryModel::create($d);
        }
    }
}
