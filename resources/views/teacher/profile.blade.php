@extends('layouts.teacher')

@section('title', 'My Profile - Nabha Learning')

@section('teacher-content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">

    <div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">My Profile</h1>
        <p class="text-xs text-gray-500 mt-1">View and update your account details.</p>
    </div>

    <!-- Profile Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center gap-4.5 mb-6 pb-5 border-b border-gray-200">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 class="w-14 h-14 rounded-lg object-cover ring-2 ring-emerald-500/10">
            <div>
                <h2 class="text-base font-bold text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md border border-emerald-100 uppercase tracking-wider">
                        Teacher
                    </span>
                    @php $st = $user->status ?? 'approved'; @endphp
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border uppercase tracking-wider
                        {{ $st === 'approved' ? 'bg-blue-50 text-blue-700 border-blue-100' :
                           ($st === 'rejected' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-yellow-50 text-yellow-700 border-yellow-100') }}">
                        {{ $st }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Read-only info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50/50 rounded-lg border border-gray-150 p-4">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-1">Email</p>
                <p class="text-xs font-semibold text-gray-800">{{ $user->email }}</p>
            </div>
            <div class="bg-gray-50/50 rounded-lg border border-gray-150 p-4">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-1">Specialization</p>
                <p class="text-xs font-semibold text-gray-800">{{ $user->subject_specialization ?? 'Not set' }}</p>
            </div>
            <div class="bg-gray-50/50 rounded-lg border border-gray-150 p-4">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-1">Phone</p>
                <p class="text-xs font-semibold text-gray-800">{{ $user->phone ?? 'Not set' }}</p>
            </div>
            <div class="bg-gray-50/50 rounded-lg border border-gray-150 p-4">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-1">Member Since</p>
                <p class="text-xs font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Edit form -->
        <form method="POST" action="{{ route('teacher.profile.update') }}">
            @csrf @method('PUT')
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-4">Edit Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800
                               @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                           placeholder="e.g., +91 98765 43210">
                    @error('phone') <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3 rounded-lg transition-all text-xs">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-4">Change Password</h3>

        <form method="POST" action="{{ route('teacher.profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Current Password *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800
                               @error('current_password') border-red-400 @enderror">
                    @error('current_password') <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">New Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800
                               @error('password') border-red-400 @enderror"
                           placeholder="Minimum 6 characters">
                    @error('password') <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold uppercase tracking-wider py-3 rounded-lg transition-all text-xs">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
