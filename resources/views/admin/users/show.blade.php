<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">← Back to
                    Users</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('User Details: ') . $user->name }}
                </h2>
            </div>
            <a href="{{ route('admin.users.edit', $user) }}"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Edit User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Avatar -->
                        <div class="flex flex-col items-center">
                            <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                                <span class="text-3xl font-medium text-gray-700">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                @if ($user->role === 'admin') bg-red-100 text-red-800
                                @elseif($user->role === 'teacher') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <!-- User Details -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Name</h3>
                                <p class="text-lg text-gray-900">{{ $user->name }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                <p class="text-lg text-gray-900">{{ $user->email }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Role</h3>
                                <p class="text-lg text-gray-900">{{ ucfirst($user->role) }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Member Since</h3>
                                <p class="text-lg text-gray-900">{{ $user->created_at->format('F j, Y') }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                                <p class="text-lg text-gray-900">{{ $user->updated_at->format('F j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Future Stats Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-medium text-blue-800">Exams Created</h4>
                                <p class="text-2xl font-bold text-blue-600">-</p>
                                <p class="text-sm text-blue-600">Coming in Phase 4</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-800">Exams Taken</h4>
                                <p class="text-2xl font-bold text-green-600">-</p>
                                <p class="text-sm text-green-600">Coming in Phase 5</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="font-medium text-purple-800">Questions Added</h4>
                                <p class="text-2xl font-bold text-purple-600">-</p>
                                <p class="text-sm text-purple-600">Coming in Phase 3</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
