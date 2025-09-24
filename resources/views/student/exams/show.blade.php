<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('student.exams.index') }}"
               class="text-blue-600 hover:text-blue-800 mr-4">← Back to My Exams</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $exam->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Messages -->
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

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    {{ session('info') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Active Attempt Alert -->
            @if($activeAttempt)
                <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <strong>You have an active attempt!</strong>
                            <p class="text-sm mt-1">
                                Started {{ $activeAttempt->started_at->diffForHumans() }},
                                expires {{ $activeAttempt->expires_at->format('M j, Y g:i A') }}
                            </p>
                        </div>
                        <a href="{{ route('student.attempts.take', $activeAttempt) }}"
                           class="bg-orange-500 hover:bg-orange-700 text-white px-4 py-2 rounded font-medium">
                            Continue Exam
                        </a>
                    </div>
                </div>
            @endif

            <!-- Exam Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        @if($exam->isActive())
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
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

                    @if($exam->description)
                        <p class="text-gray-600 mb-4">{{ $exam->description }}</p>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-xl font-bold text-blue-600">{{ $exam->examQuestions->count() }}</div>
                            <div class="text-sm text-blue-600">Questions</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-xl font-bold text-green-600">{{ $exam->total_marks }}</div>
                            <div class="text-sm text-green-600">Total Marks</div>
                        </div>
                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                            <div class="text-xl font-bold text-purple-600">{{ $exam->getDurationFormatted() }}</div>
                            <div class="text-sm text-purple-600">Duration</div>
                        </div>
                        <div class="text-center p-3 bg-indigo-50 rounded-lg">
                            <div class="text-xl font-bold text-indigo-600">{{ $exam->max_attempts }}</div>
                            <div class="text-sm text-indigo-600">Max Attempts</div>
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
                                <span class="font-medium">Your Attempts:</span>
                                <span>{{ $attempts->count() }} / {{ $exam->max_attempts }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            @if($exam->instructions)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Instructions</h3>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $exam->instructions }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Start Exam Section -->
            @if($exam->isActive() && $canStartNewAttempt && !$activeAttempt)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Start New Attempt</h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <h4 class="font-medium text-green-800 mb-2">Ready to begin?</h4>
                            <ul class="text-green-700 text-sm space-y-1">
                                <li>• Make sure you have a stable internet connection</li>
                                <li>• You have {{ $exam->getDurationFormatted() }} to complete this exam</li>
                                <li>• Your progress will be saved automatically</li>
                                @if($exam->max_attempts > 1)
                                    <li>• You can attempt this exam {{ $exam->max_attempts - $attempts->count() }} more time(s)</li>
                                @endif
                            </ul>
                        </div>

                        <form action="{{ route('student.exams.start', $exam) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to start the exam? The timer will begin immediately.')">
                            @csrf
                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="confirm_start" name="confirm_start" value="1"
                                       class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                       required>
                                <label for="confirm_start" class="ml-2 text-sm text-gray-700">
                                    I understand the exam rules and I'm ready to start
                                </label>
                            </div>
                            <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                                Start Exam Now
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Cannot Start Exam Messages -->
            @if(!$exam->isActive() || !$canStartNewAttempt || $activeAttempt)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        @if(!$exam->isActive())
                            @if($exam->isUpcoming())
                                <div class="text-center py-6">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Exam Not Started Yet</h3>
                                    <p class="text-gray-600">
                                        This exam will be available on {{ $exam->start_time->format('M j, Y \a\t g:i A') }}
                                        <br>
                                        <span class="text-sm text-blue-600">({{ $exam->start_time->diffForHumans() }})</span>
                                    </p>
                                </div>
                            @elseif($exam->isPast())
                                <div class="text-center py-6">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Exam Has Ended</h3>
                                    <p class="text-gray-600">
                                        This exam ended on {{ $exam->end_time->format('M j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            @endif
                        @elseif(!$canStartNewAttempt)
                            <div class="text-center py-6">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Maximum Attempts Reached</h3>
                                <p class="text-gray-600">
                                    You have used all {{ $exam->max_attempts }} allowed attempts for this exam.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Previous Attempts -->
            @if($attempts->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Attempts</h3>
                        <div class="space-y-4">
                            @foreach($attempts as $attempt)
                                <div class="border border-gray-200 rounded-lg p-4 {{ $attempt->isInProgress() ? 'border-orange-300 bg-orange-50' : '' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-medium">Attempt {{ $attempt->attempt_number }}</span>
                                                @if($attempt->isInProgress())
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        {{ $attempt->getRemainingTimeFormatted() }} left
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-sm text-gray-600 space-y-1">
                                                <div>
                                                    <span class="font-medium">Started:</span> {{ $attempt->started_at->format('M j, Y g:i A') }}
                                                    <span class="text-gray-400">({{ $attempt->started_at->diffForHumans() }})</span>
                                                </div>
                                                @if($attempt->submitted_at)
                                                    <div>
                                                        <span class="font-medium">Submitted:</span> {{ $attempt->submitted_at->format('M j, Y g:i A') }}
                                                        <span class="text-gray-400">({{ $attempt->submitted_at->diffForHumans() }})</span>
                                                    </div>
                                                @endif
                                                @if($attempt->isInProgress())
                                                    <div class="text-orange-600 font-medium">
                                                        <span class="font-medium">Expires:</span> {{ $attempt->expires_at->format('M j, Y g:i A') }}
                                                    </div>
                                                    <div class="text-blue-600">
                                                        <span class="font-medium">Progress:</span> {{ $attempt->getProgressPercentage() }}% complete
                                                    </div>
                                                @endif
                                                @if(($attempt->total_score !== null) && $attempt->exam->results_released)
                                                    <div class="text-green-600 font-medium">
                                                        <span class="font-medium">Score:</span> {{ $attempt->total_score }}/{{ $exam->total_marks }}
                                                        ({{ round($attempt->percentage_score, 1) }}%)
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="ml-4 flex flex-col gap-2">
                                            @if($attempt->isInProgress())
                                                <a href="{{ route('student.attempts.take', $attempt) }}"
                                                   class="bg-orange-500 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm font-medium text-center">
                                                    Continue Exam
                                                </a>
                                            @elseif($attempt->exam->results_released)
                                                <a href="{{ route('student.attempts.results', $attempt) }}"
                                                   class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium text-center">
                                                    View Results
                                                </a>
                                            @else
                                                <span class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm text-center">
                                                    Pending Review
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- No Attempts Yet -->
            @if($attempts->count() == 0 && !$exam->isActive())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No attempts yet</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($exam->isUpcoming())
                                    You can start this exam when it becomes available.
                                @elseif($exam->isPast())
                                    This exam has ended and you didn't make any attempts.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
