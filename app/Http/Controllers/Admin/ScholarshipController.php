<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::latest()->paginate(15);
        return view('admin.scholarships.index', compact('scholarships'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'title_pa' => 'nullable|string|max:255',
            'amount' => 'required|string|max:255',
            'amount_hi' => 'nullable|string|max:255',
            'amount_pa' => 'nullable|string|max:255',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'description_hi' => 'nullable|string',
            'description_pa' => 'nullable|string',
            'eligibility_criteria' => 'nullable|string',
            'eligibility_criteria_hi' => 'nullable|string',
            'eligibility_criteria_pa' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        Scholarship::create($request->all());

        return back()->with('success', __('messages.scholarship_added_success'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'title_pa' => 'nullable|string|max:255',
            'amount' => 'required|string|max:255',
            'amount_hi' => 'nullable|string|max:255',
            'amount_pa' => 'nullable|string|max:255',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'description_hi' => 'nullable|string',
            'description_pa' => 'nullable|string',
            'eligibility_criteria' => 'nullable|string',
            'eligibility_criteria_hi' => 'nullable|string',
            'eligibility_criteria_pa' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        $scholarship->update($request->all());

        return back()->with('success', __('messages.scholarship_updated_success'));
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return back()->with('success', __('messages.scholarship_deleted_success'));
    }
}
