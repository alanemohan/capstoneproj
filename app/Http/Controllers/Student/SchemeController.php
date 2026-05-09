<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GovernmentScheme;

class SchemeController extends Controller
{
    public function index()
    {
        $schemes = GovernmentScheme::latest()->get();
        return view('student.schemes.index', compact('schemes'));
    }
}
