<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $attempt->exam->title }}
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-sm text-gray-600">Time Remaining</div>
                    <div id="timer" class="text-lg font-bold text-red-600">
                        {{ $attempt->getRemainingTimeFormatted() }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Progress</div>
                    <div class="text-lg font-bold text-blue-600">
                        <span id="progress">{{ $attempt->getProgressPercentage() }}</span>%
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6">

                <!-- Question Navigation Sidebar -->
                <div class="w-64 flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 mb-4">Questions</h3>
                            <div class="grid grid-cols-5 gap-2">
                                @foreach ($questions as $index => $examQuestion)
                                    @php
                                        $questionAnswer = $answers->get($examQuestion->question_id);
                                        $isAnswered = $questionAnswer && $questionAnswer->isAnswered();
                                        $questionNumber = $index + 1;
                                        $isCurrent = $index == $currentQuestionIndex;
                                    @endphp
                                    <a href="{{ route('student.attempts.question', [$attempt, $questionNumber]) }}"
                                        class="w-10 h-10 rounded border-2 flex items-center justify-center text-sm font-medium transition-colors
                                              {{ $isCurrent
                                                  ? 'border-blue-500 bg-blue-100 text-blue-700'
                                                  : ($isAnswered
                                                      ? 'border-green-500 bg-green-100 text-green-700 hover:bg-green-200'
                                                      : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100') }}">
                                        {{ $questionNumber }}
                                    </a>
                                @endforeach
                            </div>

                            <div class="mt-4 space-y-2 text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded border-2 border-green-500 bg-green-100"></div>
                                    <span>Answered</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded border-2 border-blue-500 bg-blue-100"></div>
                                    <span>Current</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded border-2 border-gray-300 bg-white"></div>
                                    <span>Not answered</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t">
                                <form action="{{ route('student.attempts.submit', $attempt) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to submit your exam? You cannot make changes after submission.')"
                                    id="submitForm">
                                    @csrf
                                    <input type="hidden" name="confirm_submit" value="1">
                                    <button type="submit"
                                        class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Submit Exam
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-center">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Choose a question from the sidebar to begin
                                </h3>
                                <p class="text-gray-600 mb-6">
                                    Click on any question number to start answering.
                                    Your progress will be saved automatically.
                                </p>

                                @if ($questions->count() > 0)
                                    <a href="{{ route('student.attempts.question', [$attempt, 1]) }}"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Start with Question 1
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-submit warning modal -->
    <div id="autoSubmitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.072 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Time's Up!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Your exam will be automatically submitted in <span id="autoSubmitCountdown"></span> seconds.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="submitNowBtn"
                        class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Submit Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timeRemaining = {{ $attempt->getRemainingTimeSeconds() }};
        let timerInterval;
        let autoSubmitWarning = false;

        function updateTimer() {
            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = Math.floor(timeRemaining % 60);

            let display;
            if (hours > 0) {
                display =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            } else {
                display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            document.getElementById('timer').textContent = display;

            // Show warning when 1 minute remaining
            if (timeRemaining < 1 && !autoSubmitWarning) {
                showAutoSubmitWarning();
                autoSubmitWarning = true;
                clearInterval(timerInterval)
            } else {
                timeRemaining--
            }
        }

        function showAutoSubmitWarning() {
            document.getElementById('autoSubmitModal').classList.remove('hidden');
            let countdown = 10;

            const countdownInterval = setInterval(() => {
                document.getElementById('autoSubmitCountdown').textContent = countdown;
                countdown--;

                if (countdown < 0) {
                    autoSubmitExam()
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }

        function autoSubmitExam() {
            document.getElementById('submitForm').submit();
        }

        document.getElementById('submitNowBtn').addEventListener('click', function() {
            document.getElementById('submitForm').submit();
        });

        // Start the timer
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Initial call

        // Warn before leaving page
    </script>
</x-app-layout>
