@extends('layouts.student')

@section('title', 'My Profile - Nabha Learning')

@section('student-content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="animate-fade-in">
        <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.my_profile') }}</h1>
        <p class="text-xs text-white/40 mt-1">{{ __('View and update your account details') }}</p>
    </div>

    <!-- Profile Info Card -->
    <div class="glass-card p-6 glow-violet animate-scale-in">
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-white/[0.06]">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 class="w-16 h-16 rounded-2xl object-cover ring-2 ring-violet-500/20">
            <div>
                <h2 class="text-base font-bold text-white/90" style="font-family: var(--font-display);">{{ $user->name }}</h2>
                <p class="text-xs text-white/50">{{ $user->email }}</p>
                <span class="inline-block mt-2 px-2.5 py-0.5 bg-violet-500/15 text-violet-300 text-[10px] rounded-md font-bold uppercase tracking-wider">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        <!-- Read-only info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
            <div class="bg-white/[0.02] border border-white/[0.04] rounded-xl p-4">
                <p class="text-[10px] text-white/35 font-semibold uppercase tracking-wider mb-1">Email</p>
                <p class="text-sm font-medium text-white/90">{{ $user->email }}</p>
            </div>
            <div class="bg-white/[0.02] border border-white/[0.04] rounded-xl p-4">
                <p class="text-[10px] text-white/35 font-semibold uppercase tracking-wider mb-1">Class Level</p>
                <p class="text-sm font-medium text-white/90">{{ $user->class_level ?? 'Not set' }}</p>
            </div>
            <div class="bg-white/[0.02] border border-white/[0.04] rounded-xl p-4">
                <p class="text-[10px] text-white/35 font-semibold uppercase tracking-wider mb-1">School</p>
                <p class="text-sm font-medium text-white/90">{{ $user->school ?? 'Not set' }}</p>
            </div>
            @if($user->streak_count > 0)
                <div class="bg-orange-500/10 border border-orange-500/15 rounded-xl p-4">
                    <p class="text-[10px] text-orange-400 font-semibold uppercase tracking-wider mb-1">Daily Streak</p>
                    <p class="text-sm font-bold text-orange-300">🔥 {{ $user->streak_count }} day{{ $user->streak_count !== 1 ? 's' : '' }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
            <div class="bg-cyan-500/10 border border-cyan-500/15 rounded-xl p-4">
                <p class="text-[10px] text-cyan-400 font-semibold uppercase tracking-wider mb-1">{{ __('Assigned Mentor') }}</p>
                @if($user->assignedMentor)
                    <p class="text-sm font-bold text-cyan-300">{{ $user->assignedMentor->name }}</p>
                    <p class="text-[10px] text-cyan-200 mt-0.5">{{ $user->assignedMentor->subject_specialization ?? __('General Mentor') }}</p>
                    <p class="text-[10px] text-cyan-400 mt-0.5">{{ $user->assignedMentor->email }}</p>
                @else
                    <p class="text-sm text-cyan-300/60">{{ __('Not assigned yet') }}</p>
                @endif
            </div>
            <div class="bg-violet-500/10 border border-violet-500/15 rounded-xl p-4">
                <p class="text-[10px] text-violet-400 font-semibold uppercase tracking-wider mb-1">{{ __('Recent Notifications') }}</p>
                @if($user->portalNotifications->isEmpty())
                    <p class="text-sm text-violet-300/60">{{ __('No notifications yet') }}</p>
                @else
                    <p class="text-sm font-semibold text-violet-300 line-clamp-1">{{ $user->portalNotifications->first()->title }}</p>
                    <p class="text-[10px] text-violet-400 mt-0.5 line-clamp-2">{{ $user->portalNotifications->first()->message }}</p>
                @endif
            </div>
        </div>

        <!-- Edit form -->
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <h3 class="text-xs font-bold text-white/50 mb-4 uppercase tracking-wider">Edit Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/25 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm
                               @error('name') border-red-500/30 @enderror">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/25 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm"
                           placeholder="e.g., +91 98765 43210">
                    @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Profile Photo</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                           class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/60 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm">
                    @error('avatar') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Gender</label>
                        <select name="gender" class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm">
                            <option value="" class="bg-gray-900 text-white">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }} class="bg-gray-900 text-white">Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }} class="bg-gray-900 text-white">Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }} class="bg-gray-900 text-white">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Preferred Language</label>
                        <select name="language" class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm">
                            <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }} class="bg-gray-900 text-white">English</option>
                            <option value="hi" {{ old('language', $user->language) === 'hi' ? 'selected' : '' }} class="bg-gray-900 text-white">Hindi</option>
                            <option value="pa" {{ old('language', $user->language) === 'pa' ? 'selected' : '' }} class="bg-gray-900 text-white">Punjabi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Class Level</label>
                        <select name="class_level" class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm">
                            <option value="" class="bg-gray-900 text-white">Select Class</option>
                            @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $c)
                                <option value="{{ $c }}" {{ old('class_level', $user->class_level) === $c ? 'selected' : '' }} class="bg-gray-900 text-white">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Section</label>
                        <input type="text" name="section" value="{{ old('section', $user->section) }}"
                               class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/25 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm"
                               placeholder="e.g., A">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">School</label>
                    <input type="text" name="school" value="{{ old('school', $user->school) }}"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/25 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm"
                           placeholder="e.g., Nabha Public School">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Address</label>
                    <textarea name="address" rows="2"
                              class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/25 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm resize-none"
                              placeholder="Your full address">{{ old('address', $user->address) }}</textarea>
                </div>
                
                <div class="pt-2">
                    <label class="flex items-start gap-3 p-4 bg-white/[0.02] border border-white/[0.06] rounded-xl cursor-pointer hover:bg-white/[0.04] transition">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="low_data_mode" value="1" {{ $user->low_data_mode ? 'checked' : '' }}
                                   class="w-5 h-5 text-violet-500 border-white/20 bg-white/[0.04] rounded focus:ring-violet-500/30">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-white/80">Enable Low Data Mode</span>
                            <span class="text-xs text-white/40 mt-1">Hides heavy videos and shows text/PDFs instead to save bandwidth. Recommended for slow connections.</span>
                        </div>
                    </label>
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-3 rounded-xl font-semibold transition text-sm shadow-lg shadow-violet-500/20 hover:shadow-violet-500/30 hover:-translate-y-0.5 active:translate-y-0">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="glass-card p-6 glow-violet animate-scale-in stagger-2">
        <h3 class="text-sm font-semibold text-white/80 uppercase tracking-wider mb-5" style="font-family: var(--font-display);">Change Password</h3>

        <form method="POST" action="{{ route('student.profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Current Password *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm
                               @error('current_password') border-red-500/30 @enderror">
                    @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">New Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm
                               @error('password') border-red-500/30 @enderror"
                           placeholder="Minimum 6 characters">
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/50 mb-2 uppercase tracking-wider">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-sm">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-white/[0.08] hover:bg-white/[0.12] border border-white/[0.08] text-white py-3 rounded-xl font-semibold transition text-sm">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
