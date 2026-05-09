<?php

namespace App\Http\Controllers;

use App\Services\SmsOtpService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => __('messages.account_deactivated')]);
            }

            // Teacher approval check
            if ($user->isTeacher() && !$user->isApprovedTeacher()) {
                Auth::logout();
                $message = match($user->status) {
                    'rejected' => __('messages.teacher_rejected'),
                    default    => __('messages.teacher_pending_approval'),
                };
                return back()->withErrors(['email' => $message]);
            }

            // Update login streak for students
            if ($user->isStudent()) {
                $user->updateStreak();
            }

            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email', 'unique:users'],
            'password'               => ['required', 'confirmed', Password::min(6)],
            'role'                   => ['required', 'in:student,teacher'],
            'class_level'            => ['required_if:role,student', 'nullable', 'string'],
            'subject_specialization' => ['required_if:role,teacher', 'nullable', 'string'],
            'phone'                  => ['nullable', 'string', 'max:15'],
        ]);

        $isTeacher = $validated['role'] === 'teacher';

        $user = User::create([
            'name'                   => $validated['name'],
            'email'                  => $validated['email'],
            'password'               => Hash::make($validated['password']),
            'role'                   => $validated['role'],
            'class_level'            => $validated['class_level'] ?? null,
            'subject_specialization' => $validated['subject_specialization'] ?? null,
            'phone'                  => $validated['phone'] ?? null,
            'is_active'              => true,
            'status'                 => $isTeacher ? 'pending' : null,
        ]);

        if ($isTeacher) {
            // Don't log in — redirect to login with message
            return redirect()->route('login')->with(
                'success',
                __('messages.teacher_registration_submitted')
            );
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->updateStreak();

        return $this->redirectToDashboard()->with('success', __('messages.welcome_user', ['name' => $user->name]));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', __('messages.logout_success'));
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => ['required', 'string', 'max:20']]);

        $phone = preg_replace('/\s+/', '', (string) $request->phone);

        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No account found with this phone number.'], 404);
        }

        $cooldownKey = 'otp_cooldown_' . $phone;
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another OTP.',
            ], 429);
        }

        $otp = (string) random_int(100000, 999999);
        Cache::put('otp_' . $phone, [
            'hash' => Hash::make($otp),
            'user_id' => $user->id,
        ], now()->addMinutes(5));
        Cache::put($cooldownKey, true, now()->addSeconds(30));

        // Send via Notification (Email + DB)
        $user->notify(new \App\Notifications\OtpNotification($otp));

        $sent = app(SmsOtpService::class)->send($phone, $otp);
        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to deliver OTP right now. Please try again shortly.',
            ], 503);
        }

        Log::info("OTP issued for {$phone}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'resend_after' => 30,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string'
        ]);

        $phone = preg_replace('/\s+/', '', (string) $request->phone);
        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || empty($cachedOtp['hash']) || !Hash::check((string) $request->otp, $cachedOtp['hash'])) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.'])->onlyInput('phone');
        }

        $user = User::where('phone', $phone)->first();
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            
            if ($user->isStudent()) {
                $user->updateStreak();
            }

            Cache::forget('otp_' . $phone);
            Cache::forget('otp_cooldown_' . $phone);
            return $this->redirectToDashboard();
        }

        return back()->withErrors(['phone' => 'User not found.']);
    }

    private function redirectToDashboard()
    {
        return match(Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }
}
