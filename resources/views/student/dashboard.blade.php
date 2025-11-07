<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Examinee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ Auth::user()->name }}!</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h4 class="font-medium text-green-800">Available Exams</h4>
                            <p class="text-green-600 text-sm mt-2">Take your assigned exams</p>
                            <div class="mt-3 space-y-1">
                                @php
                                    $activeExams = Auth::user()
                                        ->assignedExams()
                                        ->where('status', 'published')
                                        ->where('start_time', '<=', now())
                                        ->where('end_time', '>=', now())
                                        ->count();
                                    $upcomingExams = Auth::user()
                                        ->assignedExams()
                                        ->where('status', 'published')
                                        ->where('start_time', '>', now())
                                        ->count();
                                @endphp
                                <p class="text-sm text-green-700">Active Now: {{ $activeExams }}</p>
                                <p class="text-sm text-green-700">Upcoming: {{ $upcomingExams }}</p>
                            </div>
                            <a href="{{ route('student.exams.index') }}"
                                class="inline-block mt-3 text-green-600 hover:text-green-800 font-medium">
                                View My Exams →
                            </a>
                        </div>

                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h4 class="font-medium text-blue-800">Exam History</h4>
                            <p class="text-blue-600 text-sm mt-2">View your exam results</p>
                            <div class="mt-3 space-y-1">
                                @php
                                    $totalAttempts = Auth::user()->examAttempts()->count();
                                    $completedAttempts = Auth::user()
                                        ->examAttempts()
                                        ->whereIn('status', ['submitted', 'graded'])
                                        ->count();
                                    $avgScore = Auth::user()
                                        ->examAttempts()
                                        ->whereNotNull('percentage_score')
                                        ->avg('percentage_score');
                                @endphp
                                <p class="text-sm text-blue-700">Total Attempts: {{ $totalAttempts }}</p>
                                <p class="text-sm text-blue-700">Completed: {{ $completedAttempts }}</p>
                                @if ($avgScore)
                                    <p class="text-sm text-blue-700">Average Score: {{ round($avgScore, 1) }}%</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h4>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('student.exams.index') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                View Available Exams
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
