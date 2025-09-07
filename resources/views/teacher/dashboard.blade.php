<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Dashboard') }}
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
                            <a href="#" class="inline-block mt-3 text-indigo-600 hover:text-indigo-800">Coming in
                                Phase 3 →</a>
                        </div>

                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h4 class="font-medium text-yellow-800">Exams</h4>
                            <p class="text-yellow-600 text-sm mt-2">Create and manage exams</p>
                            <a href="#" class="inline-block mt-3 text-yellow-600 hover:text-yellow-800">Coming in
                                Phase 4 →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
