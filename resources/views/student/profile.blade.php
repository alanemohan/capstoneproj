@extends('layouts.student')

@section('title', 'My Profile - Nabha Learning')

@section('student-content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.my_profile') }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ __('View and update your account details') }}</p>
    </div>

    <!-- Profile Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 class="w-16 h-16 rounded-full object-cover">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 bg-primary-100 text-primary-700 text-xs rounded-full font-medium">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        <!-- Read-only info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Email</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->email }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Class Level</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->class_level ?? 'Not set' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">School</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->school ?? 'Not set' }}</p>
            </div>
            @if($user->streak_count > 0)
                <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                    <p class="text-xs text-orange-600 mb-1">Daily Streak</p>
                    <p class="text-sm font-bold text-orange-700">🔥 {{ $user->streak_count }} day{{ $user->streak_count !== 1 ? 's' : '' }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                <p class="text-xs text-blue-600 mb-1">{{ __('Assigned Mentor') }}</p>
                @if($user->assignedMentor)
                    <p class="text-sm font-semibold text-blue-800">{{ $user->assignedMentor->name }}</p>
                    <p class="text-xs text-blue-700 mt-0.5">{{ $user->assignedMentor->subject_specialization ?? __('General Mentor') }}</p>
                    <p class="text-xs text-blue-700 mt-0.5">{{ $user->assignedMentor->email }}</p>
                @else
                    <p class="text-sm text-blue-700">{{ __('Not assigned yet') }}</p>
                @endif
            </div>
            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                <p class="text-xs text-indigo-600 mb-1">{{ __('Recent Notifications') }}</p>
                @if($user->portalNotifications->isEmpty())
                    <p class="text-sm text-indigo-700">{{ __('No notifications yet') }}</p>
                @else
                    <p class="text-sm font-semibold text-indigo-800 line-clamp-1">{{ $user->portalNotifications->first()->title }}</p>
                    <p class="text-xs text-indigo-700 mt-0.5 line-clamp-2">{{ $user->portalNotifications->first()->message }}</p>
                @endif
            </div>
        </div>

        <!-- Edit form -->
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Edit Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm
                               @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm"
                           placeholder="e.g., +91 98765 43210">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Profile Photo</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                        <select name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Preferred Language</label>
                        <select name="language" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
                            <option value="hi" {{ old('language', $user->language) === 'hi' ? 'selected' : '' }}>Hindi</option>
                            <option value="pa" {{ old('language', $user->language) === 'pa' ? 'selected' : '' }}>Punjabi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Class Level</label>
                        <select name="class_level" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <option value="">Select Class</option>
                            @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $c)
                                <option value="{{ $c }}" {{ old('class_level', $user->class_level) === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Section</label>
                        <input type="text" name="section" value="{{ old('section', $user->section) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm"
                               placeholder="e.g., A">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                    <textarea name="address" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm resize-none"
                              placeholder="Your full address">{{ old('address', $user->address) }}</textarea>
                </div>
                
                <div class="pt-2">
                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="low_data_mode" value="1" {{ $user->low_data_mode ? 'checked' : '' }}
                                   class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">Enable Low Data Mode</span>
                            <span class="text-xs text-gray-500 mt-1">Hides heavy videos and shows text/PDFs instead to save bandwidth. Recommended for slow connections.</span>
                        </div>
                    </label>
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 rounded-xl font-semibold transition text-sm">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Certificates Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-800">My Certificates</h3>
            <span class="bg-indigo-100 text-indigo-700 text-xs px-2.5 py-0.5 rounded-full font-medium">
                {{ $completedCourses->count() }} Earned
            </span>
        </div>
        
        @if($completedCourses->isEmpty())
            <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <p class="text-sm text-gray-500">You haven't earned any certificates yet.</p>
                <p class="text-xs text-gray-400 mt-1">Complete a course 100% to generate one!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($completedCourses as $course)
                    <div class="border border-indigo-100 bg-indigo-50/30 rounded-xl p-4 flex flex-col items-center text-center hover:bg-indigo-50/50 transition cursor-pointer">
                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-1" title="{{ $course->title }}">{{ $course->title }}</h4>
                        <p class="text-xs text-gray-500 mb-3">Completed</p>
                        <button onclick="alert('Downloading Certificate for {{ $course->title }}...')" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg transition w-full font-medium">
                            Download PDF
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-5">Change Password</h3>

        <form method="POST" action="{{ route('student.profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm
                               @error('current_password') border-red-400 @enderror">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm
                               @error('password') border-red-400 @enderror"
                           placeholder="Minimum 6 characters">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-900 text-white py-3 rounded-xl font-semibold transition text-sm">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
