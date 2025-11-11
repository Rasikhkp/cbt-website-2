<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Results Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($exams->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Exams Found</h3>
                    <p class="text-gray-500 mb-4">You haven't created any exams yet.</p>
                    <a href="{{ route('teacher.exams.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Create Your First Exam
                    </a>
                </div>
            @else
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    @php
                        $totalExams = $exams->count();
                        $releasedExams = $exams->where('results_released', true)->count();
                        $totalAttempts = $exams->sum('total_attempts');
                        $gradedAttempts = $exams->sum('graded_attempts');
                    @endphp

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-notepad-text-icon lucide-notepad-text"><path d="M8 2v4"/><path d="M12 2v4"/><path d="M16 2v4"/><rect width="16" height="18" x="4" y="4" rx="2"/><path d="M8 10h6"/><path d="M8 14h8"/><path d="M8 18h5"/></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Exams</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalExams }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Results Released</p>
                                <p class="text-2xl font-bold text-green-600">{{ $releasedExams }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="text-purple-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target-icon lucide-target"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Attempts</p>
                                <p class="text-2xl font-bold text-purple-600">{{ number_format($totalAttempts) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-orange-100 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Graded Attempts</p>
                                <p class="text-2xl font-bold text-orange-600">{{ number_format($gradedAttempts) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exams Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Your Exams</h2>
                            <div class="flex items-center space-x-4">
                                <select id="statusFilter" class="px-3 py-1 border border-gray-300 rounded-md text-sm">
                                    <option value="all">All Exams</option>
                                    <option value="released">Released</option>
                                    <option value="unreleased">Unreleased</option>
                                    <option value="graded">Fully Graded</option>
                                    <option value="pending">Pending Grading</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Attempts</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Grading Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Results Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($exams as $exam)
                                    @php
                                        $isFullyGraded =
                                            $exam->graded_attempts == $exam->total_attempts &&
                                            $exam->total_attempts > 0;
                                        $gradingProgress =
                                            $exam->total_attempts > 0
                                                ? ($exam->graded_attempts / $exam->total_attempts) * 100
                                                : 0;
                                    @endphp

                                    <tr class="hover:bg-gray-50 exam-row"
                                        data-status="{{ $exam->results_released ? 'released' : 'unreleased' }}"
                                        data-grading="{{ $isFullyGraded ? 'graded' : 'pending' }}">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                                                <div class="text-sm text-gray-500">
                                                    Created {{ $exam->created_at->diffForHumans() }}
                                                    @if ($exam->end_time)
                                                        • Ended {{ $exam->end_time->diffForHumans() }}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $exam->total_attempts }} total</div>
                                            <div class="text-sm text-gray-500">{{ $exam->graded_attempts }} graded
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                        style="width: {{ $gradingProgress }}%"></div>
                                                </div>
                                                <span
                                                    class="text-sm text-gray-600">{{ number_format($gradingProgress, 1) }}%</span>
                                            </div>
                                            @if (!$isFullyGraded && $exam->total_attempts > 0)
                                                <div class="text-xs text-orange-600 mt-1">
                                                    {{ $exam->total_attempts - $exam->graded_attempts }} pending</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($exam->results_released)
                                                <div class="flex items-center">
                                                    <span
                                                        class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Released</span>
                                                    @if ($exam->results_released_at)
                                                        <span
                                                            class="ml-2 text-xs text-gray-500">{{ $exam->results_released_at->format('M j') }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Unreleased</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('teacher.results.show', $exam) }}"
                                                    class="text-blue-600 hover:text-blue-900">View</a>

                                                @if (!$exam->results_released && $exam->total_attempts > 0)
                                                    <button
                                                        class="text-green-600 hover:text-green-900 release-results-btn"
                                                        data-exam-id="{{ $exam->id }}"
                                                        data-exam-title="{{ $exam->title }}"
                                                        data-attempts-count="{{ $exam->total_attempts }}"
                                                        data-graded-count="{{ $exam->graded_attempts }}"
                                                        {{ !$isFullyGraded ? 'data-partial=true' : '' }}>
                                                        Release
                                                    </button>
                                                @endif

                                                @if ($exam->results_released)
                                                    <form action="{{ route('teacher.results.hide', $exam) }}"
                                                        method="POST" class="inline" data-confirm="Hide results from examinees?">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-orange-600 hover:text-orange-900">
                                                            Hide
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($exam->total_attempts > 0)
                                                    <a href="{{ route('teacher.results.export', ['exam' => $exam, 'format' => 'xlsx']) }}"
                                                        class="text-gray-600 hover:text-gray-900">Export</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
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

                    <div id="partialGradingWarning" class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg hidden">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-orange-800">Some answers are still ungraded. Examinees will see partial results.</span>
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
            // Filter functionality
            $('#statusFilter').on('change', function() {
                const filter = $(this).val();
                $('.exam-row').each(function() {
                    const status = $(this).data('status');
                    const grading = $(this).data('grading');

                    let show = false;
                    switch(filter) {
                        case 'all':
                            show = true;
                            break;
                        case 'released':
                            show = status === 'released';
                            break;
                        case 'unreleased':
                            show = status === 'unreleased';
                            break;
                        case 'graded':
                            show = grading === 'graded';
                            break;
                        case 'pending':
                            show = grading === 'pending';
                            break;
                    }

                    if (show) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Release results modal
            $('.release-results-btn').on('click', function() {
                const examId = $(this).data('exam-id');
                const examTitle = $(this).data('exam-title');
                const attemptsCount = $(this).data('attempts-count');
                const gradedCount = $(this).data('graded-count');
                const isPartial = $(this).data('partial');

                console.log('isPartial', isPartial)

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
                          title: "Success!",
                          message: "Results released successfully!",
                          type: "success"
                        });
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
                          title: "Error!",
                          message: errorMsg,
                          type: "error"
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Show notification
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

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Escape to close modal
                if (e.key === 'Escape') {
                    $('#releaseResultsModal').addClass('hidden');
                }
            });
        });
    </script>
</x-app-layout>
