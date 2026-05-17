<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load([
            'assignedMentor:id,name,email,phone,subject_specialization,school',
            'portalNotifications' => fn ($query) => $query->take(5),
        ]);
        $enrollments = $user->enrollments()->with('course.lessons')->get();
        $completedCourses = collect();

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (!$course) continue;

            $totalLessons = $course->lessons->count();
            if ($totalLessons === 0) continue;

            $completedLessons = \App\Models\ProgressReport::where('student_id', $user->id)
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->where('is_completed', true)
                ->count();

            if ($completedLessons >= $totalLessons) {
                $completedCourses->push($course);
            }
        }

        return view('student.profile', compact('user', 'completedCourses'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:15'],
            'gender'      => ['nullable', 'string', 'in:male,female,other'],
            'address'     => ['nullable', 'string', 'max:500'],
            'class_level' => ['nullable', 'string'],
            'school'      => ['nullable', 'string', 'max:150'],
            'section'     => ['nullable', 'string', 'max:10'],
            'language'    => ['nullable', 'string', 'in:en,hi,pa'],
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $validated['low_data_mode'] = $request->has('low_data_mode');

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'  => ['required'],
            'password'          => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }
}
