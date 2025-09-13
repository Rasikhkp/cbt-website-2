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
        // Get teacher users to assign as creators
        $teachers = User::where('role', 'teacher')->get();

        $questions = [
            // Mathematics MCQ Questions
            [
                'type' => 'mcq',
                'question_text' => 'What is the result of 15 + 27?',
                'explanation' => '15 + 27 = 42. This is a basic addition problem.',
                'points' => 1.0,
                'difficulty' => 'easy',
                'tags' => ['mathematics', 'arithmetic', 'addition'],
                'options' => [
                    ['text' => '42', 'correct' => true],
                    ['text' => '32', 'correct' => false],
                    ['text' => '52', 'correct' => false],
                    ['text' => '41', 'correct' => false]
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Solve for x: 2x + 8 = 16',
                'explanation' => '2x + 8 = 16, so 2x = 8, therefore x = 4.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['mathematics', 'algebra', 'equations'],
                'options' => [
                    ['text' => '4', 'correct' => true],
                    ['text' => '8', 'correct' => false],
                    ['text' => '2', 'correct' => false],
                    ['text' => '6', 'correct' => false]
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'What is the derivative of f(x) = x² + 3x - 5?',
                'explanation' => 'Using the power rule: d/dx(x²) = 2x, d/dx(3x) = 3, d/dx(-5) = 0. Therefore f\'(x) = 2x + 3.',
                'points' => 3.0,
                'difficulty' => 'hard',
                'tags' => ['mathematics', 'calculus', 'derivatives'],
                'options' => [
                    ['text' => '2x + 3', 'correct' => true],
                    ['text' => 'x² + 3', 'correct' => false],
                    ['text' => '2x + 3x', 'correct' => false],
                    ['text' => '2x - 5', 'correct' => false]
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which of the following is a prime number?',
                'explanation' => '17 is a prime number because it is only divisible by 1 and itself.',
                'points' => 1.5,
                'difficulty' => 'easy',
                'tags' => ['mathematics', 'number-theory', 'prime-numbers'],
                'options' => [
                    ['text' => '17', 'correct' => true],
                    ['text' => '15', 'correct' => false],
                    ['text' => '21', 'correct' => false],
                    ['text' => '25', 'correct' => false]
                ]
            ],

            // Science MCQ Questions
            [
                'type' => 'mcq',
                'question_text' => 'What is the chemical formula for water?',
                'explanation' => 'Water consists of 2 hydrogen atoms and 1 oxygen atom, hence H₂O.',
                'points' => 1.0,
                'difficulty' => 'easy',
                'tags' => ['chemistry', 'compounds', 'basic'],
                'options' => [
                    ['text' => 'H₂O', 'correct' => true],
                    ['text' => 'CO₂', 'correct' => false],
                    ['text' => 'NaCl', 'correct' => false],
                    ['text' => 'O₂', 'correct' => false]
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which planet is closest to the Sun?',
                'explanation' => 'Mercury is the innermost planet in our solar system.',
                'points' => 1.0,
                'difficulty' => 'easy',
                'tags' => ['astronomy', 'planets', 'solar-system'],
                'options' => [
                    ['text' => 'Mercury', 'correct' => true],
                    ['text' => 'Venus', 'correct' => false],
                    ['text' => 'Mars', 'correct' => false],
                    ['text' => 'Earth', 'correct' => false]
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'What is the speed of light in a vacuum?',
                'explanation' => 'The speed of light in vacuum is approximately 299,792,458 meters per second, commonly rounded to 3×10⁸ m/s.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['physics', 'light', 'constants'],
                'options' => [
                    ['text' => '3×10⁸ m/s', 'correct' => true],
                    ['text' => '3×10⁶ m/s', 'correct' => false],
                    ['text' => '3×10¹⁰ m/s', 'correct' => false],
                    ['text' => '3×10⁷ m/s', 'correct' => false]
                ]
            ],

            // Multiple Correct MCQ Questions
            [
                'type' => 'mcq',
                'question_text' => 'Which of the following are noble gases? (Select all that apply)',
                'explanation' => 'Helium (He) and Argon (Ar) are both noble gases in Group 18 of the periodic table.',
                'points' => 2.5,
                'difficulty' => 'medium',
                'tags' => ['chemistry', 'periodic-table', 'noble-gases'],
                'options' => [
                    ['text' => 'Helium (He)', 'correct' => true],
                    ['text' => 'Oxygen (O₂)', 'correct' => false],
                    ['text' => 'Argon (Ar)', 'correct' => false],
                    ['text' => 'Nitrogen (N₂)', 'correct' => false]
                ]
            ],
            [
                'type' => 'mcq',
                'question_text' => 'Which programming concepts are fundamental to Object-Oriented Programming?',
                'explanation' => 'Encapsulation, Inheritance, and Polymorphism are the three fundamental principles of OOP.',
                'points' => 3.0,
                'difficulty' => 'medium',
                'tags' => ['computer-science', 'oop', 'programming'],
                'options' => [
                    ['text' => 'Encapsulation', 'correct' => false],
                    ['text' => 'Inheritance', 'correct' => true],
                    ['text' => 'Compilation', 'correct' => false],
                    ['text' => 'Polymorphism', 'correct' => false],
                    ['text' => 'Debugging', 'correct' => false]
                ]
            ],

            // History MCQ Questions
            [
                'type' => 'mcq',
                'question_text' => 'In which year did World War II end?',
                'explanation' => 'World War II ended in 1945 with the surrender of Japan in September.',
                'points' => 1.0,
                'difficulty' => 'easy',
                'tags' => ['history', 'world-war', '20th-century'],
                'options' => [
                    ['text' => '1945', 'correct' => true],
                    ['text' => '1944', 'correct' => false],
                    ['text' => '1946', 'correct' => false],
                    ['text' => '1943', 'correct' => false]
                ]
            ],

            // Short Answer Questions
            [
                'type' => 'short',
                'question_text' => 'What is the capital city of Indonesia?',
                'explanation' => 'The capital city of Indonesia is Jakarta.',
                'points' => 1.0,
                'difficulty' => 'easy',
                'tags' => ['geography', 'capitals', 'asia']
            ],
            [
                'type' => 'short',
                'question_text' => 'Define photosynthesis in one sentence.',
                'explanation' => 'Photosynthesis is the process by which plants use sunlight, water, and carbon dioxide to produce glucose and oxygen.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['biology', 'plants', 'processes']
            ],
            [
                'type' => 'short',
                'question_text' => 'What does "CPU" stand for in computer terminology?',
                'explanation' => 'CPU stands for Central Processing Unit.',
                'points' => 1.0,
                'difficulty' => 'easy',
                'tags' => ['computer-science', 'hardware', 'acronyms']
            ],
            [
                'type' => 'short',
                'question_text' => 'Name the three states of matter.',
                'explanation' => 'The three basic states of matter are solid, liquid, and gas.',
                'points' => 1.5,
                'difficulty' => 'easy',
                'tags' => ['physics', 'states-of-matter', 'basic']
            ],
            [
                'type' => 'short',
                'question_text' => 'Calculate the area of a rectangle with length 8m and width 5m.',
                'explanation' => 'Area = length × width = 8m × 5m = 40 square meters.',
                'points' => 2.0,
                'difficulty' => 'easy',
                'tags' => ['mathematics', 'geometry', 'area']
            ],

            // Long Answer Questions
            [
                'type' => 'long',
                'question_text' => 'Explain the process of cellular respiration and its importance to living organisms.',
                'explanation' => 'Cellular respiration is a metabolic process that converts glucose and oxygen into ATP (energy), carbon dioxide, and water. It involves three main stages: glycolysis, Krebs cycle, and electron transport chain. This process is crucial for providing energy for all cellular activities in living organisms.',
                'points' => 5.0,
                'difficulty' => 'hard',
                'tags' => ['biology', 'cellular-processes', 'metabolism']
            ],
            [
                'type' => 'long',
                'question_text' => 'Discuss the causes and consequences of the Industrial Revolution.',
                'explanation' => 'The Industrial Revolution was caused by factors including technological innovations, availability of capital, natural resources, and labor. Its consequences included urbanization, changes in social structure, environmental impacts, and the foundation of modern industrial society.',
                'points' => 6.0,
                'difficulty' => 'hard',
                'tags' => ['history', 'industrial-revolution', 'society']
            ],
            [
                'type' => 'long',
                'question_text' => 'Analyze the advantages and disadvantages of renewable energy sources.',
                'explanation' => 'Renewable energy sources like solar, wind, and hydroelectric power offer advantages such as sustainability, reduced carbon emissions, and energy independence. However, they also face challenges including intermittency, high initial costs, and geographic limitations.',
                'points' => 5.5,
                'difficulty' => 'medium',
                'tags' => ['environmental-science', 'energy', 'sustainability']
            ],
            [
                'type' => 'long',
                'question_text' => 'Describe the structure and function of DNA in genetic inheritance.',
                'explanation' => 'DNA (Deoxyribonucleic acid) has a double helix structure composed of nucleotides containing bases A, T, G, and C. It stores genetic information and plays a crucial role in heredity by passing traits from parents to offspring through replication and transcription processes.',
                'points' => 5.0,
                'difficulty' => 'hard',
                'tags' => ['biology', 'genetics', 'dna']
            ],

            // Computer Science Questions
            [
                'type' => 'mcq',
                'question_text' => 'Which data structure uses LIFO (Last In, First Out) principle?',
                'explanation' => 'A stack follows the LIFO principle where the last element added is the first one to be removed.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['computer-science', 'data-structures', 'stack'],
                'options' => [
                    ['text' => 'Stack', 'correct' => true],
                    ['text' => 'Queue', 'correct' => false],
                    ['text' => 'Array', 'correct' => false],
                    ['text' => 'Linked List', 'correct' => false]
                ]
            ],
            [
                'type' => 'short',
                'question_text' => 'What is the time complexity of binary search algorithm?',
                'explanation' => 'Binary search has a time complexity of O(log n) because it divides the search space in half with each iteration.',
                'points' => 2.0,
                'difficulty' => 'medium',
                'tags' => ['computer-science', 'algorithms', 'complexity']
            ],

            // Literature Questions
            [
                'type' => 'mcq',
                'question_text' => 'Who wrote the novel "To Kill a Mockingbird"?',
                'explanation' => 'Harper Lee wrote "To Kill a Mockingbird", published in 1960.',
                'points' => 1.5,
                'difficulty' => 'easy',
                'tags' => ['literature', 'authors', 'novels'],
                'options' => [
                    ['text' => 'Harper Lee', 'correct' => true],
                    ['text' => 'Mark Twain', 'correct' => false],
                    ['text' => 'Ernest Hemingway', 'correct' => false],
                    ['text' => 'F. Scott Fitzgerald', 'correct' => false]
                ]
            ],

            // Economics Questions
            [
                'type' => 'short',
                'question_text' => 'Define supply and demand in economics.',
                'explanation' => 'Supply is the quantity of goods or services available in the market, while demand is the quantity that consumers are willing and able to purchase at various prices.',
                'points' => 2.5,
                'difficulty' => 'medium',
                'tags' => ['economics', 'market', 'basic-concepts']
            ],

            // Philosophy Questions
            [
                'type' => 'long',
                'question_text' => 'Explain Plato\'s Allegory of the Cave and its philosophical significance.',
                'explanation' => 'Plato\'s Allegory of the Cave illustrates the difference between knowledge and ignorance, reality and illusion. It demonstrates the philosopher\'s journey from ignorance to knowledge and the responsibility to enlighten others.',
                'points' => 6.0,
                'difficulty' => 'hard',
                'tags' => ['philosophy', 'plato', 'epistemology']
            ]
        ];

        foreach ($questions as $questionData) {
            $teacher = $teachers->random();

            $question = Question::create([
                'created_by' => $teacher->id,
                'type' => $questionData['type'],
                'question_text' => $questionData['question_text'],
                'explanation' => $questionData['explanation'] ?? null,
                'points' => $questionData['points'],
                'difficulty' => $questionData['difficulty'],
                'tags' => $questionData['tags'] ?? [],
            ]);

            // Add options for MCQ questions
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
