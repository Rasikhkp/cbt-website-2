<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('teacher.questions.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                Questions</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Question') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Messages -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('teacher.questions.store') }}" enctype="multipart/form-data"
                        id="questionForm">
                        @csrf

                        <!-- Question Type -->
                        <div class="mb-6">
                            <x-input-label for="type" :value="__('Question Type')" />
                            <select id="type" name="type"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                onchange="toggleOptionsSection()">
                                <option value="">Select question type...</option>
                                <option value="mcq" {{ old('type') == 'mcq' ? 'selected' : '' }}>Multiple Choice
                                    Question</option>
                                <option value="short" {{ old('type') == 'short' ? 'selected' : '' }}>Short Answer
                                </option>
                                <option value="long" {{ old('type') == 'long' ? 'selected' : '' }}>Long Answer
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <x-input-label for="question_text" :value="__('Question Text')" />
                            <textarea id="question_text" name="question_text" rows="4"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Enter your question here...">{{ old('question_text') }}</textarea>
                            <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
                        </div>

                        <!-- Images Section -->
                        <div class="mb-6">
                            <x-input-label for="images" :value="__('Question Images (Optional)')" />
                            <div class="mt-2 space-y-4" id="imagesContainer">
                                <div class="image-upload-item border-2 border-dashed border-gray-300 p-4 rounded-lg">
                                    <div class="flex flex-col items-center">
                                        <input type="file" name="images[]" accept="image/*"
                                            class="image-input hidden" onchange="handleImagePreview(this, 0)">
                                        <button type="button" onclick="this.previousElementSibling.click()"
                                            class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded text-sm">
                                            Choose Image
                                        </button>
                                        <div class="image-preview mt-2 hidden">
                                            <img src="" alt="Preview" class="max-w-full h-32 object-contain">
                                            <input type="text" name="alt_texts[]" placeholder="Alt text (optional)"
                                                class="mt-2 block w-full border-gray-300 rounded text-sm">
                                            <button type="button" onclick="removeImageUpload(this)"
                                                class="mt-2 text-red-600 hover:text-red-800 text-sm">Remove
                                                Image</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="addImageUpload()"
                                class="mt-2 text-blue-600 hover:text-blue-800 text-sm">+ Add Another Image</button>
                            <p class="text-sm text-gray-500 mt-1">Maximum file size: 2MB per image. Supported formats:
                                JPEG, JPG, PNG, GIF</p>
                            <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                        </div>

                        <!-- MCQ Options (Hidden by default) -->
                        <div id="mcqOptions" class="mb-6 hidden">
                            <x-input-label :value="__('Answer Options')" />
                            <div class="mt-2 space-y-3" id="optionsContainer">
                                <div class="option-item flex items-center space-x-3">
                                    <input type="radio" name="correct_options[]" value="0">
                                    <span class="option-label">A.</span>
                                    <input type="text" name="options[]" placeholder="Enter option A"
                                        class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                </div>
                                <div class="option-item flex items-center space-x-3">
                                    <input type="radio" name="correct_options[]" value="1">
                                    <span class="option-label">B.</span>
                                    <input type="text" name="options[]" placeholder="Enter option B"
                                        class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                </div>
                            </div>
                            <button type="button" onclick="addOption()"
                                class="mt-3 text-blue-600 hover:text-blue-800 text-sm">
                                + Add Option
                            </button>
                            <button type="button" onclick="removeOption()"
                                class="mt-3 ml-4 text-red-600 hover:text-red-800 text-sm">
                                - Remove Option
                            </button>
                            <p class="text-sm text-gray-500 mt-2">Check the box(es) next to the correct answer(s)</p>
                            <x-input-error :messages="$errors->get('options')" class="mt-2" />
                            <x-input-error :messages="$errors->get('correct_options')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Points -->
                            <div>
                                <x-input-label for="points" :value="__('Points')" />
                                <x-text-input id="points" name="points" type="number" step="0.1"
                                    min="0.1" max="100" :value="old('points', 1.0)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('points')" class="mt-2" />
                            </div>

                            <!-- Difficulty -->
                            <div>
                                <x-input-label for="difficulty" :value="__('Difficulty')" />
                                <select id="difficulty" name="difficulty"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy
                                    </option>
                                    <option value="medium"
                                        {{ old('difficulty', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('difficulty')" class="mt-2" />
                            </div>

                            <!-- Tags -->
                            <div>
                                <x-input-label for="tags" :value="__('Tags')" />
                                <x-text-input id="tags" name="tags" :value="old('tags')"
                                    placeholder="math, algebra, grade-10" class="mt-1 block w-full" />
                                <p class="text-xs text-gray-500 mt-1">Separate tags with commas</p>
                                <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Explanation -->
                        <div class="mb-6">
                            <x-input-label for="explanation" :value="__('Explanation (Optional)')" />
                            <textarea id="explanation" name="explanation" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Provide explanation for the correct answer...">{{ old('explanation') }}</textarea>
                            <x-input-error :messages="$errors->get('explanation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <x-secondary-button onclick="window.location='{{ route('teacher.questions.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Question') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let imageUploadCount = 1;
        let optionCount = 2;

        function toggleOptionsSection() {
            const type = document.getElementById('type').value;
            const mcqOptions = document.getElementById('mcqOptions');

            if (type === 'mcq') {
                mcqOptions.classList.remove('hidden');
            } else {
                mcqOptions.classList.add('hidden');
            }
        }

        function addOption() {
            if (optionCount >= 6) return; // Maximum 6 options

            const container = document.getElementById('optionsContainer');
            const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];

            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-item flex items-center space-x-3';
            optionDiv.innerHTML = `
                <input type="radio" name="correct_options[]" value="${optionCount}">
                <span class="option-label">${optionLabels[optionCount]}.</span>
                <input type="text" name="options[]" placeholder="Enter option ${optionLabels[optionCount]}"
                       class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            `;

            container.appendChild(optionDiv);
            optionCount++;
        }

        function removeOption() {
            if (optionCount <= 2) return; // Minimum 2 options

            const container = document.getElementById('optionsContainer');
            const lastOption = container.lastElementChild;
            if (lastOption) {
                container.removeChild(lastOption);
                optionCount--;
            }
        }

        function addImageUpload() {
            const container = document.getElementById('imagesContainer');
            const uploadDiv = document.createElement('div');
            uploadDiv.className = 'image-upload-item border-2 border-dashed border-gray-300 p-4 rounded-lg';
            uploadDiv.innerHTML = `
                <div class="flex flex-col items-center">
                    <input type="file" name="images[]" accept="image/*" class="image-input hidden" onchange="handleImagePreview(this, ${imageUploadCount})">
                    <button type="button" onclick="this.previousElementSibling.click()"
                            class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded text-sm">
                        Choose Image
                    </button>
                    <div class="image-preview mt-2 hidden">
                        <img src="" alt="Preview" class="max-w-full h-32 object-contain">
                        <input type="text" name="alt_texts[]" placeholder="Alt text (optional)"
                               class="mt-2 block w-full border-gray-300 rounded text-sm">
                        <button type="button" onclick="removeImageUpload(this)"
                                class="mt-2 text-red-600 hover:text-red-800 text-sm">Remove Image</button>
                    </div>
                </div>
            `;

            container.appendChild(uploadDiv);
            imageUploadCount++;
        }

        function handleImagePreview(input, index) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = input.parentElement.querySelector('.image-preview');
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImageUpload(button) {
            const uploadItem = button.closest('.image-upload-item');
            uploadItem.remove();
        }

        // Initialize form based on old input
        document.addEventListener('DOMContentLoaded', function() {
            toggleOptionsSection();

            // Handle old MCQ options if form was submitted with errors
            @if (old('options'))
                @foreach (old('options', []) as $index => $option)
                    @if ($index > 1)
                        addOption();
                    @endif
                @endforeach
            @endif
        });
    </script>
</x-app-layout>
