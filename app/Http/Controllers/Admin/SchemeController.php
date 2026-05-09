<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernmentScheme;
use Illuminate\Http\Request;

class SchemeController extends Controller
{
    public function index()
    {
        $schemes = GovernmentScheme::latest()->paginate(15);
        return view('admin.schemes.index', compact('schemes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'title_pa' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_hi' => 'nullable|string',
            'description_pa' => 'nullable|string',
            'target_audience' => 'required|string',
            'target_audience_hi' => 'nullable|string',
            'target_audience_pa' => 'nullable|string',
            'benefits' => 'required|string',
            'benefits_hi' => 'nullable|string',
            'benefits_pa' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        GovernmentScheme::create($request->all());

        return back()->with('success', __('messages.scheme_added_success'));
    }

    public function update(Request $request, GovernmentScheme $scheme)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'title_pa' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_hi' => 'nullable|string',
            'description_pa' => 'nullable|string',
            'target_audience' => 'required|string',
            'target_audience_hi' => 'nullable|string',
            'target_audience_pa' => 'nullable|string',
            'benefits' => 'required|string',
            'benefits_hi' => 'nullable|string',
            'benefits_pa' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        $scheme->update($request->all());

        return back()->with('success', __('messages.scheme_updated_success'));
    }

    public function destroy(GovernmentScheme $scheme)
    {
        $scheme->delete();
        return back()->with('success', __('messages.scheme_deleted_success'));
    }
}
