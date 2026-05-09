<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = User::where('role', 'teacher')->where('is_mentor', true)->where('is_active', true)->get();
        return view('student.mentors.index', compact('mentors'));
    }
}
