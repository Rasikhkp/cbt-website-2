<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('teacher.questions.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                Questions</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Question') }}
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
                    <form method="POST" action="{{ route('teacher.questions.update', $question) }}"
                        enctype="multipart/form-data" id="questionForm">
                        @csrf
                        @method('PATCH')

                        <!-- Question Type -->
                        <div class="mb-6">
                            <x-input-label for="type" :value="__('Question Type')" />
                            <select id="type" name="type"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                onchange="toggleOptionsSection()">
                                <option value="mcq" {{ old('type', $question->type) == 'mcq' ? 'selected' : '' }}>
                                    Multiple Choice Question</option>
                                <option value="short" {{ old('type', $question->type) == 'short' ? 'selected' : '' }}>
                                    Short Answer</option>
                                <option value="long" {{ old('type', $question->type) == 'long' ? 'selected' : '' }}>
                                    Long Answer</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <x-input-label for="question_text" :value="__('Question Text')" />
                            <textarea id="question_text" name="question_text" class="tinymce-field">{{ old('question_text', $question->question_text) }}</textarea>
                            <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
                        </div>

                        <!-- Existing Images -->
                        @if ($question->images->count() > 0)
                            <div class="mb-6">
                                <x-input-label :value="__('Current Images')" />
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($question->images as $image)
                                        <div class="border rounded-lg p-4">
                                            <img src="{{ $image->getUrl() }}" alt="{{ $image->alt_text }}"
                                                class="w-full h-32 object-contain mb-2">
                                            <p class="text-sm text-gray-600 mb-2">{{ $image->original_name }}</p>
                                            <p class="text-xs text-gray-500 mb-2">{{ $image->getFormattedSize() }}</p>
                                            @if ($image->alt_text)
                                                <p class="text-xs text-gray-600 mb-2">Alt: {{ $image->alt_text }}</p>
                                            @endif
                                            <label class="flex items-center text-sm">
                                                <input type="checkbox" name="remove_images[]"
                                                    value="{{ $image->id }}" class="rounded mr-2">
                                                Remove this image
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add New Images Section -->
                        <div class="mb-6">
                            <x-input-label for="images" :value="__('Add New Images (Optional)')" />
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

                        <!-- MCQ Options -->
                        <div id="mcqOptions" class="mb-6 {{ $question->type === 'mcq' ? '' : 'hidden' }}">
                            <x-input-label :value="__('Answer Options')" />
                            <div class="mt-2 space-y-3" id="optionsContainer">
                                @php
                                    $options = old('options', $question->options->pluck('option_text')->toArray());
                                    $correctOptions = old(
                                        'correct_options',
                                        $question->options->where('is_correct', true)->keys()->toArray(),
                                    );
                                    $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];
                                @endphp

                                @for ($i = 0; $i < max(2, count($options)); $i++)
                                    <div class="option-item flex items-start gap-3">
                                        <div class="flex gap-3 items-center mt-1">
                                            <input type="radio" name="correct_options[]" value="{{ $i }}" {{ in_array($i, $correctOptions) ? 'checked' : '' }}>
                                            <span class="option-label">{{ $optionLabels[$i] ?? chr(65 + $i) }}.</span>
                                        </div>
                                        <textarea name="options[]" class="tinymce-field">{{ $options[$i] ?? '' }}</textarea>
                                    </div>
                                @endfor
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
                                    min="0.1" max="100" :value="old('points', $question->points)" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('points')" class="mt-2" />
                            </div>

                            <!-- Difficulty -->
                            <div>
                                <x-input-label for="difficulty" :value="__('Difficulty')" />
                                <select id="difficulty" name="difficulty"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="easy"
                                        {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy
                                    </option>
                                    <option value="medium"
                                        {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>
                                        Medium</option>
                                    <option value="hard"
                                        {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('difficulty')" class="mt-2" />
                            </div>

                            <!-- Tags -->
                            <div>
                                <x-input-label for="tags" :value="__('Tags')" />
                                <x-text-input id="tags" name="tags" :value="old('tags', $question->tags ? implode(', ', $question->tags) : '')"
                                    placeholder="math, algebra, grade-10" class="mt-1 block w-full" />
                                <p class="text-xs text-gray-500 mt-1">Separate tags with commas</p>
                                <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Explanation -->
                        <div class="mb-6">
                            <x-input-label for="explanation" :value="__('Explanation (Optional)')" />
                            <textarea id="explanation" name="explanation" class="tinymce-field prose">{{ old('explanation', $question->explanation) }}</textarea>
                            <x-input-error :messages="$errors->get('explanation')" class="mt-2" />
                        </div>

                        <div class="flex gap-2 items-center justify-end">
                            <x-secondary-button onclick="window.location='{{ route('teacher.questions.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Question') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let imageUploadCount = 1;
        let optionCount = {{ max(2, count(old('options', $question->options->toArray()))) }};

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
                <span class="option-label">${optionLabels[optionCount] || String.fromCharCode(65 + optionCount)}.</span>
                <textarea name="options[]" class="tinymce-field"></textarea>
            `;

            container.appendChild(optionDiv);
            optionCount++;

            addTinyMCE()
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
    </script>
</x-app-layout>
