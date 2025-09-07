<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, {{ Auth::user()->name }}!</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h4 class="font-medium text-green-800">Available Exams</h4>
                            <p class="text-green-600 text-sm mt-2">Take your assigned exams</p>
                            <a href="#" class="inline-block mt-3 text-green-600 hover:text-green-800">Coming in
                                Phase 5 →</a>
                        </div>

                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h4 class="font-medium text-blue-800">Results</h4>
                            <p class="text-blue-600 text-sm mt-2">View your exam results</p>
                            <a href="#" class="inline-block mt-3 text-blue-600 hover:text-blue-800">Coming in
                                Phase 7 →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
