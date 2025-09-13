<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Question Bank') }}
            </h2>
            <a href="{{ route('teacher.questions.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Question
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('teacher.questions.index') }}"
                        class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-64">
                            <label class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search questions..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type"
                                class="mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Types</option>
                                <option value="mcq" {{ request('type') == 'mcq' ? 'selected' : '' }}>Multiple Choice
                                </option>
                                <option value="short" {{ request('type') == 'short' ? 'selected' : '' }}>Short Answer
                                </option>
                                <option value="long" {{ request('type') == 'long' ? 'selected' : '' }}>Long Answer
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Difficulty</label>
                            <select name="difficulty"
                                class="mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Difficulties</option>
                                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy
                                </option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Filter
                            </button>
                            <a href="{{ route('teacher.questions.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

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

            <!-- Questions List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @forelse ($questions as $question)
                        <div class="border-b border-gray-200 pb-6 mb-6 last:border-b-0 last:pb-0 last:mb-0">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $question->getTypeDisplayName() }}
                                        </span>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->getDifficultyColorClass() }}">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $question->points }} {{ Str::plural('point', $question->points) }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        {{ Str::limit($question->question_text, 100) }}
                                    </h3>
                                    @if ($question->tags && count($question->tags) > 0)
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach ($question->tags as $tag)
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        @if ($question->images_count > 0 || $question->images->count() > 0)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                {{ $question->images->count() }}
                                                {{ Str::plural('image', $question->images->count()) }}
                                            </span>
                                        @endif
                                        @if ($question->isMCQ())
                                            <span>{{ $question->options->count() }} options</span>
                                        @endif
                                        <span>Created {{ $question->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 ml-4">
                                    <a href="{{ route('teacher.questions.show', $question) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                                    <a href="{{ route('teacher.questions.edit', $question) }}"
                                        class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                                    <form action="{{ route('teacher.questions.destroy', $question) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this question?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No questions</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new question.</p>
                            <div class="mt-6">
                                <a href="{{ route('teacher.questions.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Add New Question
                                </a>
                            </div>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if ($questions->hasPages())
                        <div class="mt-6">
                            {{ $questions->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
