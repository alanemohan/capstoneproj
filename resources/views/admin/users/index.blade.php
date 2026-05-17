@extends('layouts.admin')

@section('title', 'Manage Users - Nabha Learning')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800" x-data="{ showAdd: false }">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Manage Users</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Administer, inspect, and update user credentials and accounts.</p>
        </div>
        <button @click="showAdd = !showAdd"
                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
            Add User
        </button>
    </div>

    <!-- Add User Form -->
    <div x-show="showAdd" x-cloak class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4 animate-slide-down">
        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-400">Add New User</h3>
        <form method="POST" action="{{ route('admin.users.store') }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-3" x-data="{ role: 'student' }">
            @csrf
            <input type="text" name="name" required placeholder="Full Name"
                   class="px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            <input type="email" name="email" required placeholder="Email Address"
                   class="px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            <input type="password" name="password" required placeholder="Password"
                   class="px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            
            <select name="role" x-model="role" required
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
            
            <div x-show="role === 'student'">
                <select name="class_level" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                    @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                        <option>{{ $class }}</option>
                    @endforeach
                </select>
            </div>
            
            <div x-show="role === 'teacher'" x-cloak>
                <select name="subject_specialization" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                    @foreach(['Mathematics','Science','English','Hindi','Social Studies'] as $s)
                        <option>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Create User
            </button>
        </form>
    </div>

    <!-- Filter -->
    <form method="GET" data-no-loading class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
               class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
        <select name="role" class="px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
            <option value="">All Roles</option>
            <option value="student" {{ request('role') === 'student' ? 'selected':'' }}>Students</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected':'' }}>Teachers</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected':'' }}>Admins</option>
        </select>
        <button type="submit" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">Filter</button>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Class/Subject</th>
                        <th class="px-5 py-3">Activity</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200" alt="">
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm leading-snug" style="font-family: var(--font-display);">{{ $user->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider
                                    {{ $user->role === 'admin' ? 'bg-slate-55 text-slate-600 border-slate-250' : ($user->role === 'teacher' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : 'bg-blue-50 text-blue-700 border-blue-250') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-700">
                                {{ $user->class_level ?? $user->subject_specialization ?? '—' }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-500">
                                {{ $user->quiz_attempts_count }} attempts | {{ $user->progress_reports_count }} lessons
                            </td>
                            <td class="px-5 py-4">
                                @if($user->status === 'pending')
                                    <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider bg-amber-50 text-amber-700 border-amber-250">
                                        Pending Approval
                                    </span>
                                @elseif($user->status === 'rejected')
                                    <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider bg-rose-50 text-rose-700 border-rose-250">
                                        Rejected
                                    </span>
                                @else
                                    <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : 'bg-red-50 text-red-705 border-red-250' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->status === 'pending')
                                        <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-md border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-md border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition shadow-sm">
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-md border transition shadow-sm
                                                           {{ $user->is_active ? 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                              onsubmit="return confirm('Delete user {{ $user->name }}?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-md transition shadow-sm">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-semibold">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-3">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
