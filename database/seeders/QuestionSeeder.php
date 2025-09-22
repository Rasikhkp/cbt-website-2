<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $teachers = User::where('role', 'teacher')->get();

        $questions = [
            // === 10 MCQs ===
            [
                'type' => 'mcq',
                'question_text' => 'What is the capital of France?',
                'explanation' => 'Paris is the capital city of France.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['geography', 'capitals', 'europe'],
                'options' => [
                    ['text' => 'Berlin', 'correct' => false],
                    ['text' => 'Madrid', 'correct' => false],
                    ['text' => 'Paris', 'correct' => true],
                    ['text' => 'Rome', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which planet is known as the Red Planet?',
                'explanation' => 'Mars is called the Red Planet because of its reddish appearance due to iron oxide on its surface.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['astronomy', 'planets', 'solar-system'],
                'options' => [
                    ['text' => 'Earth', 'correct' => false],
                    ['text' => 'Mars', 'correct' => true],
                    ['text' => 'Jupiter', 'correct' => false],
                    ['text' => 'Venus', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'What is 2 + 2?',
                'explanation' => '2 + 2 = 4.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['mathematics', 'arithmetic', 'addition'],
                'options' => [
                    ['text' => '3', 'correct' => false],
                    ['text' => '4', 'correct' => true],
                    ['text' => '5', 'correct' => false],
                    ['text' => '22', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which gas do plants absorb from the atmosphere?',
                'explanation' => 'Plants absorb carbon dioxide during photosynthesis.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['biology', 'plants', 'photosynthesis'],
                'options' => [
                    ['text' => 'Oxygen', 'correct' => false],
                    ['text' => 'Carbon Dioxide', 'correct' => true],
                    ['text' => 'Nitrogen', 'correct' => false],
                    ['text' => 'Hydrogen', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Who wrote “Romeo and Juliet”?',
                'explanation' => 'The play was written by William Shakespeare.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['literature', 'authors', 'plays'],
                'options' => [
                    ['text' => 'William Wordsworth', 'correct' => false],
                    ['text' => 'William Shakespeare', 'correct' => true],
                    ['text' => 'Charles Dickens', 'correct' => false],
                    ['text' => 'Jane Austen', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'What is the largest mammal in the world?',
                'explanation' => 'The blue whale is the largest mammal on Earth.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['biology', 'animals', 'mammals'],
                'options' => [
                    ['text' => 'Elephant', 'correct' => false],
                    ['text' => 'Blue Whale', 'correct' => true],
                    ['text' => 'Giraffe', 'correct' => false],
                    ['text' => 'Orca', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which continent is the Sahara Desert located in?',
                'explanation' => 'The Sahara Desert is located in Africa.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['geography', 'deserts', 'africa'],
                'options' => [
                    ['text' => 'Asia', 'correct' => false],
                    ['text' => 'Africa', 'correct' => true],
                    ['text' => 'Australia', 'correct' => false],
                    ['text' => 'South America', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Who developed the theory of relativity?',
                'explanation' => 'The theory of relativity was developed by Albert Einstein.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['physics', 'scientists', 'theories'],
                'options' => [
                    ['text' => 'Isaac Newton', 'correct' => false],
                    ['text' => 'Albert Einstein', 'correct' => true],
                    ['text' => 'Nikola Tesla', 'correct' => false],
                    ['text' => 'Galileo Galilei', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'What is the chemical symbol for gold?',
                'explanation' => 'The symbol for gold in the periodic table is Au.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['chemistry', 'elements', 'periodic-table'],
                'options' => [
                    ['text' => 'Ag', 'correct' => false],
                    ['text' => 'Au', 'correct' => true],
                    ['text' => 'Gd', 'correct' => false],
                    ['text' => 'Go', 'correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which ocean is the largest?',
                'explanation' => 'The Pacific Ocean is the largest ocean on Earth.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['geography', 'oceans', 'earth'],
                'options' => [
                    ['text' => 'Atlantic Ocean', 'correct' => false],
                    ['text' => 'Pacific Ocean', 'correct' => true],
                    ['text' => 'Indian Ocean', 'correct' => false],
                    ['text' => 'Arctic Ocean', 'correct' => false],
                ]
            ],

            // === 5 Short Answer Questions ===
            [
                'type' => 'short',
                'question_text' => 'What is the currency of Japan?',
                'explanation' => 'The currency of Japan is the Japanese Yen.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['economics', 'currency', 'asia'],
            ],
            [
                'type' => 'short',
                'question_text' => 'Name the process by which plants make food.',
                'explanation' => 'The process is called photosynthesis.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['biology', 'plants', 'photosynthesis'],
            ],
            [
                'type' => 'short',
                'question_text' => 'What is the boiling point of water in Celsius?',
                'explanation' => 'The boiling point of water is 100°C at sea level.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['physics', 'chemistry', 'basic'],
            ],
            [
                'type' => 'short',
                'question_text' => 'Who painted the Mona Lisa?',
                'explanation' => 'The Mona Lisa was painted by Leonardo da Vinci.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['art', 'renaissance', 'painting'],
            ],
            [
                'type' => 'short',
                'question_text' => 'Which element has the chemical symbol O?',
                'explanation' => 'The symbol O stands for Oxygen.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['chemistry', 'elements', 'periodic-table'],
            ],

            // === 5 Long Answer Questions ===
            [
                'type' => 'long',
                'question_text' => 'Explain the causes of World War I.',
                'explanation' => 'World War I was triggered by the assassination of Archduke Franz Ferdinand, but deeper causes included militarism, alliances, imperialism, and nationalism.',
                'points' => 2.0,
                'difficulty' => 'hard',
                'tags' => ['history', 'world-war', '20th-century'],
            ],
            [
                'type' => 'long',
                'question_text' => 'Describe the process of photosynthesis in detail.',
                'explanation' => 'Photosynthesis is the process in which green plants use sunlight, carbon dioxide, and water to produce glucose and oxygen through chlorophyll activity.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['biology', 'plants', 'photosynthesis'],
            ],
            [
                'type' => 'long',
                'question_text' => 'Discuss the impact of technology on modern education.',
                'explanation' => 'Technology has transformed education through e-learning, digital classrooms, and access to global resources, while also raising issues of digital divide.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['education', 'technology', 'society'],
            ],
            [
                'type' => 'long',
                'question_text' => 'Explain the water cycle and its importance.',
                'explanation' => 'The water cycle involves processes like evaporation, condensation, precipitation, and infiltration, ensuring the continuous movement and renewal of water on Earth.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['geography', 'hydrology', 'environment'],
            ],
            [
                'type' => 'long',
                'question_text' => 'What are the advantages and disadvantages of globalization?',
                'explanation' => 'Globalization promotes trade, cultural exchange, and technological progress, but can also cause inequality, cultural homogenization, and environmental concerns.',
                'points' => 2.0,
                'difficulty' => 'hard',
                'tags' => ['economics', 'globalization', 'society'],
            ],
        ];

        foreach ($questions as $questionData) {
            $teacher = $teachers->random();

            $question = Question::create([
                'created_by' => $teacher->id,
                'type' => $questionData['type'],
                'question_text' => $questionData['question_text'],
                'explanation' => $questionData['explanation'],
                'points' => $questionData['points'],
                'difficulty' => $questionData['difficulty'],
                'tags' => $questionData['tags'],
            ]);

            if ($questionData['type'] === 'mcq' && isset($questionData['options'])) {
                foreach ($questionData['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['correct'],
                        'order' => $index,
                    ]);
                }
            }
        }
    }
}
