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
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="toggleOptionsSection()">
                                <option value="mcq" {{ old('type', $question->type) == 'mcq' ? 'selected' : '' }}>Multiple Choice Question</option>
                                <option value="short" {{ old('type', $question->type) == 'short' ? 'selected' : '' }}>Short Answer</option>
                                <option value="long" {{ old('type', $question->type) == 'long' ? 'selected' : '' }}>Long Answer</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <x-input-label for="question_text" :value="__('Question Text')" />
                            <textarea id="question_text" name="question_text" class="tinymce-field">{{ old('question_text', $question->question_text) }}</textarea>
                            <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
                        </div>

                        <!-- Question Image -->
                        <div class="mb-6">
                            <x-input-label for="images" :value="__('Question Images (Optional)')" />
                            <div class="mt-2 flex flex-wrap gap-4" id="imageContainer">
                                @if ($question->images->count() > 0)
                                    @foreach ($question->images as $image)
                                        <div class="relative image-preview w-fit">
                                            <input type='text' name="existing_images[]" value="{{ $image->path }}" class="hidden image-input" />
                                            <img src="{{ $image->getUrl() }}" class="h-60 w-60 rounded-lg object-contain object-center border-2 border-gray-300 border-dashed" />
                                            <button type='button' onclick="removeImage(this, false)" class="w-5 h-5 flex items-center hover:bg-gray-100 justify-center bg-white absolute -top-2 -right-2 rounded-full text-gray-300 border border-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                                <button type='button' class="add-image-btn transition-all hover:bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg h-60 w-60 text-gray-300" onclick="addImage()">+</button>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Maximum file size: 2MB per image. Supported formats:
                                JPEG, JPG, PNG, GIF</p>
                            <x-input-error :messages="implode(', ', collect($errors->get('images.*'))->flatten()->toArray())" class="mt-2" />
                        </div>

                        <!-- MCQ Options -->
                        <div id="mcqOptions" class="mb-6 {{ old('type', $question->type) === 'mcq' ? '' : 'hidden' }}">
                            <x-input-label :value="__('Answer Options')" />
                            <div class="mt-2 space-y-3" id="optionsContainer">
                                @php
                                    $options = old('options', $question->options);
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
                                        <div>
                                            <textarea name="options[{{ $i }}][option_text]" class="tinymce-field">{{ $options[$i]['option_text'] ?? '' }}</textarea>
                                            <x-input-error :messages="$errors->get('options.' . $i . '.option_text')" class="mt-2" />
                                            <div class="mt-2 space-y-2" id="optionImageContainer">
                                                @if(!empty($options[$i]->image_path))
                                                    <div class="relative image-preview w-fit">
                                                        <input type='text' name="options[{{ $i }}][existing_option_image]" value="{{ $options[$i]->image_path }}" class="hidden image-input" />
                                                        @if (!empty($options[$i]->image_path))
                                                            <img src="{{ Storage::url($options[$i]->image_path) }}" class="h-60 w-60 rounded-lg object-contain object-center border-2 border-gray-300 border-dashed" />
                                                        @endif
                                                        <button type='button' onclick="removeImage(this, true)" class="w-5 h-5 flex items-center hover:bg-gray-100 justify-center bg-white absolute -top-2 -right-2 rounded-full text-gray-300 border border-gray-300">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                        </button>
                                                    </div>
                                                @endif
                                                <button type='button' class="add-image-btn {{ empty($options[$i]->image_path) ? '' : 'hidden' }} transition-all hover:bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg h-60 w-60 text-gray-300" onclick="addOptionImage(this, {{ $i }})">+</button>
                                                <div class="text-sm text-gray-500">Maximum file size: 2MB per image. Supported formats: JPEG, JPG, PNG, GIF</div>
                                                <x-input-error :messages="$errors->get('options.' . $i . '.new_option_image')" class="" />
                                            </div>

                                        </div>
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

        console.log('optionCount', optionCount)

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
            const nextIndex = optionCount

            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-item flex items-start gap-3';
            optionDiv.innerHTML = `
                <input type="radio" name="correct_options[]" value="${optionCount}">
                <span class="option-label">${optionLabels[optionCount]}.</span>
                <div>
                    <textarea name="options[${nextIndex}][option_text]" class="tinymce-field"></textarea>
                    <div class="mt-2 space-y-2" id="optionImageContainer">
                        <button type='button' class="add-image-btn transition-all hover:bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg h-60 w-60 text-gray-300" onclick="addOptionImage(this, ${nextIndex})">+</button>
                        <div class="text-sm text-gray-500">Maximum file size: 2MB per image. Supported formats: JPEG, JPG, PNG, GIF</div>
                    </div>
                </div>
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

        function addImage() {
            const imageContainer = document.querySelector('#imageContainer')
            const newImagePreview = document.createElement('div')
            newImagePreview.classList.add('relative', 'image-preview', 'hidden')
            newImagePreview.innerHTML = `
                                    <input type='file' name="new_images[]" class="hidden image-input" onchange="handleImagePreview(this, false)" />
                                    <img src="" class="h-60 w-60 rounded-lg object-contain object-center border-2 border-gray-300 border-dashed" />
                                    <button type='button' onclick="removeImage(this, false)" class="w-5 h-5 flex items-center hover:bg-gray-100 justify-center bg-white absolute -top-2 -right-2 rounded-full text-gray-300 border border-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>`

            imageContainer.prepend(newImagePreview)
            imageContainer.querySelector('.image-input').click()
        }

        function handleImagePreview(input, isOptionImage) {
            const file = input.files[0]
            const img = input.nextElementSibling
            const imgPreview = input.parentElement
            const addImageBtn = imgPreview.parentElement.querySelector('.add-image-btn')

            if (file) {
                const reader = new FileReader()
                reader.onload = e => {
                    img.src = e.target.result
                    imgPreview.classList.remove('hidden')
                    if (isOptionImage) {
                        addImageBtn.classList.add('hidden')
                    }
                }

                reader.readAsDataURL(file)
            }
        }

        function removeImage(removeButton, isOptionImage) {
            if (isOptionImage) {
                console.log(removeButton.parentElement)
                const addImageBtn = removeButton.closest('#optionImageContainer').querySelector('.add-image-btn')
                console.log(addImageBtn)

                addImageBtn.classList.remove('hidden')
            }

            removeButton.closest('.image-preview').remove()
        }

        function addOptionImage(addImageBtn, index) {
            const optionImageContainer = addImageBtn.parentElement

            const newImagePreview = document.createElement('div')
            newImagePreview.classList.add('relative', 'image-preview', 'hidden', 'w-fit')
            newImagePreview.innerHTML = `
                                    <input type='file' name="options[${index}][new_option_image]" class="hidden image-input" onchange="handleImagePreview(this, true)" />
                                    <img src="" class="h-60 w-60 rounded-lg object-contain object-center border-2 border-gray-300 border-dashed" />
                                    <button type='button' onclick="removeImage(this, true)" class="w-5 h-5 flex items-center hover:bg-gray-100 justify-center bg-white absolute -top-2 -right-2 rounded-full text-gray-300 border border-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>`

            optionImageContainer.prepend(newImagePreview)
            optionImageContainer.querySelector('.image-input').click()


        }
    </script>
</x-app-layout>
