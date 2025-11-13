<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UserImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * Create a new User model from the imported row.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $role = match (strtolower($row['role'])) {
            'committee' => 'teacher',
            'examinee' => 'student',
            'admin' => 'admin',
            default => 'unknown',
        };

        return User::create([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'role'     => $role,
            'password' => Hash::make($row['password']),
        ]);
    }

    /**
     * Define validation rules for each imported row.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.name'     => ['required', 'string', 'max:255'],
            '*.email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            '*.role'     => ['required', Rule::in(['admin', 'examinee', 'committee'])],
            '*.password' => ['required', 'string', 'min:6'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.name.required'     => 'Name is required.',
            '*.name.string'       => 'Name must be a valid string.',
            '*.name.max'          => 'Name may not be greater than 255 characters.',

            '*.email.required'    => 'Email is required.',
            '*.email.email'       => 'Email must be a valid email address.',
            '*.email.max'         => 'Email may not be greater than 255 characters.',
            '*.email.unique'      => 'Email has already been taken.',

            '*.role.required'     => 'Role is required.',
            '*.role.in'           => 'Role must be one of: admin, examinee, or committee.',

            '*.password.required' => 'Password is required.',
            '*.password.string'   => 'Password must be a valid string.',
            '*.password.min'      => 'Password must be at least 6 characters.',
        ];
    }
}
