@extends('layouts.admin')

@section('title', 'Teacher Approvals — Admin')

@section('admin-content')
<div class="space-y-6" id="teacher-approvals-page">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Teacher Approvals</h1>
            <p class="text-gray-400 text-sm mt-0.5">Review, approve, suspend and manage teacher accounts</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @foreach(['pending' => ['label' => 'Pending', 'color' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30'],
                      'approved' => ['label' => 'Approved', 'color' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'],
                      'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-500/20 text-red-300 border-red-500/30'],
                      'all'      => ['label' => 'All', 'color' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30']] as $val => $info)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-bold {{ $info['color'] }}">
                    <span>{{ $info['label'] }}</span>
                    <span class="font-extrabold">{{ $counts[$val] }}</span>
                </span>
            @endforeach
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div id="flash-success" class="flex items-center gap-3 bg-emerald-900/40 border border-emerald-500/40 text-emerald-300 rounded-xl px-5 py-3 text-sm font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-900/40 border border-red-500/40 text-red-300 rounded-xl px-5 py-3 text-sm font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Filters: Status Tabs + Search ── --}}
    <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-4 flex flex-col sm:flex-row gap-3 items-center">
        {{-- Status Tabs --}}
        <div class="flex gap-2 flex-wrap flex-1">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All Teachers'] as $val => $label)
                <a href="{{ route('admin.teachers', ['status' => $val, 'search' => $search]) }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold transition border
                       {{ $status === $val
                           ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-500/20'
                           : 'bg-gray-700/50 border-gray-600 text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ $label }}
                    @if($val !== 'all' && $counts[$val] > 0)
                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full
                            {{ $val === 'pending' ? 'bg-yellow-400 text-gray-900' : ($val === 'approved' ? 'bg-emerald-400 text-gray-900' : 'bg-red-400 text-white') }}">
                            {{ $counts[$val] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.teachers') }}" class="flex gap-2 w-full sm:w-72">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search name, email, phone…"
                   class="flex-1 bg-gray-700 border border-gray-600 text-gray-100 placeholder-gray-400 text-sm px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
            @if($search)
                <a href="{{ route('admin.teachers', ['status' => $status]) }}"
                   class="bg-gray-600 hover:bg-gray-500 text-gray-200 px-3 py-2.5 rounded-xl text-sm transition" title="Clear search">✕</a>
            @endif
        </form>
    </div>

    {{-- ── Empty State ── --}}
    @if($teachers->isEmpty())
        <div class="bg-gray-800/40 border border-gray-700 rounded-2xl py-20 text-center">
            <div class="text-5xl mb-4">👨‍🏫</div>
            <p class="text-gray-400 text-sm">No teacher accounts with status "{{ $status }}"{{ $search ? ' matching "' . $search . '"' : '' }}.</p>
        </div>
    @else

    {{-- ── Teacher Table ── --}}
    <div class="bg-gray-800/60 border border-gray-700 rounded-2xl overflow-hidden">

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-900/60 border-b border-gray-700">
                        <th class="text-left px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Teacher</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Contact</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Qualification</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Registered</th>
                        <th class="text-center px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="text-center px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Account</th>
                        <th class="text-center px-5 py-4 font-semibold text-gray-400 uppercase text-xs tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/60">
                    @foreach($teachers as $teacher)
                        @php
                            $st = $teacher->status ?? 'approved';
                            $isActive = $teacher->is_active;
                        @endphp
                        <tr class="hover:bg-gray-700/30 transition-colors group {{ !$isActive ? 'opacity-60' : '' }}">
                            {{-- Teacher info --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                                         class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-600">
                                    <div>
                                        <div class="font-semibold text-gray-100">{{ $teacher->name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $teacher->email }}</div>
                                        @if($teacher->subject_specialization)
                                            <div class="text-xs text-indigo-400 mt-0.5">{{ $teacher->subject_specialization }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-5 py-4 text-gray-300">
                                {{ $teacher->phone ?? '—' }}
                            </td>

                            {{-- Qualification --}}
                            <td class="px-5 py-4 text-gray-300">
                                {{ $teacher->qualification ?? '—' }}
                            </td>

                            {{-- Registered --}}
                            <td class="px-5 py-4 text-gray-400 text-xs">
                                {{ $teacher->created_at->format('d M Y') }}<br>
                                <span class="text-gray-500">{{ $teacher->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- Approval Status --}}
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ $st === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                       ($st === 'rejected' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30') }}">
                                    {{ ucfirst($st) }}
                                </span>
                            </td>

                            {{-- Account Active --}}
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ $isActive ? 'bg-emerald-500/20 text-emerald-300' : 'bg-gray-500/20 text-gray-400' }}">
                                    {{ $isActive ? 'Active' : 'Suspended' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    {{-- Approve --}}
                                    @if($st !== 'approved')
                                        <form method="POST" action="{{ route('admin.teachers.approve', $teacher) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg transition font-semibold">
                                                ✓ Approve
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject --}}
                                    @if($st !== 'rejected')
                                        <form method="POST" action="{{ route('admin.teachers.reject', $teacher) }}"
                                              onsubmit="return confirm('Reject {{ addslashes($teacher->name) }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs rounded-lg transition font-semibold">
                                                ✕ Reject
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Suspend / Unsuspend --}}
                                    <form method="POST" action="{{ route('admin.teachers.suspend', $teacher) }}"
                                          onsubmit="return confirm('{{ $isActive ? 'Suspend' : 'Reactivate' }} {{ addslashes($teacher->name) }}?')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs rounded-lg transition font-semibold
                                                    {{ $isActive
                                                        ? 'bg-yellow-600/80 hover:bg-yellow-500 text-white'
                                                        : 'bg-emerald-700/80 hover:bg-emerald-600 text-white' }}">
                                            {{ $isActive ? '🔒 Suspend' : '🔓 Activate' }}
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                          onsubmit="return confirm('PERMANENTLY DELETE {{ addslashes($teacher->name) }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-gray-700 hover:bg-red-900 text-gray-300 hover:text-red-300 text-xs rounded-lg transition font-semibold">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: stacked cards --}}
        <div class="md:hidden divide-y divide-gray-700">
            @foreach($teachers as $teacher)
                @php $st = $teacher->status ?? 'approved'; @endphp
                <div class="p-4 space-y-3 {{ !$teacher->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                             class="w-11 h-11 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-600">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-100 text-sm truncate">{{ $teacher->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $teacher->email }}</p>
                            <p class="text-xs text-gray-500">{{ $teacher->phone ?? 'No phone' }}</p>
                        </div>
                        <div class="flex flex-col gap-1 items-end">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $st === 'approved' ? 'bg-emerald-500/20 text-emerald-300' :
                                   ($st === 'rejected' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300') }}">
                                {{ ucfirst($st) }}
                            </span>
                            <span class="text-[10px] {{ $teacher->is_active ? 'text-emerald-400' : 'text-gray-500' }}">
                                {{ $teacher->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-gray-400 flex justify-between">
                        <span>{{ $teacher->subject_specialization ?? 'No specialization' }}</span>
                        <span>{{ $teacher->qualification ?? 'No qualification' }}</span>
                        <span>{{ $teacher->created_at->format('d M Y') }}</span>
                    </div>

                    <div class="flex gap-2 flex-wrap">
                        @if($st !== 'approved')
                            <form method="POST" action="{{ route('admin.teachers.approve', $teacher) }}" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-2 bg-emerald-600 text-white text-xs rounded-lg font-semibold">✓ Approve</button>
                            </form>
                        @endif
                        @if($st !== 'rejected')
                            <form method="POST" action="{{ route('admin.teachers.reject', $teacher) }}" class="flex-1"
                                  onsubmit="return confirm('Reject?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-2 bg-red-600 text-white text-xs rounded-lg font-semibold">✕ Reject</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.teachers.suspend', $teacher) }}"
                              onsubmit="return confirm('{{ $teacher->is_active ? 'Suspend' : 'Activate' }} this teacher?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="py-2 px-3 text-xs rounded-lg font-semibold {{ $teacher->is_active ? 'bg-yellow-600 text-white' : 'bg-emerald-700 text-white' }}">
                                {{ $teacher->is_active ? '🔒' : '🔓' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                              onsubmit="return confirm('PERMANENTLY DELETE this teacher?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="py-2 px-3 bg-gray-700 hover:bg-red-900 text-gray-300 text-xs rounded-lg">🗑️</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($teachers->hasPages())
            <div class="px-5 py-4 border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-900/30">
                <p class="text-sm text-gray-400">
                    Showing {{ $teachers->firstItem() }}–{{ $teachers->lastItem() }} of {{ $teachers->total() }} teachers
                </p>
                <div class="text-sm">{{ $teachers->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
    @endif
</div>

<script>
// Auto-hide flash message after 4 seconds
setTimeout(() => {
    const flash = document.getElementById('flash-success');
    if (flash) flash.style.transition = 'opacity 0.5s', flash.style.opacity = '0', setTimeout(() => flash.remove(), 500);
}, 4000);
</script>
@endsection
