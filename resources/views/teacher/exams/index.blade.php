<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exam Management') }}
            </h2>
            <a href="{{ route('teacher.exams.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Exam
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('teacher.exams.index') }}"
                        class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-64">
                            <label class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search exams..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status"
                                class="mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Published</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>
                                    Archived</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Filter
                            </button>
                            <a href="{{ route('teacher.exams.index') }}"
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

            <!-- Exams List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @forelse ($exams as $exam)
                        <div class="border-b border-gray-200 pb-6 mb-6 last:border-b-0 last:pb-0 last:mb-0">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $exam->getStatusColorClass() }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                        @if ($exam->isActive())
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                Active Now
                                            </span>
                                        @elseif($exam->isUpcoming())
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Upcoming
                                            </span>
                                        @elseif($exam->isPast())
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Past
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $exam->title }}</h3>

                                    @if ($exam->description)
                                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($exam->description, 100) }}
                                        </p>
                                    @endif


                                    <div class="flex flex-wrap gap-3 text-sm text-gray-600 mt-2">
                                        <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded-md">
                                            <span class="font-medium">Questions:</span>
                                            {{ $exam->examQuestions->count() }}
                                        </div>
                                        <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded-md">
                                            <span class="font-medium">Total Marks:</span>
                                            {{ $exam->total_marks }}
                                        </div>
                                        <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded-md">
                                            <span class="font-medium">Duration:</span>
                                            {{ $exam->getDurationFormatted() }}
                                        </div>
                                        <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded-md">
                                            <span class="font-medium">Examinees:</span>
                                            {{ $exam->assignedStudents->count() }}
                                        </div>
                                    </div>


                                    <div class="mt-2 text-sm text-gray-500">
                                        <div class="flex gap-2">
                                            <div>
                                                <span class="font-medium">Start:</span>
                                                {{ $exam->start_time->format('M j, Y g:i A') }}
                                            </div>
                                            •
                                            <div>
                                                <span class="font-medium">End:</span>
                                                {{ $exam->end_time->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 ml-4">
                                    <a href="{{ route('teacher.exams.show', $exam) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>

                                    @if (!$exam->isPublished() || ($exam->isPublished() && now()->lt($exam->start_time)))
                                        <a href="{{ route('teacher.exams.edit', $exam) }}"
                                            class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                                    @endif

                                    @if ($exam->isDraft())
                                        <form action="{{ route('teacher.exams.publish', $exam) }}" method="POST"
                                            class="inline-block" data-confirm="Are you sure you want to publish this exam?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 text-sm">
                                                Publish
                                            </button>
                                        </form>
                                    @elseif($exam->isPublished() && now()->lt($exam->start_time))
                                        <form action="{{ route('teacher.exams.unpublish', $exam) }}" method="POST"
                                            class="inline-block" data-confirm="Are you sure you want to unpublish this exam?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-orange-600 hover:text-orange-900 text-sm">
                                                Unpublish
                                            </button>
                                        </form>
                                    @endif

                                    @if ($exam->isDraft() || $exam->isPast())
                                        <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST"
                                            class="inline-block"
                                            data-confirm="Are you sure you want to delete this exam?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('teacher.exams.duplicate', $exam) }}" method="POST"
                                        class="inline-block"
                                        data-confirm="Are you sure you want to duplicate this exam?">
                                        @csrf
                                        <button type="submit"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                            Duplicate
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No exams</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first exam.</p>
                            <div class="mt-6">
                                <a href="{{ route('teacher.exams.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Create New Exam
                                </a>
                            </div>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if ($exams->hasPages())
                        <div class="mt-6">
                            {{ $exams->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
