<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class TeacherApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $search = $request->input('search', '');

        $teachers = User::where('role', 'teacher')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('subject_specialization', 'like', "%{$search}%")
                          ->orWhere('qualification', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => User::where('role', 'teacher')->where('status', 'pending')->count(),
            'approved' => User::where('role', 'teacher')->where('status', 'approved')->count(),
            'rejected' => User::where('role', 'teacher')->where('status', 'rejected')->count(),
            'all'      => User::where('role', 'teacher')->count(),
        ];

        return view('admin.teachers.index', compact('teachers', 'status', 'search', 'counts'));
    }

    public function approve(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        $oldStatus = $user->status;
        $user->update(['status' => 'approved', 'is_active' => true]);

        AuditLogger::log('approve_teacher', $user, ['status' => $oldStatus], ['status' => 'approved']);
        $user->notify(new \App\Notifications\ApprovalNotification('Teacher Profile', 'approved', $user->name));

        return back()->with('success', "✅ {$user->name} has been approved as a teacher.");
    }

    public function reject(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        $oldStatus = $user->status;
        $user->update(['status' => 'rejected']);

        AuditLogger::log('reject_teacher', $user, ['status' => $oldStatus], ['status' => 'rejected']);
        $user->notify(new \App\Notifications\ApprovalNotification('Teacher Profile', 'rejected', $user->name));

        return back()->with('success', "❌ {$user->name}'s teacher application has been rejected.");
    }

    public function suspend(User $user)
    {
        abort_unless($user->role === 'teacher', 403);

        $newActive = !$user->is_active;
        $user->update(['is_active' => $newActive]);

        $action = $newActive ? 'unsuspend_teacher' : 'suspend_teacher';
        AuditLogger::log($action, $user, ['is_active' => !$newActive], ['is_active' => $newActive]);

        $msg = $newActive
            ? "🔓 {$user->name}'s account has been re-activated."
            : "🔒 {$user->name}'s account has been suspended.";

        return back()->with('success', $msg);
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $name = $user->name;
        AuditLogger::log('delete_teacher', $user, ['id' => $user->id, 'email' => $user->email], []);
        $user->delete();

        return back()->with('success', "🗑️ Teacher account for \"{$name}\" has been permanently deleted.");
    }
}
