<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <div class="flex gap-4">
                <a href="{{ route('admin.users.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New User
                </a>
                <a href="{{ route('admin.users.import') }}"
                    class="border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white font-bold py-2 px-4 rounded">
                    Import User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filter and Search Form -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-8 p-6">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                                <!-- Search on the left -->
                                <div class="w-full flex-1">
                                    <label for="search" class="block text-sm font-medium text-gray-700">Search User</label>
                                    <input type="text" name="search" id="search"
                                        value="{{ request()->input('search') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Name or email...">
                                </div>

                                <!-- Filters and buttons on the right -->
                                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-4 md:gap-2">
                                    <div class="w-full sm:w-auto">
                                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                        <select name="role" id="role"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="">All Roles</option>
                                            @php
                                                $roleLabels = [
                                                    'admin' => 'Admin',
                                                    'student' => 'Examinee',
                                                    'teacher' => 'Committee',
                                                ];
                                            @endphp
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" @selected(request()->input('role') == $role)>
                                                    {{ $roleLabels[$role] ?? ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full sm:w-auto">
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select name="status" id="status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="">All Statuses</option>
                                            <option value="active" @selected(request()->input('status') == 'active')>Active</option>
                                            <option value="inactive" @selected(request()->input('status') == 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="w-full sm:w-auto">
                                        <label for="date_from" class="block text-sm font-medium text-gray-700">From</label>
                                        <input type="date" name="date_from" id="date_from"
                                            value="{{ request()->input('date_from') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div class="w-full sm:w-auto">
                                        <label for="date_to" class="block text-sm font-medium text-gray-700">To</label>
                                        <input type="date" name="date_to" id="date_to"
                                            value="{{ request()->input('date_to') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div class="flex gap-2 w-full sm:w-auto">
                                        <button type="submit"
                                            class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                            Filter
                                        </button>
                                        <a href="{{ route('admin.users.index') }}"
                                            class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 flex items-center justify-center">
                                            Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Bulk Actions Bar -->
                    <div id="bulkActions" class="hidden bg-blue-50 border border-blue-200 p-4 rounded-md mb-4 flex items-center justify-between">
                        <span class="text-blue-800 font-medium">
                            <span id="selectedCount">0</span> users selected
                        </span>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.users.bulk-action') }}" method="POST" class="inline" onsubmit="return confirm('Activate selected users?')">
                                @csrf
                                <input type="hidden" name="action" value="activate">
                                <input type="hidden" name="user_ids[]" class="bulk-ids">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                    Activate Selected
                                </button>
                            </form>
                            <form action="{{ route('admin.users.bulk-action') }}" method="POST" class="inline" onsubmit="return confirm('Deactivate selected users? They will not be able to login.')">
                                @csrf
                                <input type="hidden" name="action" value="deactivate">
                                <input type="hidden" name="user_ids[]" class="bulk-ids">
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                    Deactivate Selected
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </th>
                                    @php
                                        $sortParams = array_merge(request()->only(['search', 'role', 'status', 'date_from', 'date_to']));
                                    @endphp
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a
                                            href="{{ route('admin.users.index', array_merge($sortParams, ['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                            Name
                                            @if (request('sort_by') == 'name')
                                                <span>{{ request('sort_order') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a
                                            href="{{ route('admin.users.index', array_merge($sortParams, ['sort_by' => 'created_at', 'sort_order' => request('sort_by', 'created_at') == 'created_at' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc'])) }}">
                                            Created
                                            @if (request('sort_by', 'created_at') == 'created_at')
                                                <span>{{ request('sort_order', 'desc') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 {{ !$user->is_active ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4">
                                            @if($user->id !== auth()->id())
                                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium {{ $user->is_active ? 'text-gray-900' : 'text-gray-500' }}">{{ $user->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-fit h-2 bg-green-400 rounded-full"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <span class="w-fit h-2 bg-red-400 rounded-full"></span>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if ($user->role === 'admin') bg-red-100 text-red-800
                                                @elseif($user->role === 'teacher') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">

                                                @php
                                                    $labels = [
                                                        'admin' => 'Admin',
                                                        'student' => 'Examinee',
                                                        'teacher' => 'Committee',
                                                    ];
                                                @endphp

                                                {{ $labels[$user->role] ?? ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->created_at->format('j F Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                                        <div class="flex items-center space-x-2">
                                                                                            <div class="relative">
                                                                                                <button onclick="toggleDropdown('dropdown-{{ $user->id }}')" class="text-gray-500 hover:text-gray-700 focus:outline-none bg-gray-100 rounded-full p-1 transition-colors hover:bg-gray-200">
                                                                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                                                                    </svg>
                                                                                                </button>
                                                                                                <div id="dropdown-{{ $user->id }}" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden border action-dropdown">
                                                                                                    <a href="{{ route('admin.users.show', $user) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Details</a>
                                                                                                    <a href="{{ route('admin.users.edit', $user) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit User</a>
                                                                                                    
                                                                                                    @if ($user->id !== auth()->id())
                                                                                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="block">
                                                                                                            @csrf
                                                                                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ $user->is_active ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }}">
                                                                                                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                                                                                                            </button>
                                                                                                        </form>
                                                                                                        <div class="border-t border-gray-100 my-1"></div>
                                                                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="block" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                                                                                Delete User
                                                                                                            </button>
                                                                                                        </form>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            const bulkIdInputs = document.querySelectorAll('.bulk-ids');

            function updateBulkActions() {
                const checked = Array.from(userCheckboxes).filter(cb => cb.checked);
                const count = checked.length;

                selectedCount.textContent = count;

                // Update hidden inputs
                const ids = checked.map(cb => cb.value);
                bulkIdInputs.forEach(input => {
                    // Create multiple hidden inputs for array
                    const container = input.parentElement;
                    // Remove old hidden inputs
                    container.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());

                    ids.forEach(id => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'user_ids[]';
                        hidden.value = id;
                        container.appendChild(hidden);
                    });
                });

                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                } else {
                    bulkActions.classList.add('hidden');
                }
            }

            selectAll.addEventListener('change', function() {
                userCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkActions();
            });

            userCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    }
                    updateBulkActions();
                });
            });

            // Dropdown management
            window.toggleDropdown = function(id) {
                // Close all other dropdowns first
                document.querySelectorAll('.action-dropdown').forEach(el => {
                    if (el.id !== id) {
                        el.classList.add('hidden');
                    }
                });
                
                // Toggle the requested dropdown
                const dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
                
                // Prevent event bubbling so document click doesn't immediately close it
                event.stopPropagation();
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                document.querySelectorAll('.action-dropdown').forEach(el => {
                    if (!el.classList.contains('hidden')) {
                        el.classList.add('hidden');
                    }
                });
            });
            
            // Prevent clicks inside the dropdown from closing it
            document.querySelectorAll('.action-dropdown').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>
</x-app-layout>
