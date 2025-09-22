<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Grading Queue') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($examsNeedingGrading->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">All Caught Up!</h3>
                    <p class="text-gray-500">You have no exams waiting for grading at the moment.</p>
                    <a href="{{ route('teacher.exams.index') }}"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        View All Exams
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @foreach ($examsNeedingGrading as $exam)
                            @php
                                $totalAttempts = $exam->attempts->count();
                                $totalGradedAttempts = $exam->attempts->where('status', 'graded')->count();
                            @endphp

                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $exam->title }}</h3>
                                        </div>

                                        <div class="mt-2 text-sm text-gray-600">
                                            <span>{{ $totalAttempts }} total attempts</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $totalGradedAttempts }} graded attempts</span>
                                            <span class="mx-2">•</span>
                                            <span>Created {{ $exam->created_at->diffForHumans() }}</span>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            @php
                                                $progressPercent =
                                                    $totalAttempts > 0
                                                        ? ($totalGradedAttempts / $totalAttempts) * 100
                                                        : 0;
                                            @endphp

                                            <div class="flex items-center space-x-3">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                                        style="width: {{ $progressPercent }}%">
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-500 min-w-0">
                                                    {{ number_format($progressPercent, 1) }}% complete
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3 ml-6">
                                        <!-- Grade Button -->
                                        <a href="{{ route('teacher.grading.exam', $exam) }}"
                                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 transition-colors">
                                            Start Grading
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
