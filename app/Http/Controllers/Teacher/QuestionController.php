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
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

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

                foreach ($request->options as $index => $option) {
                    $path = '';
                    $filename = '';

                    if (isset($option['option_image'])) {
                        $image = $option['option_image'];
                        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('questions/' . $question->id, $filename, 'public');
                    }

                    if (!empty($option['option_text'])) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $option['option_text'],
                            'is_correct' => in_array($index, $correctOptions),
                            'order' => $index,
                            'image_path' => $path
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
                $correctOptions = $request->correct_options ?: [];
                $existingOptionImages = collect($request->options)
                    ->filter(fn($option) => !empty($option['existing_option_image']))
                    ->values()
                    ->map(function ($option) {
                        return $option['existing_option_image'];
                    })
                    ->all();

                foreach ($question->options as $option) {
                    if (!in_array($option->image_path, $existingOptionImages)) {
                        Storage::disk('public')->delete($option->image_path);
                    }

                    $option->delete();
                }

                foreach ($request->options as $index => $option) {
                    $path = '';
                    $filename = '';

                    if (!empty($option['new_option_image'])) {
                        $image = $option['new_option_image'];
                        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('questions/' . $question->id, $filename, 'public');
                    } else if (!empty($option['existing_option_image'])) {
                        $path = $option['existing_option_image'];
                    }

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['option_text'],
                        'is_correct' => in_array($index, $correctOptions),
                        'order' => $index,
                        'image_path' => $path
                    ]);
                }

            } else {
                // Remove options for non-MCQ questions
                $question->options()->delete();
            }

            foreach ($question->images as $image) {
                if(!in_array($image->path, $request->existing_images)) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }

            if (!empty($request->new_images)) {
                foreach ($request->new_images as $index => $image) {
                    $currentMaxOrder = $question->images()->max('order') ?: -1;

                    if ($image instanceof UploadedFile) {
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
                        ]);
                    }
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

    public function import()
    {
        return view('teacher.questions.import');
    }

    public function uploadFile(Request $request)
    {
        try {
            $file = $request->file('file');

            Log::info('file', [$file]);
        } catch (\Exception $e) {

        }
    }

}
