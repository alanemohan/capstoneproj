<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveClassController extends Controller
{
    public function index()
    {
        $liveClasses = LiveClass::with(['teacher', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('student.live-classes.index', compact('liveClasses'));
    }
}
