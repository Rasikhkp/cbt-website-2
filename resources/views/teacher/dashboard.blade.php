<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Committee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ Auth::user()->name }}!</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-indigo-50 p-6 rounded-lg">
                            <h4 class="font-medium text-indigo-800">Question Bank</h4>
                            <p class="text-indigo-600 text-sm mt-2">Create and manage questions</p>
                            <div class="mt-3 space-y-1">
                                <p class="text-sm text-indigo-700">Your Questions: {{ Auth::user()->questions()->count() ?? 0 }}</p>
                                <p class="text-sm text-indigo-700">MCQ: {{ Auth::user()->questions()->where('type', 'mcq')->count() ?? 0 }}</p>
                                <p class="text-sm text-indigo-700">Short Answer: {{ Auth::user()->questions()->where('type', 'short')->count() ?? 0 }}</p>
                                <p class="text-sm text-indigo-700">Long Answer: {{ Auth::user()->questions()->where('type', 'long')->count() ?? 0 }}</p>
                            </div>
                            <a href="{{ route('teacher.questions.index') }}"
                               class="inline-block mt-3 text-indigo-600 hover:text-indigo-800 font-medium">
                                Manage Questions →
                            </a>
                        </div>

                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h4 class="font-medium text-yellow-800">Exams</h4>
                            <p class="text-yellow-600 text-sm mt-2">Create and manage exams</p>
                            <div class="mt-3 space-y-1">
                                <p class="text-sm text-yellow-700">Your Exams: {{ Auth::user()->createdExams()->count() ?? 0 }}</p>
                                <p class="text-sm text-yellow-700">Draft: {{ Auth::user()->createdExams()->where('status', 'draft')->count() ?? 0 }}</p>
                                <p class="text-sm text-yellow-700">Published: {{ Auth::user()->createdExams()->where('status', 'published')->count() ?? 0 }}</p>
                                <p class="text-sm text-yellow-700">Completed: {{ Auth::user()->createdExams()->where('status', 'completed')->count() ?? 0 }}</p>
                            </div>
                            <a href="{{ route('teacher.exams.index') }}"
                               class="inline-block mt-3 text-yellow-600 hover:text-yellow-800 font-medium">
                                Manage Exams →
                            </a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h4>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('teacher.questions.create') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                Add New Question
                            </a>
                            <a href="{{ route('teacher.exams.create') }}"
                               class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                Create New Exam
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
