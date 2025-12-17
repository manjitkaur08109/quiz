<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuizModel;
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'title' => 'Laravel Basics',
                'passing_score' => 2,
                'description' => 'Test your knowledge on Laravel basics',
                'category_id' => 1,
                'questions' => [
                    [
                        'question' => 'What is Laravel?',
                        'options' => ['Framework', 'Library', 'Language', 'Database'],
                        'correctAnswer' => 'Framework'
                    ],
                    [
                        'question' => 'Which template engine Laravel uses?',
                        'options' => ['Blade', 'Twig', 'Smarty', 'PHP'],
                        'correctAnswer' => 'Blade'
                    ]
                ],
                'price' => 100,
            ],
        ];

        foreach ($data as $d) {
            QuizModel::create($d);
        }
    }
}
