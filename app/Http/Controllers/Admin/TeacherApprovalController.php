<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $teachers = User::where('role', 'teacher')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.teachers.index', compact('teachers', 'status'));
    }

    public function approve(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        $oldStatus = $user->status;
        $user->update(['status' => 'approved']);
        
        \App\Services\AuditLogger::log('approve_teacher', $user, ['status' => $oldStatus], ['status' => 'approved']);
        $user->notify(new \App\Notifications\ApprovalNotification('Teacher Profile', 'approved', $user->name));
        
        return back()->with('success', "{$user->name} has been approved as a teacher.");
    }

    public function reject(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        $oldStatus = $user->status;
        $user->update(['status' => 'rejected']);
        
        \App\Services\AuditLogger::log('reject_teacher', $user, ['status' => $oldStatus], ['status' => 'rejected']);
        $user->notify(new \App\Notifications\ApprovalNotification('Teacher Profile', 'rejected', $user->name));
        
        return back()->with('success', "{$user->name}'s teacher application has been rejected.");
    }
}
