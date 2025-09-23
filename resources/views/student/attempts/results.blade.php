<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('student.exams.show', $attempt->exam) }}"
               class="text-blue-600 hover:text-blue-800 mr-4">← Back to Exam</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Exam Results: {{ $attempt->exam->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Results Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Your Results</h3>
                        @if($attempt->percentage_score !== null)
                            <div class="text-4xl font-bold {{ $attempt->percentage_score >= 70 ? 'text-green-600' : ($attempt->percentage_score >= 50 ? 'text-yellow-600' : 'text-red-600') }} mb-2">
                                {{ round($attempt->percentage_score, 1) }}%
                            </div>
                            <div class="text-gray-600">
                                {{ $attempt->total_score }} out of {{ $attempt->exam->total_marks }} marks
                            </div>
                        @else
                            <div class="text-yellow-600 text-lg font-medium">
                                Grading in Progress
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-xl font-bold text-blue-600">{{ $summary['totalQuestions'] }}</div>
                            <div class="text-sm text-blue-600">Total Questions</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-xl font-bold text-green-600">{{ $summary['answeredQuestions'] }}</div>
                            <div class="text-sm text-green-600">Answered</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-xl font-bold text-purple-600">{{ $summary['gradedQuestions'] }}</div>
                            <div class="text-sm text-purple-600">Graded</div>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg">
                            <div class="text-xl font-bold text-indigo-600">{{ $attempt->attempt_number }}</div>
                            <div class="text-sm text-indigo-600">Attempt Number</div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Started:</span>
                                <span>{{ $attempt->started_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Submitted:</span>
                                <span>{{ $attempt->submitted_at->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Duration:</span>
                                <span>
                                    {{ $timeTakenFormatted = ($attempt->started_at && $attempt->submitted_at)
                                            ? $attempt->started_at->diff($attempt->submitted_at)->i . 'm ' . $attempt->started_at->diff($attempt->submitted_at)->s . 's'
                                            : 'N/A'; }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{
                                    $attempt->isGraded() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                }}">
                                    {{ $attempt->isGraded() ? 'Graded' : 'Pending Review' }}
                                </span>
                            </div>
                            @if(!$attempt->isGraded())
                                <div class="text-yellow-600 text-sm">
                                    Some questions require manual grading by your teacher.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question-by-Question Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Question Review</h3>

                    <div class="space-y-6">
                        @foreach($questions as $index => $examQuestion)
                            @php
                                $question = $examQuestion->question;
                                $answer = $answers->get($question->id);
                                $questionNumber = $index + 1;
                            @endphp

                            <div class="border border-gray-200 rounded-lg p-6">
                                <!-- Question Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                            Question {{ $questionNumber }}
                                        </span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $question->getTypeDisplayName() }}
                                        </span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->getDifficultyColorClass() }}">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-700">
                                            @if($answer && $answer->marks_awarded !== null)
                                                <span class="{{ $answer->marks_awarded > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $answer->marks_awarded }} / {{ $examQuestion->marks }}
                                                </span>
                                            @else
                                                <span class="text-gray-500">- / {{ $examQuestion->marks }}</span>
                                            @endif
                                            marks
                                        </div>
                                    </div>
                                </div>

                                <!-- Question Text -->
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-900 mb-2">{{ $question->question_text }}</h4>
                                </div>

                                <!-- Question Images -->
                                @if($question->images && $question->images->count() > 0)
                                    <div class="mb-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($question->images as $image)
                                                <div class="relative group cursor-pointer" onclick="openImageModal('{{ $image->getUrl() }}', '{{ $image->alt_text ?: $image->original_name }}')">
                                                    <img src="{{ $image->getUrl() }}"
                                                         alt="{{ $image->alt_text ?: $image->original_name }}"
                                                         class="w-20 h-20 object-cover rounded border border-gray-300 hover:border-blue-500 transition-colors">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($question->isMCQ())
                                    <!-- MCQ Results -->
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Options:</h5>
                                        <div class="space-y-2">
                                            @php $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp
                                            @foreach($question->options as $optionIndex => $option)
                                                @php
                                                    $isCorrect = $option->is_correct;
                                                    $isSelected = $answer && $answer->selected_options && in_array($option->id, $answer->selected_options);
                                                @endphp
                                                <div class="flex items-center p-2 border rounded {{
                                                    $isCorrect ? 'border-green-400 bg-green-50' :
                                                    ($isSelected && !$isCorrect ? 'border-red-400 bg-red-50' : 'border-gray-200')
                                                }}">
                                                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full {{
                                                        $isCorrect ? 'bg-green-500 text-white' :
                                                        ($isSelected ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600')
                                                    }} mr-3 text-sm font-medium">
                                                        {{ $optionLabels[$optionIndex] ?? chr(65 + $optionIndex) }}
                                                    </span>
                                                    <span class="flex-1 text-gray-800">{{ $option->option_text }}</span>
                                                    <div class="flex items-center gap-2">
                                                        @if($isSelected)
                                                            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded">Your Answer</span>
                                                        @endif
                                                        @if($isCorrect)
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Text Answer Results -->
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Your Answer:</h5>
                                        @if($answer && $answer->answer_text)
                                            <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                                <p class="text-gray-800 whitespace-pre-wrap">{{ $answer->answer_text }}</p>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 border border-gray-200 rounded p-3 text-gray-500 italic">
                                                No answer provided
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Grader Comments -->
                                @if($answer && $answer->grader_comments)
                                    <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded-r">
                                        <h5 class="font-medium text-blue-800 mb-1">Teacher's Comments:</h5>
                                        <p class="text-blue-700 text-sm">{{ $answer->grader_comments }}</p>
                                    </div>
                                @endif

                                <!-- Question Explanation -->
                                @if($question->explanation)
                                    <div class="mt-4 p-3 bg-gray-50 border-l-4 border-gray-400 rounded-r">
                                        <h5 class="font-medium text-gray-800 mb-1">Explanation:</h5>
                                        <p class="text-gray-700 text-sm">{{ $question->explanation }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium" id="modalImageTitle">Image</h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="text-center">
                <img id="modalImage" src="" alt="" class="max-w-full max-h-96 mx-auto">
            </div>
        </div>
    </div>

    <script>
        function openImageModal(src, title) {
            document.getElementById('modalImage').src = src;
            document.getElementById('modalImageTitle').textContent = title;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
