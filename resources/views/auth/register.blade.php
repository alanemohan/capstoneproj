@extends('layouts.app')

@section('title', __('messages.register') . ' - ' . __('messages.platform_name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-indigo-900 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="flex justify-end mb-3 gap-2 text-xs text-primary-100">
            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="hover:text-white">English</a>
            <span>|</span>
            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="hover:text-white">हिंदी</a>
            <span>|</span>
            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="hover:text-white">ਪੰਜਾਬੀ</a>
        </div>
        <div class="text-center mb-8 text-white">
            <a href="/" class="inline-flex items-center gap-2 mb-4">
                <span class="text-4xl">🎓</span>
                <span class="font-bold text-2xl">{{ __('messages.platform_name') }}</span>
            </a>
            <p class="text-primary-300">{{ __('messages.register_intro') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('messages.create_account') }}</h2>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4"
                  x-data="{ role: '{{ old('role', 'student') }}' }">
                @csrf

                {{-- Role selector (Robust with hidden radio) --}}
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <label class="cursor-pointer group">
                        <input type="radio" name="role" value="student" x-model="role" 
                               {{ old('role', 'student') === 'student' ? 'checked' : '' }} class="hidden">
                        <div :class="role==='student' ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500'"
                             class="p-4 border-2 rounded-2xl text-sm font-bold text-center transition group-hover:border-primary-400">
                            <div class="text-3xl mb-1">👨‍🎓</div>
                            {{ __('messages.i_am_student') }}
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="role" value="teacher" x-model="role"
                               {{ old('role') === 'teacher' ? 'checked' : '' }} class="hidden">
                        <div :class="role==='teacher' ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500'"
                             class="p-4 border-2 rounded-2xl text-sm font-bold text-center transition group-hover:border-emerald-400">
                            <div class="text-3xl mb-1">👨‍🏫</div>
                            {{ __('messages.i_am_teacher') }}
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.full_name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                           placeholder="{{ __('messages.full_name_placeholder') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.email_address') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                           placeholder="{{ __('messages.email_placeholder') }}">
                </div>

                {{-- Student field --}}
                <div x-show="role==='student'" x-cloak style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.your_class') }}</label>
                    <select name="class_level"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        <option value="">{{ __('messages.select_your_class') }}</option>
                        @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                            <option value="{{ $class }}" {{ old('class_level')===$class ? 'selected':'' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher fields --}}
                <div x-show="role==='teacher'" x-cloak style="display: none;">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.subject_specialization') }}</label>
                            <select name="subject_specialization"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                <option value="">{{ __('messages.select_your_subject') }}</option>
                                @foreach(['Mathematics','Science','English','Hindi','Social Studies','Physical Education'] as $subj)
                                    <option value="{{ $subj }}" {{ old('subject_specialization')===$subj ? 'selected':'' }}>{{ $subj }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Qualification <span class="text-gray-400 font-normal">(e.g. B.Ed, M.Sc, B.Tech)</span></label>
                            <input type="text" name="qualification" value="{{ old('qualification') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="Your highest qualification">
                        </div>
                    </div>
                </div>

                <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.phone_optional') }}</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                          placeholder="{{ __('messages.phone_placeholder') }}">
                </div>

                {{-- Password fields side-by-side with toggles --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.password') }}</label>
                        <div class="relative">
                            <input type="password" name="password" id="reg-password" required
                                   class="w-full px-4 py-3 pr-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="{{ __('messages.password_min_chars') }}">
                            <button type="button"
                                    onclick="togglePassword('reg-password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition"
                                    tabindex="-1" title="{{ __('messages.show_hide_password') }}">
                                <svg id="reg-password-eye-show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="reg-password-eye-hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.confirm_password') }}</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="reg-confirm" required
                                   class="w-full px-4 py-3 pr-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="{{ __('messages.repeat_password') }}">
                            <button type="button"
                                    onclick="togglePassword('reg-confirm', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition"
                                    tabindex="-1" title="{{ __('messages.show_hide_password') }}">
                                <svg id="reg-confirm-eye-show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="reg-confirm-eye-hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition shadow-md mt-2">
                    {{ __('messages.create_my_account') }} →
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-5">
                {{ __('messages.already_have_account') }}
                <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">{{ __('messages.sign_in_here') }}</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input  = document.getElementById(inputId);
    const isHide = input.type === 'password';
    input.type   = isHide ? 'text' : 'password';

    const showIcon = document.getElementById(inputId + '-eye-show');
    const hideIcon = document.getElementById(inputId + '-eye-hide');
    if (showIcon) showIcon.classList.toggle('hidden', isHide);
    if (hideIcon) hideIcon.classList.toggle('hidden', !isHide);
}

document.querySelectorAll('a[data-lang]').forEach((el) => {
    el.addEventListener('click', () => {
        localStorage.setItem('applocale', el.getAttribute('data-lang'));
    });
});
</script>
@endsection
