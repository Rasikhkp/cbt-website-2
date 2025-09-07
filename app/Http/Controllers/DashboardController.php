<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Redirect based on role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return view('dashboard');
        }
    }

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function teacherDashboard()
    {
        return view('teacher.dashboard');
    }

    public function studentDashboard()
    {
        return view('student.dashboard');
    }
}
