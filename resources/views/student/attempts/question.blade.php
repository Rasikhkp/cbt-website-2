<x-app-layout>
    <noscript>
        <div id="noscript-warning" class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-2xl
                        bg-red-900 border-4 border-red-500 text-white p-10 rounded-xl shadow-[0_0_50px_rgba(255,0,0,0.8)]
                        text-center">

                <svg class="mx-auto h-16 w-16 text-yellow-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>

                <h1 class="text-4xl font-extrabold mb-4 uppercase tracking-wider">
                    CRITICAL EXAMINATION ERROR: JAVASCRIPT REQUIRED
                </h1>

                <p class="text-xl font-semibold mb-6 text-red-100">
                    <span class="text-yellow-400 font-extrabold">PREREQUISITE FAILURE.</span> This secure testing platform requires active JavaScript for essential functions, timer synchronization, and exam integrity checks.
                </p>

                <p class="text-lg font-medium text-red-200">
                    You cannot start or continue the assessment. Please enable JavaScript in your browser settings immediately and refresh this page to proceed.
                </p>

                <img
                    src="/add-suspicious-behaviour?attempt_id={{ $attempt->id }}&suspicious_behaviour={{ urlencode('Javascript must be enabled - ' . now()->format('Y-m-d H:i:s')) }}"
                    alt=""
                    width="1"
                    height="1"
                    style="display:none;"
                    onerror="this.remove();"
                />
            </div>
        </div>
    </noscript>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $attempt->exam->title }} - Question {{ $questionNumber }}
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
                                @foreach($questions as $index => $examQuestion)
                                    @php
                                        $questionAnswer = $answers->get($examQuestion->question_id);
                                        $isAnswered = $questionAnswer && $questionAnswer->isAnswered();
                                        $qNumber = $index + 1;
                                        $isCurrent = $index == $questionIndex;
                                    @endphp
                                    <a href="{{ route('student.attempts.question', [$attempt, $qNumber]) }}"
                                       class="w-10 h-10 rounded border-2 flex items-center justify-center text-sm font-medium transition-colors
                                              {{ $isCurrent ? 'border-blue-500 bg-blue-100 text-blue-700' :
                                                 ($isAnswered ? 'border-green-500 bg-green-100 text-green-700 hover:bg-green-200' :
                                                               'border-gray-300 bg-white text-gray-700 hover:bg-gray-100') }}">
                                        {{ $qNumber }}
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

                            <!-- Quick Stats -->
                            <div class="mt-4 pt-4 border-t text-sm">
                                @php
                                    $totalQuestions = $questions->count();
                                    $answeredCount = $answers->filter(fn($a) => $a->isAnswered())->count();
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between">
                                        <span>Answered:</span>
                                        <span id="answered" class="font-medium">{{ $answeredCount }}/{{ $totalQuestions }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Remaining:</span>
                                        <span id="remaining" class="font-medium">{{ $totalQuestions - $answeredCount }}</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div id="progress-bar" class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                             style="width: {{ $attempt->getProgressPercentage() }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 pt-4 border-t">
                                <form action="{{ route('student.attempts.submit', $attempt) }}" method="POST"
                                      data-confirm="Are you sure you want to submit your exam? You cannot make changes after submission."
                                      id="submitForm">
                                    @csrf
                                    <input type="hidden" name="confirm_submit" value="1">
                                    <button id='submit-btn' type="submit"
                                            class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Submit Exam
                                    </button>
                                </form>

                                <!-- Warning about incomplete answers -->
                                @if($answeredCount < $totalQuestions)
                                    <p id="unanswered" class="text-xs text-orange-600 mt-2 text-center">
                                    {{ $answeredCount < $totalQuestions ?  $totalQuestions - $answeredCount . " question(s) unanswered" : null }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Question Content -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
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

                            <!-- Question Header -->
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-3">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                        Question {{ $questionNumber }} of {{ $questions->count() }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $question->getTypeDisplayName() }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->getDifficultyColorClass() }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                    @if($question->tags && count($question->tags) > 0)
                                        @foreach(array_slice($question->tags, 0, 2) as $tag)
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-gray-700">
                                    {{ $currentQuestion->marks }} {{ Str::plural('mark', $currentQuestion->marks) }}
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-6">
                                <div class="bg-gray-50 mb-4  p-4 rounded-lg">
                                    <div class="prose">{!! $question->question_text !!}</div>
                                </div>

                                @if($question->images && $question->images->count() > 0)
                                    <div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($question->images as $image)
                                                <div class="border rounded-lg p-2">
                                                    <img src="{{ $image->getUrl() }}"
                                                         alt="question image"
                                                         class="w-full h-48 object-contain cursor-pointer hover:shadow-lg transition-shadow"
                                                         onclick="openImageModal('{{ $image->getUrl() }}')">
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Click on images to view full size</p>
                                    </div>
                                @endif
                            </div>


                            <!-- Answer Form -->
                            <form id="answerForm" action="{{ route('student.attempts.save-answer', $attempt) }}" method="POST">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">

                                @if($question->isMCQ())
                                    <!-- MCQ Options -->
                                    <div class="mb-6">
                                        <h4 class="font-medium text-gray-700 mb-3">Select your answer(s):</h4>
                                        <div class="space-y-3">
                                            @php $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp
                                            @foreach($question->options as $index => $option)
                                                @php
                                                    $isSelected = $answer && $answer->selected_options && in_array($option->id, $answer->selected_options);
                                                @endphp
                                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-colors {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                                    <input type="radio"
                                                           name="selected_options[]"
                                                           value="{{ $option->id }}"
                                                           {{ $isSelected ? 'checked' : '' }}
                                                           class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                           onchange="autoSaveAnswer()">
                                                    <span class="ml-3 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $isSelected ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600' }} font-medium transition-colors">
                                                        {{ $optionLabels[$index] ?? chr(65 + $index) }}
                                                    </span>
                                                    <span class="ml-3 flex-1 gap-2 flex flex-col items-start text-gray-800">
                                                        <div class="prose">{!! $option->option_text !!}</div>

                                                        @if($option->image_path)
                                                            <img src="{{ Storage::url($option->image_path) }}"
                                                                alt="option_image"
                                                                class="rounded-xl h-48 object-contain cursor-pointer"
                                                                onclick="openImageModal('{{ Storage::url($option->image_path) }}')">
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @if($question->options->where('is_correct', true)->count() > 1)
                                            <p class="text-sm text-blue-600 mt-2">
                                                <i class="fas fa-info-circle"></i>
                                                This question may have multiple correct answers. Select all that apply.
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <!-- Text Answer -->
                                    <div class="mb-6">
                                        <label for="answer_text" class="block text-sm font-medium text-gray-700 mb-2">
                                            Your Answer:
                                        </label>
                                        <textarea id="answer_text" name="answer_text" class="tinymce-field" placeholder="{{ $question->isShort() ? 'Enter a brief answer...' : 'Provide a detailed answer...' }}">{{ $answer ? $answer->answer_text : '' }}</textarea>
                                        @if($question->isLong())
                                            <p class="text-xs text-gray-500 mt-1">Take your time to provide a comprehensive answer. You can use multiple paragraphs.</p>
                                        @elseif($question->isShort())
                                            <p class="text-xs text-gray-500 mt-1">Keep your answer brief and to the point.</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Answer Status -->
                                <div class="mb-6 flex justify-between gap-2">
                                    <div id="answered-at" class="text-sm text-gray-500">
                                        {{ $answer->answered_at ? "Last saved: " . $answer->answered_at->format('M j, g:i A') : null}}
                                    </div>
                                    <div id="saveStatus" class="text-sm text-gray-600 hidden">
                                        <span class="text-green-600">✓ Auto saved</span>
                                    </div>
                                </div>

                                <!-- Navigation and Save Buttons -->
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-3">
                                        @if($questionIndex > 0)
                                            <button type="submit" name="action" value="previous"
                                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                                Previous
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex gap-3">
                                        @if($questionIndex < $questions->count() - 1)
                                            <button type="submit" name="action" value="next"
                                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
                                                Next
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                    onclick="document.getElementById('submit-btn').click()"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Submit Exam
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Keyboard Shortcuts Info -->
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
                                    <strong>Keyboard shortcuts:</strong>
                                    Ctrl+S to save,
                                    @if($questionIndex > 0)Ctrl+← for previous, @endif
                                    @if($questionIndex < $questions->count() - 1)Ctrl+→ for next @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium" id="modalImageTitle">Image</h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="text-center">
                <img id="modalImage" src="" alt="" class="max-w-full max-h-96 mx-auto">
            </div>
        </div>
    </div>

    <!-- Auto-submit warning modal -->
    <div id="autoSubmitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.072 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
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

    <div id="warning-container"></div>


    <script>
        let timeRemaining = {{ $attempt->getRemainingTimeSeconds() }};
        let timerInterval;
        let autoSubmitWarning = false;
        let debounceTimer;

        function updateTimer() {
            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = Math.floor(timeRemaining % 60);

            let display;
            if (hours > 0) {
                display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            } else {
                display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            document.getElementById('timer').textContent = display;

            if (timeRemaining < 1 && !autoSubmitWarning) {
                clearInterval(timerInterval);
                showAutoSubmitWarning();
                autoSubmitWarning = true;

                return;
            } else {
                timeRemaining--;
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

        function autoSaveAnswer(answerText) {
            const formData = new FormData(document.getElementById('answerForm'));
            formData.set('answer_text', answerText)

            fetch('{{ route("student.attempts.auto-save", $attempt) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({})); // catch invalid JSON
                if (!response.ok) {
                    if (data.error === 'Exam is expired') {
                        showAutoSubmitWarning()
                    }

                    throw new Error(data.message || "Auto-save failed");
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('saveStatus').classList.remove('hidden');
                    if (data.progress) {
                        document.getElementById('progress').textContent = data.progress;
                    }

                    if (data.answered_at) {
                        document.getElementById('answered-at').textContent = data.answered_at
                    }

                    if (data.answered_count && data.total_questions) {
                        document.getElementById('answered').textContent = `${data.answered_count} / ${data.total_questions}`
                        document.getElementById('remaining').textContent = data.total_questions - data.answered_count;
                        document.getElementById('progress-bar').style.width = `${data.progress}%`
                        document.getElementById('unanswered').textContent = data.answered_count < data.total_questions ? `${data.total_questions - data.answered_count} question(s) unanswered` : ''
                    }
                    setTimeout(() => {
                        document.getElementById('saveStatus').classList.add('hidden');
                    }, 2000);
                }
            })
            .catch(error => {
                console.error(error);
            });
        }

        function debouncedAutoSave(answerText) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                autoSaveAnswer(answerText);
            }, 300);
        }

        function openImageModal(src, title) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        async function addSuspiciousBehaviour(attemptId, behaviour) {
            try {
                // Build query parameters safely
                const params = new URLSearchParams({
                    attempt_id: attemptId,
                    suspicious_behaviour: behaviour
                });

                // Make GET request
                const response = await fetch(`/add-suspicious-behaviour?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // Parse JSON response
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to add suspicious behaviour');
                }

                return data;

            } catch (error) {
                console.error('❌ Error adding suspicious behaviour:', error);
                return { success: false, message: error.message };
            }
        }

        let warningTimeout = null;
        let warningInterval = null;

        function showWarning(text) {
            // Cleanup existing warning
            clearTimeout(warningTimeout);
            clearInterval(warningInterval);
            document.getElementById('warning-overlay')?.remove();
            document.getElementById('warning-box')?.remove();

            // Create overlay
            const overlay = document.createElement('div');
            overlay.id = 'warning-overlay';
            overlay.className = `
                fixed inset-0 bg-black/60 backdrop-blur-sm
                flex items-center justify-center
                transition-opacity duration-300 opacity-100
                z-[9990]
            `;
            document.body.appendChild(overlay);

            // Create warning box
            const box = document.createElement('div');
            box.id = 'warning-box';
            box.className = `
                bg-red-700 text-white text-center
                px-6 py-5 rounded-2xl shadow-xl
                max-w-md w-[90%]
                text-xl font-semibold tracking-wide
                transition-all duration-300 transform opacity-100 scale-100
            `;
            overlay.appendChild(box);

            // Timer logic
            let timeLeft = 5;
            const updateContent = () => {
                box.innerHTML = `
                    <p class="mb-2">${text}</p>
                    <p class="text-sm text-red-200">
                        Closing in <span class="font-mono text-yellow-300">${timeLeft}</span>s
                    </p>
                `;
            };
            updateContent();

            warningInterval = setInterval(() => {
                timeLeft--;
                updateContent();
                if (timeLeft <= 0) closeWarning();
            }, 1000);

            function closeWarning() {
                clearInterval(warningInterval);
                box.classList.add('opacity-0', 'scale-95');
                overlay.classList.add('opacity-0');
                warningTimeout = setTimeout(() => {
                    overlay.remove();
                }, 300);
            }

            addSuspiciousBehaviour({{ $attempt->id, }}, `${text} - ${new Date().toLocaleString()}`)
        }

        // Event listeners
        document.getElementById('submitNowBtn').addEventListener('click', function() {
            document.getElementById('submitForm').submit();
        });

        // Start the timer
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey) {
                switch(e.key) {
                    case 's':
                        e.preventDefault();
                        document.getElementById('answerForm').submit();
                        break;
                    case 'ArrowLeft':
                        @if($questionIndex > 0)
                            e.preventDefault();
                            window.location.href = '{{ route("student.attempts.question", [$attempt, $questionNumber - 1]) }}';
                        @endif
                        break;
                    case 'ArrowRight':
                        @if($questionIndex < $questions->count() - 1)
                            e.preventDefault();
                            window.location.href = '{{ route("student.attempts.question", [$attempt, $questionNumber + 1]) }}';
                        @endif
                        break;
                }
            }
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // Warn before leaving page - but allow internal navigation
        let isInternalNavigation = false;

        // Mark internal navigation links
        document.querySelectorAll('a[href*="/student/attempts/"]').forEach(link => {
            link.addEventListener('click', function() {
                isInternalNavigation = true;
                setTimeout(() => { isInternalNavigation = false; }, 100);
            });
        });

        // Mark form submissions as internal
        document.getElementById('answerForm').addEventListener('submit', function() {
            isInternalNavigation = true;
        });

        document.getElementById('submitForm').addEventListener('submit', function() {
            isInternalNavigation = true;
        });

        window.addEventListener('load', () => {
            if (window.tinymce) {
                tinymce.activeEditor.on('input', (e) => {
                    const answerText = e.currentTarget.innerHTML
                    debouncedAutoSave(answerText)
                });
            } else {
                console.error('tinymce not loaded yet');
            }
        });

        window.addEventListener('keydown', (e) => {
            const active = document.activeElement;
            const insideTinyMCE =
                active.closest?.('.tox-tinymce') || // for main TinyMCE UI
                active.classList?.contains('tox-edit-area') || // sometimes directly focused
                active.id?.includes('tinymce'); // fallback check

            // Allow all shortcuts inside TinyMCE editor
            if (insideTinyMCE) return;

            // Block Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+A
            if (e.ctrlKey && ['c', 'v', 'x', 'a'].includes(e.key.toLowerCase())) {
                e.preventDefault();
                showWarning('Copy/Paste/Cut/Select All not allowed.');
                return false;
            }

            // Block F12, Ctrl+Shift+I/J/C (DevTools)
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(e.key.toLowerCase()))) {
                e.preventDefault();
                showWarning('Developer Tools are disabled.');
                return false;
            }

            // Block PrintScreen
            if (e.key === 'PrintScreen') {
                e.preventDefault();
                showWarning('Screenshots is not allowed');
                return false;
            }

            // Block Ctrl+P (Print)
            if (e.ctrlKey && e.key.toLowerCase() === 'p') {
                e.preventDefault();
                showWarning('Printing is not allowed');
                return false;
            }
        });

        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            showWarning('🚫 Right-click is disabled');
            return false;
        });

        document.addEventListener('visibilitychange', () => {
            showWarning('Opening another tab or app is not allowed');
        });

    </script>
</x-app-layout>
