<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class CareerController extends Controller
{
    /**
     * Get the translated careers data.
     */
    private function getCareers()
    {
        $translations = __('careers.data');
        
        // Ensure we always have an array even if translation is missing
        if (!is_array($translations)) {
            return [];
        }

        $careers = [];
        // Define metadata that isn't translatable (colors, icons)
        $meta = [
            'agriculture' => ['icon' => '🌱', 'bg_color' => 'emerald'],
            'it' => ['icon' => '💻', 'bg_color' => 'blue'],
            'healthcare' => ['icon' => '🏥', 'bg_color' => 'rose'],
            'renewable-energy' => ['icon' => '☀️', 'bg_color' => 'yellow'],
            'teaching' => ['icon' => '📚', 'bg_color' => 'purple'],
            'finance' => ['icon' => '🏦', 'bg_color' => 'indigo'],
            'mechanic' => ['icon' => '🔧', 'bg_color' => 'gray'],
        ];

        foreach ($translations as $id => $data) {
            $careers[] = array_merge([
                'id' => $id,
                'icon' => $meta[$id]['icon'] ?? '🚀',
                'bg_color' => $meta[$id]['bg_color'] ?? 'gray',
            ], $data);
        }

        return $careers;
    }

    public function index()
    {
        return view('student.careers.index', ['careers' => collect($this->getCareers())]);
    }

    public function show($id)
    {
        $career = collect($this->getCareers())->firstWhere('id', $id);
        
        if (!$career) {
            abort(404);
        }

        return view('student.careers.show', compact('career'));
    }
}
