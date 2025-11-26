<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex gap-2 justify-between items-center">
                <a href="{{ route('teacher.results.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                    Results</a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $exam->title }}
                    </h2>
                    <p class="mt-1 text-gray-600">
                        {{ $attempts->count() }} attempts •
                        Created {{ $exam->created_at->diffForHumans() }}
                        @if ($exam->results_released)
                            • Results released {{ $exam->results_released_at?->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if ($exam->results_released)
                        <form action="{{ route('teacher.results.hide', $exam) }}" method="POST" class="inline" data-confirm="Hide results from Examinee?">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                Hide Results
                            </button>
                        </form>
                        <div class="relative inline-block text-left">
                            <button id="exportBtn"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                Export
                                <svg class="ml-2 -mr-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div id="exportDropdown"
                                class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="{{ route('teacher.results.export', ['exam' => $exam, 'format' => 'csv']) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Export</a>
                                    <a href="{{ route('teacher.results.export', ['exam' => $exam, 'format' => 'xlsx']) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel Export</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <button id="releaseResultsBtn"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-700 transition-colors"
                            data-exam-id="{{ $exam->id }}"
                            data-exam-title="{{ $exam->title }}"
                            data-attempts-count="{{ $attempts->count() }}"
                            data-graded-count="{{ $attempts->where('status', 'graded')->count() }}"
                            {{ $attempts->where('status', 'submitted')->count() > 0 ? 'data-partial=true' : '' }}>
                            Release Results
                        </button>
                        <div class="relative inline-block text-left">
                            <button id="exportBtn"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                Export
                                <svg class="ml-2 -mr-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div id="exportDropdown"
                                class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="{{ route('teacher.results.export', ['exam' => $exam, 'format' => 'csv']) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Export</a>
                                    <a href="{{ route('teacher.results.export', ['exam' => $exam, 'format' => 'xlsx']) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel Export</a>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="text-purple-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target-icon lucide-target"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Attempts</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $attempts->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Average Score</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $statistics['average_score'] ?? '-' }}
                                @if (isset($statistics['max_possible_score']))
                                    <span class="text-lg text-gray-500">/{{ $statistics['max_possible_score'] }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Highest Score</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $statistics['highest_score'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Lowest Score</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $statistics['lowest_score'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution Chart -->
            @if (isset($statistics['grade_distribution']) && count($statistics['grade_distribution']) > 0)
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Grade Distribution</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="gradeDistributionChart" height="200"></canvas>
                    </div>
                </div>
            @endif


            <!-- Search and Filter Form -->
            <div class="bg-white rounded-lg shadow mb-8 p-6">
                <form action="{{ route('teacher.results.show', $exam) }}" method="GET">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                        <!-- Search on the left -->
                        <div class="w-full md:w-1/4 lg:w-full"> {{-- Adjusted width for search input --}}
                            <label for="search" class="block text-sm font-medium text-gray-700">Search Examinee</label>
                            <input type="text" name="search" id="search"
                                value="{{ $filters['search'] ?? '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Name or email...">
                        </div>

                        <!-- Filters and buttons on the right -->
                        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-4 md:gap-2"> {{-- Added sm:flex-wrap for better wrapping on smaller wide screens --}}
                            <div class="w-full sm:w-auto">
                                <label for="grade" class="block text-sm font-medium text-gray-700">Grade</label>
                                <select name="grade" id="grade"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">All Grades</option>
                                    <option value="A" @selected(isset($filters['grade']) && $filters['grade'] == 'A')>A</option>
                                    <option value="B" @selected(isset($filters['grade']) && $filters['grade'] == 'B')>B</option>
                                    <option value="C" @selected(isset($filters['grade']) && $filters['grade'] == 'C')>C</option>
                                    <option value="D" @selected(isset($filters['grade']) && $filters['grade'] == 'D')>D</option>
                                    <option value="F" @selected(isset($filters['grade']) && $filters['grade'] == 'F')>F</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-auto">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">All Statuses</option>
                                    <option value="graded" @selected(isset($filters['status']) && $filters['status'] == 'graded')>Graded
                                    </option>
                                    <option value="submitted" @selected(isset($filters['status']) && $filters['status'] == 'submitted')>
                                        Submitted</option>
                                    <option value="in_progress" @selected(isset($filters['status']) && $filters['status'] == 'in_progress')>In
                                        Progress</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-auto">
                                <label for="suspicious"
                                    class="block text-sm font-medium text-gray-700">Suspicious</label>
                                <select name="suspicious" id="suspicious"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Any</option>
                                    <option value="none" @selected(isset($filters['suspicious']) && $filters['suspicious'] == 'none')>None</option>
                                    <option value="1-10" @selected(isset($filters['suspicious']) && $filters['suspicious'] == '1-10')>1-10</option>
                                    <option value="10+" @selected(isset($filters['suspicious']) && $filters['suspicious'] == '10+')>10+</option>
                                </select>
                            </div>
                            <div class="flex gap-2 w-full sm:w-auto"> {{-- Grouping buttons --}}
                                <button type="submit"
                                    class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    Filter
                                </button>
                                <a href="{{ route('teacher.results.show', $exam) }}"
                                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 flex items-center justify-center">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Examinee Results</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="resultsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Examinee</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Attempt Number</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Percentage</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Grade</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($attempts as $attempt)
                                @php
                                    $maxScore = $statistics['max_possible_score'] ?? 100;
                                    $percentage =
                                        $maxScore > 0 && $attempt->total_score
                                            ? ($attempt->total_score / $maxScore) * 100
                                            : 0;
                                    $letterGrade =
                                        $percentage >= 90
                                            ? 'A'
                                            : ($percentage >= 80
                                                ? 'B'
                                                : ($percentage >= 70
                                                    ? 'C'
                                                    : ($percentage >= 60
                                                        ? 'D'
                                                        : 'F')));
                                @endphp
                                <tr class="hover:bg-gray-50" data-attempt-id="{{ $attempt->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($attempt->student->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $attempt->student->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $attempt->student->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $attempt->attempt_number }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($attempt->status !== 'graded')
                                                -
                                            @else
                                                <span
                                                    class="ml-2 text-sm text-gray-500">{{ $attempt->total_score }}/{{ $maxScore }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($attempt->status !== 'graded')
                                            -
                                        @else
                                            <span
                                                class="text-sm text-gray-900 percentage-display">{{ number_format($percentage, 1) }}%</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($attempt->status !== 'graded')
                                            -
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs rounded-full grade-badge
                                            {{ $letterGrade === 'A'
                                                ? 'bg-green-100 text-green-800'
                                                : ($letterGrade === 'B'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : ($letterGrade === 'C'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : ($letterGrade === 'D'
                                                            ? 'bg-orange-100 text-orange-800'
                                                            : 'bg-red-100 text-red-800'))) }}">
                                                {{ $letterGrade }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = $attempt->status;
                                            $classes = match ($status) {
                                                'graded'    => 'bg-green-100 text-green-800',
                                                'submitted' => 'bg-blue-100 text-blue-800',
                                                default     => 'bg-red-100 text-red-800',
                                            };
                                        @endphp

                                        <span class="px-2 py-1 text-xs rounded-full grade-badge {{ $classes }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            @if ($attempt->status === 'in_progress')
                                                <form action="{{ route('teacher.results.submit', $attempt) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                        Submit Exam
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('teacher.results.review', $attempt) }}"
                                                        class="text-blue-600 hover:text-blue-900">Review</a>
                                                </div>
                                            @endif

                                            @php
                                                $suspicious = json_decode($attempt->suspicious_behaviours, true) ?? [];
                                            @endphp

                                            <div class="relative">
                                                <button
                                                    onclick='showSuspiciousModal(@json($suspicious))'
                                                    class="text-red-500 hover:text-red-600">
                                                    Suspicious Behaviours
                                                </button>
                                                <div class="size-5 flex justify-center items-center bg-red-300 text-white rounded-full absolute -top-2 -right-4 text-xs">
                                                    {{ count($suspicious) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="suspiciousModal" class="fixed hidden inset-0 bg-gray-600 bg-opacity-50 z-50">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg p-6 w-full max-w-5xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Suspicious Behaviours</h3>
                    <div class="overflow-y-auto h-[60vh] mb-4">
                         <ol id="suspiciousList" class="list-decimal list-inside text-gray-700 space-y-1"></ol>
                    </div>
                    <div class="flex justify-end">
                        <x-secondary-button onclick="closeSuspiciousModal()">Cancel</x-secondary-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Notes Modal -->
        <div id="notesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Committee Notes</h3>
                    <form id="notesForm">
                        <input type="hidden" id="notesAttemptId">
                        <div class="mb-4">
                            <textarea id="notesText" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Add notes about this attempt..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelNotes"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Save Notes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Release Results Modal -->
        <div id="releaseResultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Release Results</h3>
                    <form id="releaseResultsForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">You are about to release results for:</p>
                            <p class="font-medium text-gray-900" id="examTitle"></p>
                            <p class="text-sm text-gray-500" id="attemptsSummary"></p>
                        </div>

                        <div id="partialGradingWarning"
                            class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg hidden">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-orange-800">Some answers are still ungraded. Examinee will
                                    see partial results.</span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelRelease"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                Release Results
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let selectedAttempts = [];
                const maxScore = {{ $statistics['max_possible_score'] ?? 100 }};

                // Initialize grade distribution chart
                @if (isset($statistics['grade_distribution']) && count($statistics['grade_distribution']) > 0)
                    const ctx = document.getElementById('gradeDistributionChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($statistics['grade_distribution'])) !!},
                            datasets: [{
                                label: 'Number of Examinee',
                                data: {!! json_encode(array_values($statistics['grade_distribution'])) !!},
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                @endif

                // Export dropdown
                $('#exportBtn').on('click', function(e) {
                    e.stopPropagation();
                    $('#exportDropdown').toggleClass('hidden');
                });

                $(document).on('click', function() {
                    $('#exportDropdown').addClass('hidden');
                });

                // Select all functionality
                $('#selectAll').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('.attempt-checkbox').prop('checked', isChecked).trigger('change');
                });

                // Individual checkbox change
                $('.attempt-checkbox').on('change', function() {
                    const attemptId = $(this).data('attempt-id');
                    if ($(this).is(':checked')) {
                        if (!selectedAttempts.includes(attemptId)) {
                            selectedAttempts.push(attemptId);
                        }
                    } else {
                        selectedAttempts = selectedAttempts.filter(id => id !== attemptId);
                    }
                    updateBulkUpdateButton();
                });

                // Score input changes
                $('.score-input').on('input', function() {
                    const score = parseFloat($(this).val()) || 0;
                    const percentage = maxScore > 0 ? (score / maxScore) * 100 : 0;
                    const letterGrade = percentage >= 90 ? 'A' : (percentage >= 80 ? 'B' : (percentage >= 70 ?
                        'C' : (percentage >= 60 ? 'D' : 'F')));

                    const row = $(this).closest('tr');
                    row.find('.percentage-display').text(percentage.toFixed(1) + '%');

                    // Update grade badge
                    const gradeBadge = row.find('.grade-badge');
                    gradeBadge.removeClass(
                        'bg-green-100 text-green-800 bg-blue-100 text-blue-800 bg-yellow-100 text-yellow-800 bg-orange-100 text-orange-800 bg-red-100 text-red-800'
                        );

                    const gradeClass = letterGrade === 'A' ? 'bg-green-100 text-green-800' :
                        (letterGrade === 'B' ? 'bg-blue-100 text-blue-800' :
                            (letterGrade === 'C' ? 'bg-yellow-100 text-yellow-800' :
                                (letterGrade === 'D' ? 'bg-orange-100 text-orange-800' :
                                    'bg-red-100 text-red-800')));

                    gradeBadge.addClass(gradeClass).text(letterGrade);
                });

                // Update individual score
                $('.update-score-btn').on('click', function() {
                    const attemptId = $(this).data('attempt-id');
                    const score = $(this).closest('tr').find('.score-input').val();

                    updateScore(attemptId, score, $(this));
                });

                // Teacher notes
                $('.notes-btn').on('click', function() {
                    const attemptId = $(this).data('attempt-id');
                    const notes = $(this).data('notes') || '';

                    $('#notesAttemptId').val(attemptId);
                    $('#notesText').val(notes);
                    $('#notesModal').removeClass('hidden');
                });

                $('#cancelNotes').on('click', function() {
                    $('#notesModal').addClass('hidden');
                });

                $('#notesForm').on('submit', function(e) {
                    e.preventDefault();

                    const attemptId = $('#notesAttemptId').val();
                    const notes = $('#notesText').val();

                    $.ajax({
                        url: `/teacher/results/attempts/${attemptId}/update-score`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            committee_notes: notes
                        },
                        success: function() {
                            showNotification('Notes saved successfully', 'success');
                            $('#notesModal').addClass('hidden');

                            // Update the data attribute
                            $(`.notes-btn[data-attempt-id="${attemptId}"]`).data('notes', notes);
                        },
                        error: function() {
                            showNotification('Error saving notes', 'error');
                        }
                    });
                });

                // Recalculate all scores
                $('#recalculateBtn').on('click', function() {
                    if (!confirm(
                            'Recalculate all scores? This will update all attempt totals based on individual answer grades.'
                            )) {
                        return;
                    }

                    const btn = $(this);
                    const originalText = btn.text();
                    btn.prop('disabled', true).text('Recalculating...');

                    $.ajax({
                        url: `/teacher/results/{{ $exam->id }}/recalculate`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            showNotification(`Recalculated ${response.updated_count || 0} scores`,
                                'success');
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function() {
                            showNotification('Error recalculating scores', 'error');
                        },
                        complete: function() {
                            btn.prop('disabled', false).text(originalText);
                        }
                    });
                });

                // Helper functions
                function updateScore(attemptId, score, button) {
                    const originalText = button.text();
                    button.prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/teacher/results/attempts/${attemptId}/update-score`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            total_score: score
                        },
                        success: function(response) {
                            showNotification('Score updated successfully', 'success');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors || {};
                            let errorMsg = 'Error updating score';

                            if (errors.total_score) {
                                errorMsg = errors.total_score[0];
                            }

                            showNotification(errorMsg, 'error');
                        },
                        complete: function() {
                            button.prop('disabled', false).text(originalText);
                        }
                    });
                }

                function updateBulkUpdateButton() {
                    $('#bulkUpdateBtn').prop('disabled', selectedAttempts.length === 0);
                }

                function showNotification(message, type) {
                    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                    const notification = $(`
                    <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50">
                        ${message}
                    </div>
                `);

                    $('body').append(notification);
                    setTimeout(() => notification.fadeOut(() => notification.remove()), 3000);
                }

                // Release results modal
                $('#releaseResultsBtn').on('click', function() {
                    const examId = $(this).data('exam-id');
                    const examTitle = $(this).data('exam-title');
                    const attemptsCount = $(this).data('attempts-count');
                    const gradedCount = $(this).data('graded-count');
                    const isPartial = $(this).data('partial');

                    $('#examTitle').text(examTitle);
                    $('#attemptsSummary').text(`${attemptsCount} attempts, ${gradedCount} fully graded`);

                    // Set form action
                    $('#releaseResultsForm').attr('action', `/teacher/results/${examId}/release`);

                    // Show/hide partial grading warning
                    if (isPartial) {
                        $('#partialGradingWarning').removeClass('hidden');
                    } else {
                        $('#partialGradingWarning').addClass('hidden');
                    }

                    $('#releaseResultsModal').removeClass('hidden');
                });

                $('#cancelRelease').on('click', function() {
                    $('#releaseResultsModal').addClass('hidden');
                });

                // Release form submission
                $('#releaseResultsForm').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const submitBtn = form.find('button[type="submit"]');
                    const originalText = submitBtn.text();

                    submitBtn.prop('disabled', true).text('Releasing...');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            showToast({
                                message: "Results released successfully",
                                type: "success"
                            })
                            $('#releaseResultsModal').addClass('hidden');
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors || {};
                            let errorMsg = 'Error releasing results';

                            if (errors.exam) {
                                errorMsg = errors.exam[0];
                            } else if (errors.partial_release) {
                                errorMsg = errors.partial_release[0];
                            }

                            showToast({
                                message: errorMsg,
                                type: "error"
                            })
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    });
                });

                function showNotification(message, type) {
                    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                    const notification = $(`
                    <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50">
                        ${message}
                    </div>
                `);

                    $('body').append(notification);
                    setTimeout(() => notification.fadeOut(() => notification.remove()), 3000);
                }

            });

            function showSuspiciousModal(behaviours) {
                const modal = document.getElementById('suspiciousModal');
                const list = document.getElementById('suspiciousList');

                // Clear old items
                list.innerHTML = '';

                if (behaviours.length) {
                    // Add each behaviour as <li>
                    behaviours.forEach(b => {
                        const li = document.createElement('li');
                        li.textContent = b;
                        list.appendChild(li);
                    });
                }

                modal.classList.remove('hidden');
            }

            function closeSuspiciousModal() {
                document.getElementById('suspiciousModal').classList.add('hidden');
            }
        </script>
</x-app-layout>
