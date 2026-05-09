<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with('student')->latest()->paginate(15);
        return view('admin.complaints.index', compact('complaints'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved,rejected',
        ]);

        $complaint->update(['status' => $request->status]);

        return back()->with('success', __('messages.complaint_status_updated'));
    }
}
