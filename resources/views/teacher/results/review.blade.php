<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex gap-2 justify-between items-center">
                <a href="{{ route('teacher.results.show', $attempt->exam->id) }}"
                    class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                    Results: {{ $attempt->exam->title }}</a>

                <div>
                    <h1 class="text-xl font-bold text-gray-900">Examinee: {{ $attempt->student->name }}</h1>
                    <p class="mt-1 text-gray-600">
                        Exam: {{ $attempt->exam->title }} (attempt {{ $attempt->attempt_number }}) •
                        Submitted: {{ $attempt->submitted_at?->format('M j, Y H:i') }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-900 current-score">
                    {{ $attempt->total_score ?? '0.00' }}
                </div>
                <div class="text-sm text-gray-500">Current Score</div>
            </div>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Bar -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                @php
                    $gradedCount = $attempt->answers->where('is_graded', true)->count();
                    $totalCount = $attempt->answers->count();
                    $progressPercent = $totalCount > 0 ? ($gradedCount / $totalCount) * 100 : 0;
                @endphp

                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Grading Progress</span>
                    <span class="text-sm text-gray-600 progress-info">{{ $gradedCount }}/{{ $totalCount }} questions
                        graded</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 progress-percent h-2 rounded-full transition-all duration-300"
                        style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>

            <!-- Questions -->
            <div class="space-y-8">
                @foreach ($attempt->answers as $answer)
                    <div class="bg-white rounded-lg shadow" data-is-graded="{{ $answer->is_graded ? '1' : '0' }}"
                        data-answer-id="{{ $answer->id }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Question {{ $loop->iteration }}
                                    @if ($answer->question->type)
                                        <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                            {{ strtoupper($answer->question->type) }}
                                        </span>
                                    @endif
                                </h3>
                                <div class="flex items-center space-x-2">
                                    @if ($answer->is_graded)
                                        <span
                                            class="grade-status px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                            Graded: {{ $answer->marks_awarded }}/{{ $answer->question->points }}
                                        </span>
                                    @else
                                        <span
                                            class="grade-status px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="my-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    {!! nl2br(e($answer->question->question_text)) !!}
                                </div>
                            </div>

                            @if ($answer->question->type === 'mcq' && $answer->question->options->count() > 0)
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-900 mb-2">Student Answer:</h4>
                                    <div class="space-y-2">
                                        @foreach ($answer->question->options as $option)
                                            <div
                                                class="flex items-center p-3 rounded-lg {{ $option->is_correct ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                                                <span
                                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 {{ $option->is_correct ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300' }}">
                                                    {{ chr(65 + $loop->index) }}
                                                </span>
                                                <span class="flex-1">{{ $option->option_text }}</span>
                                                @if ($option->is_correct)
                                                    <span
                                                        class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Correct</span>
                                                @endif
                                                @if (in_array($option->id, $answer->selected_options))
                                                    <span
                                                        class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">Selected</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-900 mb-2">Student Answer:</h4>
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        @if ($answer->answer_text)
                                            <div class="whitespace-pre-wrap">{{ $answer->answer_text }}</div>
                                        @else
                                            <span class="text-gray-500 italic">No answer provided</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Grading Form -->
                            <div class="border-t pt-6">
                                <form class="grade-form" data-answer-id="{{ $answer->id }}">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                                        @if ($answer->question->type === 'mcq')
                                            <div class="flex gap-4 items-center">
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" id="correct-{{ $answer->id }}"
                                                        name="is_correct" value="1"
                                                        {{ $answer->is_correct && $answer->is_graded ? 'checked' : '' }} />
                                                    <label for="correct-{{ $answer->id }}">Correct</label>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" id="incorrect-{{ $answer->id }}"
                                                        name="is_correct" value="0"
                                                        {{ !$answer->is_correct && $answer->is_graded ? 'checked' : '' }} />
                                                    <label for="incorrect-{{ $answer->id }}">Incorrect</label>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Marks Awarded (Max: {{ $answer->question->points }})
                                                </label>
                                                <input type="number" name="marks_awarded" min="0"
                                                    max="{{ $answer->question->points }}" step="0.5"
                                                    value="{{ $answer->marks_awarded }}"
                                                    class="w-full px-3 py-2 border border-gray-300 disabled:bg-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                    required>
                                            </div>
                                        @endif

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Comments (Optional)
                                            </label>
                                            <textarea name="grader_comments" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Provide comments to the student...">{{ $answer->grader_comments }}</textarea>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <button type="button"
                                                class="text-sm text-gray-600 hover:text-gray-800 reset-grade-btn @if ($answer->marks_awarded === null) hidden @endif">
                                                Reset Grading
                                            </button>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <button type="submit"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-nowrap">
                                                {{ $answer->marks_awarded !== null ? 'Update Grade' : 'Save Grade' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            <!-- Existing Feedback Display -->
                            @if ($answer->grader_comments && $answer->graded_at)
                                <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                                    <h5 class="font-medium text-yellow-800 mb-1">Previous Feedback:</h5>
                                    <p class="text-yellow-700 text-sm">{{ $answer->grader_comments }}</p>
                                    <p class="text-xs text-yellow-600 mt-1">
                                        Graded {{ $answer->graded_at->diffForHumans() }}
                                    </p>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Grade form submission
                $('.grade-form').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const answerId = form.data('answer-id');
                    const container = $(`[data-answer-id='${answerId}']`).first();
                    const submitBtn = container.find('button[type="submit"]');
                    const originalText = submitBtn.text();
                    const resetBtn = container.find('.reset-grade-btn');
                    const comments = form.find('textarea[name="grader_comments"]');

                    // Show loading state
                    submitBtn.prop('disabled', true)

                    $.ajax({
                        url: `/teacher/grading/answers/${answerId}/grade`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            marks_awarded: form.find('input[name="marks_awarded"]').val() || null,
                            grader_comments: comments.val(),
                            is_correct: form.find('input[name="is_correct"]:checked').val() || null
                        },
                        success: function(response) {
                            console.log('response', response)
                            // Update UI to show graded state
                            const gradeStatus = container.find('.grade-status');
                            gradeStatus.removeClass('bg-orange-100 text-orange-800').addClass(
                                'bg-green-100 text-green-800').text(
                                `Graded: ${response.marks_awarded}/${response.question_points}`);

                            // Show success message
                            showNotification('Grade saved successfully', 'success');

                            // Update submit button text
                            submitBtn.text('Update Grade');
                            submitBtn.prop('disabled', false)

                            // Update reset button
                            resetBtn.removeClass('hidden')

                            // Update isGraded in data to true
                            container.attr('data-is-graded', '1')

                            // Update current score
                            $('.current-score').text(response.current_score)

                            // Update comments
                            comments.val(response.grader_comments)

                            // Update progress bar
                            updateProgressBar();
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors || {};
                            let errorMsg = 'Error saving grade';

                            if (errors.marks_awarded) {
                                errorMsg = errors.marks_awarded[0];
                            }

                            showNotification(errorMsg, 'error');
                            submitBtn.text(originalText)
                        },
                    });
                });
            })

            $('.reset-grade-btn').on('click', function() {
                if (!confirm('Are you sure you want to reset this grade?')) {
                    return;
                }

                const answerId = $(this).closest('[data-answer-id]').data('answer-id');
                const container = $(`[data-answer-id='${answerId}']`).first();
                const submitBtn = container.find('button[type="submit"]');
                const resetBtn = container.find('.reset-grade-btn');
                const comments = container.find('textarea[name="grader_comments"]');
                const marksAwarded = container.find('input[name="marks_awarded"]');

                $.ajax({
                    url: `/teacher/grading/answers/${answerId}/reset`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('response', response)
                        // Update UI to show graded state
                        const gradeStatus = container.find('.grade-status');
                        gradeStatus.removeClass('bg-green-100 text-green-800').addClass(
                            'bg-orange-100 text-orange-800').text('Pending');

                        // Show success message
                        showNotification('Grading reset successfully', 'success');

                        // Update submit button text
                        submitBtn.text('Save Grade');

                        // Update reset button
                        resetBtn.addClass('hidden')

                        // Update isGraded in data to true
                        container.attr('data-is-graded', '0')

                        // Update current score
                        $('.current-score').text(response.current_score)

                        // Uncheck both radio
                        container.find('input[name="is_correct"]').prop('checked', false);

                        // Clear comments
                        comments.val('')

                        // Clear marks awarded input
                        marksAwarded.val('')

                        // Update progress bar
                        updateProgressBar();
                    },
                    error: function() {
                        showNotification('Error resetting grade', 'error');
                    }
                });
            });

            function updateProgressBar() {
                const total = $('[data-is-graded]').length
                const graded = $('[data-is-graded="1"]').length
                const percent = Math.floor((graded / total) * 100);
                console.log('percent', percent)

                $('.progress-percent').css('width', percent + '%');
                $('.progress-info').text(`${graded}/${total} questions graded`);
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
        </script>
</x-app-layout>
