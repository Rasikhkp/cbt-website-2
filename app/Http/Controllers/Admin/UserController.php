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
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

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

    public function import()
    {
        return view('admin.users.import');
    }

    public function uploadFile(Request $request)
    {
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
