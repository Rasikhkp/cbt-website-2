<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('teacher.questions.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                    Questions</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Question Details') }}
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('teacher.questions.edit', $question) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Question
                </a>
                <form action="{{ route('teacher.questions.destroy', $question) }}" method="POST" class="inline-block"
                    onsubmit="return confirm('Are you sure you want to delete this question?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Delete Question
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Question Header -->
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $question->getTypeDisplayName() }}
                            </span>
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full {{ $question->getDifficultyColorClass() }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ $question->points }} {{ Str::plural('point', $question->points) }}
                            </span>
                        </div>

                        @if ($question->tags && count($question->tags) > 0)
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach ($question->tags as $tag)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Question Text -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Question:</h3>
                        <div class="bg-gray-50  p-4 rounded-lg">
                            <div class="prose">{!! $question->question_text !!}
                            </div>
                        </div>
                    </div>

                    <!-- Question Images -->
                    @if ($question->images->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Images:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($question->images as $image)
                                        <img src="{{ $image->getUrl() }}"
                                            alt="question image"
                                            class="w-full rounded-xl h-48 object-contain mb-3 cursor-pointer"
                                            onclick="openImageModal('{{ $image->getUrl() }}')">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- MCQ Options -->
                    @if ($question->isMCQ() && $question->options->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Answer Options:</h3>
                            <div class="space-y-2">
                                @php $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp
                                @foreach ($question->options as $index => $option)
                                    <div
                                        class="flex justify-between items-center p-3 border rounded-lg {{ $option->is_correct ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                        <div class="flex gap-4 items-center">
                                            <div
                                                class="w-8 h-8 flex items-center justify-center rounded-full {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $optionLabels[$index] ?? chr(65 + $index) }}
                                            </div>
                                            <div class="flex flex-col gap-2 justify-center" >
                                                <div class="prose flex-1 text-gray-800">{!! $option->option_text !!}</div>
                                                @if($option->image_path)
                                                    <img src="{{ Storage::url($option->image_path) }}"
                                                        alt="option_image"
                                                        class="w-full rounded-xl h-48 object-contain cursor-pointer"
                                                        onclick="openImageModal('{{ Storage::url($option->image_path) }}', 'option_image')">
                                                @endif
                                            </div>
                                        </div>
                                        @if ($option->is_correct)
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Explanation -->
                    @if ($question->explanation)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Explanation:</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="prose">
                                    {!! $question->explanation !!}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Question Meta -->
                    <div class="border-t p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                            <div>
                                <p><span class="font-medium">Created by:</span> {{ $question->creator->name }}</p>
                                <p><span class="font-medium">Created:</span>
                                    {{ $question->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <p><span class="font-medium">Last updated:</span>
                                    {{ $question->updated_at->format('F j, Y g:i A') }}</p>
                                <p><span class="font-medium">Question ID:</span> #{{ $question->id }}</p>
                            </div>
                        </div>
                    </div>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
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
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
