<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\UserImport;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['name', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(10);
        $roles = User::distinct()->pluck('role')->toArray();


        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User {$user->name} has been {$status}.");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate',
        ]);

        $userIds = $request->input('user_ids');
        $action = $request->input('action');
        $isActive = $action === 'activate';

        // Filter out self
        $userIds = array_diff($userIds, [Auth::id()]);

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'No valid users selected (you cannot modify your own account).');
        }

        User::whereIn('id', $userIds)->update(['is_active' => $isActive]);

        $count = count($userIds);
        $status = $isActive ? 'activated' : 'deactivated';
        
        return redirect()->back()->with('success', "{$count} users have been {$status}.");
    }

    public function import()
    {
        return view('admin.users.import');
    }

    public function uploadFile(Request $request)
    {
        set_time_limit(0);
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            $file = $request->file('file');

            Excel::import(new UserImport, $file);

            Log::info('Excel import successful', ['file_name' => $file->getClientOriginalName()]);

            return response()->json(['status' => 'success', 'message' => 'Import completed successfully.']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorDetails = [];

            foreach ($failures as $failure) {
                $errorDetails[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];

                Log::error('Validation error', end($errorDetails)); // logs latest error
            }

            return response()->json([
                'status' => 'validation_error',
                'message' => 'There were validation errors in the file.',
                'errors' => $errorDetails,
            ], 422);
        } catch (\Exception $e) {
            Log::error('Excel import failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred during import.',
            ], 500);
        }
    }
}
