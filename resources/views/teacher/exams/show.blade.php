<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('teacher.exams.index') }}"
                   class="text-blue-600 hover:text-blue-800 mr-4">← Back to Exams</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $exam->title }}
                </h2>
            </div>
            <div class="flex space-x-2">
                @if(!$exam->isPublished() || ($exam->isPublished() && now()->lt($exam->start_time)))
                    <a href="{{ route('teacher.exams.edit', $exam) }}"
                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit Exam
                    </a>
                @endif

                @if($exam->isDraft())
                    <form action="{{ route('teacher.exams.publish', $exam) }}"
                          method="POST" class="inline-block" data-confirm="Are you sure you want to publish this exam?">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Publish Exam
                        </button>
                    </form>
                @elseif($exam->isPublished() && now()->lt($exam->start_time))
                    <form action="{{ route('teacher.exams.unpublish', $exam) }}"
                          method="POST" class="inline-block" data-confirm="Are you sure you want to unpublish this exam?">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                            Unpublish
                        </button>
                    </form>
                @endif

                @if($exam->isDraft())
                    <form action="{{ route('teacher.exams.destroy', $exam) }}"
                          method="POST" class="inline-block"
                          data-confirm="Are you sure you want to delete this exam? This action cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete Exam
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Exam Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $exam->getStatusColorClass() }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                            @if($exam->isActive())
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Active Now
                                </span>
                            @elseif($exam->isUpcoming())
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Upcoming
                                </span>
                            @elseif($exam->isPast())
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Past
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            Created {{ $exam->created_at->diffForHumans() }} by {{ $exam->creator->name }}
                        </div>
                    </div>

                    @if($exam->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-600">{{ $exam->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $exam->examQuestions->count() }}</div>
                            <div class="text-sm text-blue-600">Questions</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $exam->total_marks }}</div>
                            <div class="text-sm text-green-600">Total Marks</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $exam->getDurationFormatted() }}</div>
                            <div class="text-sm text-purple-600">Duration</div>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">{{ $exam->assignedStudents->count() }}</div>
                            <div class="text-sm text-indigo-600">Assigned Examinees</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Start Time:</span>
                                <span>{{ $exam->start_time->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">End Time:</span>
                                <span>{{ $exam->end_time->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Max Attempts:</span>
                                <span>{{ $exam->max_attempts }}</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Randomize Questions:</span>
                                <span class="{{ $exam->randomize_questions ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $exam->randomize_questions ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Randomize Options:</span>
                                <span class="{{ $exam->randomize_options ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $exam->randomize_options ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            @if($exam->instructions)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Instructions for Examinees</h3>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $exam->instructions }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Questions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Exam Questions ({{ $exam->examQuestions->count() }})</h3>
                        <div class="text-sm text-gray-500">
                            Total: {{ $exam->total_marks }} {{ Str::plural('mark', $exam->total_marks) }}
                        </div>
                    </div>

                    @if($exam->examQuestions->count() > 0)
                        <div class="space-y-6">
                            @foreach($exam->examQuestions as $examQuestion)
                                @php $question = $examQuestion->question; @endphp
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <!-- Question Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2 py-1 rounded-full bg-gray-200 text-xs text-gray-800">
                                                #{{ $question->id }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $question->getTypeDisplayName() }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->getDifficultyColorClass() }}">
                                                {{ ucfirst($question->difficulty) }}
                                            </span>
                                        </div>
                                        <div class="text-sm font-medium text-gray-700">
                                            {{ $examQuestion->marks }} {{ Str::plural('mark', $examQuestion->marks) }}
                                        </div>
                                    </div>

                                    <!-- Question Text -->
                                    <div class="mb-4">
                                        <div class="bg-gray-50  p-4 rounded-lg mb-4">
                                            <div class="prose">{!! $question->question_text !!}</div>
                                        </div>

                                        @if($question->tags && count($question->tags) > 0)
                                            <div class="flex flex-wrap gap-1 mb-3">
                                                @foreach($question->tags as $tag)
                                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                                        {{ $tag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Question Images -->
                                    @if($question->images && $question->images->count() > 0)
                                        <div class="mb-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($question->images as $image)
                                                    <div class="relative group cursor-pointer" onclick="openImageModal('{{ $image->getUrl() }}')">
                                                        <img src="{{ $image->getUrl() }}"
                                                             alt="question image"
                                                             class="w-20 h-20 object-cover rounded border border-gray-300 hover:border-blue-500 transition-colors">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded transition-all flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Click images to view full size</p>
                                        </div>
                                    @endif

                                    <!-- MCQ Options -->
                                    @if($question->isMCQ() && $question->options && $question->options->count() > 0)
                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-700 mb-2">Options:</h4>
                                            <div class="space-y-2">
                                                @php $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp
                                                @foreach($question->options as $index => $option)
                                                    <div class="flex items-center p-2 border rounded {{ $option->is_correct ? 'border-green-400 bg-green-50' : 'border-gray-200' }}">
                                                        <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600' }} mr-3 text-sm font-medium">
                                                            {{ $optionLabels[$index] ?? chr(65 + $index) }}
                                                        </span>
                                                        <div class="flex flex-1 flex-col gap-2 justify-center" >
                                                            <div class="prose flex-1 text-gray-800">{!! $option->option_text !!}</div>
                                                            @if($option->image_path)
                                                                <img src="{{ Storage::url($option->image_path) }}"
                                                                    alt="option_image"
                                                                    class="w-fit rounded-xl h-48 object-contain cursor-pointer"
                                                                    onclick="openImageModal('{{ Storage::url($option->image_path) }}', 'option_image')">
                                                            @endif
                                                        </div>
                                                        @if($option->is_correct)
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Answer Type Info for Non-MCQ -->
                                    @if(!$question->isMCQ())
                                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-600">
                                                @if($question->isShort())
                                                    <strong>Answer Type:</strong> Short answer expected (typically 1-2 sentences or a brief phrase)
                                                @elseif($question->isLong())
                                                    <strong>Answer Type:</strong> Long answer expected (detailed explanation or essay-style response)
                                                @endif
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Explanation -->
                                    @if($question->explanation)
                                        <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r">
                                            <h5 class="font-medium text-blue-800 mb-1">Explanation:</h5>
                                            <p class="text-blue-700 prose text-sm">
                                                {!! $question->explanation !!}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Question Meta -->
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>Created by: {{ $question->creator->name }}</span>
                                            <span>Original points: {{ $question->points }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Question Statistics -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-3">Question Statistics</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">MCQ Questions:</span>
                                    <span class="text-blue-600">{{ $exam->examQuestions->filter(function ($eq) { return $eq->question->type === 'mcq'; })->count(); }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Short Answer:</span>
                                    <span class="text-green-600">{{ $exam->examQuestions->filter(function ($eq) { return $eq->question->type === 'short'; })->count(); }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Long Answer:</span>
                                    <span class="text-purple-600">{{ $exam->examQuestions->filter(function ($eq) { return $eq->question->type === 'long'; })->count(); }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Avg. Difficulty:</span>
                                    <span class="text-gray-700">
                                        @php
                                            $difficulties = $exam->examQuestions->pluck('question.difficulty');
                                            $difficultyMap = ['easy' => 1, 'medium' => 2, 'hard' => 3];
                                            $avgDifficulty = $difficulties->avg(function($d) use ($difficultyMap) {
                                                return $difficultyMap[$d] ?? 2;
                                            });
                                            echo $avgDifficulty < 1.5 ? 'Easy' : ($avgDifficulty < 2.5 ? 'Medium' : 'Hard');
                                        @endphp
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No questions added</h3>
                            <p class="mt-1 text-sm text-gray-500">Add questions to this exam to make it available to examinees.</p>
                            <div class="mt-6">
                                <a href="{{ route('teacher.exams.edit', $exam) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Add Questions
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Students -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Assigned Examinees ({{ $exam->assignedStudents->count() }})
                    </h3>

                    @if($exam->assignedStudents->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($exam->assignedStudents as $student)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $student->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $student->email }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-medium">
                                                    {{ substr($student->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Examinees assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">Assign examinees to this exam to make it available to them.</p>
                            <div class="mt-6">
                                <a href="{{ route('teacher.exams.edit', $exam) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Assign Examinees
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
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
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
