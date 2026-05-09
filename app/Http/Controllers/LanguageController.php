<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    public function switchLang(Request $request, $lang)
    {
        $allowed = ['en', 'hi', 'pa'];
        if (in_array($lang, $allowed, true)) {
            session(['locale' => $lang, 'applocale' => $lang]);
            if (Auth::check()) {
                User::whereKey(Auth::id())->update(['language' => $lang]);
            }
        }
        return back();
    }
}
