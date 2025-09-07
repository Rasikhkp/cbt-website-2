<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome, Admin!</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h4 class="font-medium text-blue-800">User Management</h4>
                            <p class="text-blue-600 text-sm mt-2">Manage teachers and students</p>
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-block mt-3 text-blue-600 hover:text-blue-800 font-medium">
                                Manage Users →
                            </a>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg">
                            <h4 class="font-medium text-green-800">System Stats</h4>
                            <p class="text-green-600 text-sm mt-2">View system statistics</p>
                            <div class="mt-3 space-y-1">
                                <p class="text-sm text-green-700">Total Users: {{ \App\Models\User::count() }}</p>
                                <p class="text-sm text-green-700">Admins:
                                    {{ \App\Models\User::where('role', 'admin')->count() }}</p>
                                <p class="text-sm text-green-700">Teachers:
                                    {{ \App\Models\User::where('role', 'teacher')->count() }}</p>
                                <p class="text-sm text-green-700">Students:
                                    {{ \App\Models\User::where('role', 'student')->count() }}</p>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h4 class="font-medium text-purple-800">Reports</h4>
                            <p class="text-purple-600 text-sm mt-2">Generate system reports</p>
                            <a href="#" class="inline-block mt-3 text-purple-600 hover:text-purple-800">Coming
                                Soon →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
