<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function index(Request $request)
    {

        $query = Question::with(['creator', 'options', 'images']);

        // Filter by current user if teacher
        if (Auth::user()->isTeacher()) {
            $query->where('created_by', Auth::id());
        }

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('search')) {
            $query->where('question_text', 'LIKE', '%' . $request->search . '%');
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('teacher.questions.index', compact('questions'));
    }

    public function create()
    {
        return view('teacher.questions.create');
    }

    public function store(StoreQuestionRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create question
            $question = Question::create([
                'created_by' => Auth::id(),
                'type' => $request->type,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'points' => $request->points,
                'difficulty' => $request->difficulty,
                'tags' => $request->tags ?: [],
            ]);

            // Handle MCQ options
            if ($request->type === 'mcq') {
                $correctOptions = $request->correct_options ?: [];

                foreach ($request->options as $index => $optionText) {
                    if (!empty($optionText)) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionText,
                            'is_correct' => in_array($index, $correctOptions),
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('questions/' . $question->id, $filename, 'public');

                    QuestionImage::create([
                        'question_id' => $question->id,
                        'filename' => $filename,
                        'original_name' => $image->getClientOriginalName(),
                        'path' => $path,
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'order' => $index,
                        'alt_text' => $request->alt_texts[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('teacher.questions.index')
                ->with('success', 'Question created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating question: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Question $question)
    {
        // Check authorization
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403);
        }

        $question->load(['options', 'images', 'creator']);
        return view('teacher.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        // Check authorization
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403);
        }

        $question->load(['options', 'images']);
        return view('teacher.questions.edit', compact('question'));
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        try {
            DB::beginTransaction();

            // Update question
            $question->update([
                'type' => $request->type,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'points' => $request->points,
                'difficulty' => $request->difficulty,
                'tags' => $request->tags ?: [],
            ]);

            // Handle MCQ options
            if ($request->type === 'mcq') {
                // Delete existing options
                $question->options()->delete();

                $correctOptions = $request->correct_options ?: [];

                foreach ($request->options as $index => $optionText) {
                    if (!empty($optionText)) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionText,
                            'is_correct' => in_array($index, $correctOptions),
                            'order' => $index,
                        ]);
                    }
                }
            } else {
                // Remove options for non-MCQ questions
                $question->options()->delete();
            }

            // Handle image removal
            if ($request->filled('remove_images')) {
                $imagesToRemove = QuestionImage::whereIn('id', $request->remove_images)
                    ->where('question_id', $question->id)
                    ->get();

                foreach ($imagesToRemove as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $currentMaxOrder = $question->images()->max('order') ?: -1;

                foreach ($request->file('images') as $index => $image) {
                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('questions/' . $question->id, $filename, 'public');

                    QuestionImage::create([
                        'question_id' => $question->id,
                        'filename' => $filename,
                        'original_name' => $image->getClientOriginalName(),
                        'path' => $path,
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'order' => $currentMaxOrder + $index + 1,
                        'alt_text' => $request->alt_texts[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('teacher.questions.index')
                ->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating question: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Question $question)
    {
        // Check authorization
        if (Auth::user()->isTeacher() && $question->created_by !== Auth::id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Delete associated images from storage
            foreach ($question->images as $image) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete the question (cascades to options and images)
            $question->delete();

            DB::commit();

            return redirect()->route('teacher.questions.index')
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error deleting question: ' . $e->getMessage());
        }
    }
}
