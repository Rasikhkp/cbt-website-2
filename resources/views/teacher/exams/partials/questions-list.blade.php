@if($questions->count() > 0)
    <div class="space-y-3">
        @foreach($questions as $question)
            <div class="question-item border border-gray-200 rounded p-3 hover:bg-gray-50">
                <div class="flex items-start space-x-3">
                    <input type="checkbox"
                           name="questions[]"
                           value="{{ $question->id }}"
                           id="question_{{ $question->id }}"
                           class="mt-1 rounded question-checkbox"
                           onchange="updateSelectedQuestions()">

                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $question->getTypeDisplayName() }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $question->getDifficultyColorClass() }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-800">{{ Str::limit($question->question_text, 100) }}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                            <span>Default: {{ $question->points }} pts</span>
                            @if($question->images->count() > 0)
                                <span>{{ $question->images->count() }} image(s)</span>
                            @endif
                            @if($question->isMCQ())
                                <span>{{ $question->options->count() }} options</span>
                            @endif
                            <span>Tags: {{ implode(", ", $question->tags) }}
                        </div>
                    </div>

                    <div class="question-marks-input hidden">
                        <label class="block text-xs font-medium text-gray-700">Marks</label>
                        <input type="number"
                               name="question_marks[]"
                               step="0.1"
                               min="0.1"
                               max="100"
                               value="{{ $question->points }}"
                               class="mt-1 block w-20 text-sm border-gray-300 rounded">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-8">
        <p class="text-gray-500">No questions found with selected tags.</p>
    </div>
@endif