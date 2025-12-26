<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Registration</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Enable or disable user registration.
                                </p>
                            </div>

                            <label class="inline-flex items-center cursor-pointer gap-3">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $registrationEnabled ? 'Enabled' : 'Disabled' }}
                                </span>

                                <div class="relative">
                                    <input
                                        type="checkbox"
                                        name="registration_enabled"
                                        class="sr-only peer"
                                        {{ $registrationEnabled ? 'checked' : '' }}
                                    >

                                    <!-- Track -->
                                    <div class="w-14 h-8 rounded-full bg-gray-400 peer-checked:bg-green-500 transition-colors"></div>

                                    <!-- Thumb -->
                                    <div
                                        class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full
                                               transition-transform peer-checked:translate-x-6"
                                    ></div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-primary-button>
                                {{ __('Save Settings') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
