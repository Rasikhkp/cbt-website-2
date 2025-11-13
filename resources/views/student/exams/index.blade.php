<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Active Exams -->
            @if ($activeExams->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-semibold mr-2">
                                Active Now
                            </span>
                            Available Exams ({{ $activeExams->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach ($activeExams as $exam)
                                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-medium text-gray-900">{{ $exam->title }}</h4>
                                        <div>
                                            <a href="{{ route('student.exams.show', $exam) }}"
                                                class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                    @if ($exam->description)
                                        <p class="text-gray-600 text-sm mt-1">
                                            {{ Str::limit($exam->description, 100) }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>{{ $exam->examQuestions->count() }} questions</span>
                                        <span>{{ $exam->total_marks }} marks</span>
                                        <span>{{ $exam->getDurationFormatted() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Ends:</span>
                                        {{ $exam->end_time->format('M j, Y g:i A') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upcoming Exams -->
            @if ($upcomingExams->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold mr-2">
                                Upcoming
                            </span>
                            Scheduled Exams ({{ $upcomingExams->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach ($upcomingExams as $exam)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $exam->title }}</h4>
                                            @if ($exam->description)
                                                <p class="text-gray-600 text-sm mt-1">
                                                    {{ Str::limit($exam->description, 100) }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                                <span>{{ $exam->examQuestions->count() }} questions</span>
                                                <span>{{ $exam->total_marks }} marks</span>
                                                <span>{{ $exam->getDurationFormatted() }}</span>
                                            </div>
                                            <div class="text-sm text-blue-600 mt-1">
                                                <span class="font-medium">Starts:</span>
                                                {{ $exam->start_time->format('M j, Y g:i A') }}
                                                <span
                                                    class="text-gray-500">({{ $exam->start_time->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('student.exams.show', $exam) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Past Exams -->
            @if ($pastExams->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm font-semibold mr-2">
                                Completed
                            </span>
                            Past Exams ({{ $pastExams->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach ($pastExams as $exam)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $exam->title }}</h4>
                                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                                <span>{{ $exam->examQuestions->count() }} questions</span>
                                                <span>{{ $exam->total_marks }} marks</span>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                <span class="font-medium">Ended:</span>
                                                {{ $exam->end_time->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('student.exams.show', $exam) }}"
                                                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- No Exams -->
            @if ($activeExams->count() == 0 && $upcomingExams->count() == 0 && $pastExams->count() == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No exams assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">Your committee hasn't assigned any exams to you yet.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
