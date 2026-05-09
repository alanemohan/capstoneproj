<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Scholarship;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::orderBy('deadline', 'asc')->get();
        return view('student.scholarships.index', compact('scholarships'));
    }
}
