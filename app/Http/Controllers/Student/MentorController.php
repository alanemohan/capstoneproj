<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class MentorController extends Controller
{
    public function index()
    {
        $student = auth()->user();
        $mentors = $student->assignedMentor ? collect([$student->assignedMentor]) : collect();
        return view('student.mentors.index', compact('mentors'));
    }
}
