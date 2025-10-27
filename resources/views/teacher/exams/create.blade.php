<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('teacher.exams.index') }}"
               class="text-blue-600 hover:text-blue-800 mr-4">← Back to Exams</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Exam') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Messages -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('teacher.exams.store') }}" id="examForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <!-- Title -->
                                <div>
                                    <x-input-label for="title" :value="__('Exam Title')" />
                                    <x-text-input id="title"
                                                name="title"
                                                :value="old('title')"
                                                class="mt-1 block w-full"
                                                placeholder="e.g., Mathematics Midterm Exam" />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                    <textarea id="description"
                                            name="description"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="Brief description of the exam content and purpose">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <!-- Instructions -->
                                <div>
                                    <x-input-label for="instructions" :value="__('Student Instructions (Optional)')" />
                                    <textarea id="instructions"
                                            name="instructions"
                                            rows="4"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="Special instructions for students taking this exam">{{ old('instructions') }}</textarea>
                                    <x-input-error :messages="$errors->get('instructions')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Timing -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Exam Timing</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Start Time -->
                                <div>
                                    <x-input-label for="start_time" :value="__('Start Time')" />
                                    <x-text-input id="start_time"
                                                name="start_time"
                                                type="datetime-local"
                                                :value="old('start_time')"
                                                class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>

                                <!-- End Time -->
                                <div>
                                    <x-input-label for="end_time" :value="__('End Time')" />
                                    <x-text-input id="end_time"
                                                name="end_time"
                                                type="datetime-local"
                                                :value="old('end_time')"
                                                class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                                </div>

                                <!-- Duration -->
                                <div>
                                    <x-input-label for="duration_minutes" :value="__('Duration (Minutes)')" />
                                    <x-text-input id="duration_minutes"
                                                name="duration_minutes"
                                                type="number"
                                                min="1"
                                                :value="old('duration_minutes', 60)"
                                                class="mt-1 block w-full" />
                                    <p class="text-xs text-gray-500 mt-1">Maximum time allowed per attempt</p>
                                    <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Questions Selection -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Select Questions</h3>

                            <div class="flex gap-4 mb-4">
                                <x-multi-select 
                                    name="tags"
                                    id="tags-select"
                                    :options="$uniqueTags"
                                    :selected="request('tags', [])"
                                />

                                <button type="button" onclick="filterQuestions()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Filter by Tags
                                </button>
                            </div>


                            @if($questions->count() > 0)
                                <div class="border rounded-lg p-4 h-[80vh] overflow-y-auto" id="questions-container">
                                    @include('teacher.exams.partials.questions-list', ['questions' => $questions])
                                </div>
                                <div class="mt-4 p-3 bg-blue-50 rounded">
                                    <div class="flex justify-between items-center text-sm">
                                        <span>Selected Questions: <span id="selectedCount">0</span></span>
                                        <span>Total Marks: <span id="totalMarks">0</span></span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                    <p class="text-gray-500">No questions available. Please create some questions first.</p>
                                    <a href="{{ route('teacher.questions.create') }}"
                                       class="mt-2 text-blue-600 hover:text-blue-800">Create Your First Question</a>
                                </div>
                            @endif

                            <x-input-error :messages="$errors->get('questions')" class="mt-2" />
                        </div>

                        <!-- Exam Settings -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Exam Settings</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               id="randomize_questions"
                                               name="randomize_questions"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="randomize_questions" class="ml-2 text-sm text-gray-700">
                                            Randomize question order for each student
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               id="randomize_options"
                                               name="randomize_options"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="randomize_options" class="ml-2 text-sm text-gray-700">
                                            Randomize MCQ option order
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="max_attempts" :value="__('Maximum Attempts')" />
                                    <select id="max_attempts"
                                            name="max_attempts"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('max_attempts', 1) == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'attempt' : 'attempts' }}
                                            </option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('max_attempts')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Student Assignment -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Examinee</h3>

                            @if($students->count() > 0)
                                <div class="border rounded-lg p-4 max-h-64 overflow-y-auto">
                                    <div class="mb-3">
                                        <button type="button" onclick="selectAllStudents()" class="text-sm text-blue-600 hover:text-blue-800 mr-4">
                                            Select All
                                        </button>
                                        <button type="button" onclick="deselectAllStudents()" class="text-sm text-blue-600 hover:text-blue-800">
                                            Deselect All
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($students as $student)
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                       name="students[]"
                                                       value="{{ $student->id }}"
                                                       id="student_{{ $student->id }}"
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 student-checkbox">
                                                <label for="student_{{ $student->id }}" class="ml-2 text-sm text-gray-700 truncate">
                                                    {{ $student->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">
                                    Leave unchecked to manually assign examinee later
                                </p>
                            @else
                                <div class="text-center py-6 border-2 border-dashed border-gray-300 rounded-lg">
                                    <p class="text-gray-500">No students found in the system.</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2 items-center justify-end">
                            <x-secondary-button onclick="window.location='{{ route('teacher.exams.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Exam') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSelectedQuestions() {
            const checkboxes = document.querySelectorAll('.question-checkbox:checked');
            const selectedCount = checkboxes.length;

            document.getElementById('selectedCount').textContent = selectedCount;

            // Show/hide marks input for selected questions
            document.querySelectorAll('.question-item').forEach(item => {
                const checkbox = item.querySelector('.question-checkbox');
                const marksInput = item.querySelector('.question-marks-input');

                if (checkbox.checked) {
                    marksInput.classList.remove('hidden');
                } else {
                    marksInput.classList.add('hidden');
                }
            });

            // Calculate total marks
            updateTotalMarks();
        }

        function updateTotalMarks() {
            let total = 0;
            document.querySelectorAll('.question-checkbox:checked').forEach(checkbox => {
                const questionItem = checkbox.closest('.question-item');
                const marksInput = questionItem.querySelector('input[name="question_marks[]"]');
                if (marksInput) {
                    total += parseFloat(marksInput.value) || 0;
                }
            });

            document.getElementById('totalMarks').textContent = total.toFixed(1);
        }

        function selectAllStudents() {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                cb.checked = true;
            });
        }

        function deselectAllStudents() {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                cb.checked = false;
            });
        }

        // Listen for changes in question marks
        document.addEventListener('input', function(e) {
            if (e.target.name === 'question_marks[]') {
                updateTotalMarks();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedQuestions();
        });

        function filterQuestions() {
            const tagsElement = document.querySelectorAll('[name="tags[]"]');
            const selectedTags = Array.from(tagsElement).map(tagElement => tagElement.value);
            console.log('selectedTags', selectedTags)

            // const params = new URLSearchParams();
            // selectedTags.forEach(tag => params.append('tags[]', tag));
            
            // fetch(`{{ route('teacher.exams.filter-questions') }}?${params}`)
            //     .then(response => response.text())
            //     .then(html => {
            //         document.getElementById('questions-container').innerHTML = 
            //             '<div class="space-y-3">' + html + '</div>';
            //         updateSelectedQuestions();
            //     })
            //     .catch(error => console.error('Error:', error));
        }
    </script>
</x-app-layout>
