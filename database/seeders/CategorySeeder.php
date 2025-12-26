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
            ],
            [
                'title' => 'Mathematics',
                'description' => 'Math quizzes',
            ],
            [
                'title' => 'Science',
                'description' => 'Science quizzes',
            ],
        ];
        foreach($data as $d){
            CategoryModel::updateOrCreate(
                ["title" => $d['title']],
                $d
        );
        }
    }
}
