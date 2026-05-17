@extends('layouts.admin')

@section('title', 'Teacher Approvals — Admin')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800" id="teacher-approvals-page">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-200 gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Teacher Approvals</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Review, approve, suspend, and manage teacher accounts.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @foreach(['pending' => ['label' => 'Pending', 'color' => 'bg-amber-50 border-amber-200 text-amber-700'],
                      'approved' => ['label' => 'Approved', 'color' => 'bg-emerald-50 border-emerald-205 text-emerald-700'],
                      'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-50 border-red-200 text-red-700'],
                      'all'      => ['label' => 'All', 'color' => 'bg-slate-50 border-slate-200 text-slate-600']] as $val => $info)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[10px] font-bold uppercase tracking-wider {{ $info['color'] }} shadow-sm">
                    <span>{{ $info['label'] }}</span>
                    <span class="font-extrabold">{{ $counts[$val] }}</span>
                </span>
            @endforeach
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div id="flash-success" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-755 rounded-lg px-4.5 py-3 text-xs font-semibold animate-fade-in">
            <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-705 rounded-lg px-4.5 py-3 text-xs font-semibold animate-fade-in">
            <svg class="w-4 h-4 flex-shrink-0 text-red-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Filters: Status Tabs + Search ── --}}
    <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row gap-3 items-center shadow-sm">
        {{-- Status Tabs --}}
        <div class="flex gap-2 flex-wrap flex-1">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All Teachers'] as $val => $label)
                <a href="{{ route('admin.teachers', ['status' => $val, 'search' => $search]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition border
                       {{ $status === $val
                           ? 'bg-orange-500 text-white border-orange-500 shadow-sm'
                           : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                    {{ $label }}
                    @if($val !== 'all' && $counts[$val] > 0)
                        <span class="ml-1 text-[9px] px-1.5 py-0.5 rounded-full font-black
                            {{ $val === 'pending' ? 'bg-amber-400 text-slate-900' : ($val === 'approved' ? 'bg-emerald-400 text-slate-900' : 'bg-red-400 text-white') }}">
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
                   class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
            @if($search)
                <a href="{{ route('admin.teachers', ['status' => $status]) }}"
                   class="bg-slate-100 hover:bg-slate-205 text-slate-500 border border-slate-300 px-3.5 py-2.5 rounded-lg text-xs font-bold transition shadow-sm" title="Clear search">✕</a>
            @endif
        </form>
    </div>

    {{-- ── Empty State ── --}}
    @if($teachers->isEmpty())
        <div class="bg-white rounded-xl py-20 text-center border border-slate-200 shadow-sm text-slate-400 text-xs font-semibold">
            <div class="text-3xl mb-3">👨‍🏫</div>
            <p>No teacher accounts with status "{{ $status }}"{{ $search ? ' matching "' . $search . '"' : '' }}.</p>
        </div>
    @else

    {{-- ── Teacher Table ── --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">Teacher</th>
                        <th class="px-5 py-3">Contact</th>
                        <th class="px-5 py-3">Qualification</th>
                        <th class="px-5 py-3">Registered</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Account</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @foreach($teachers as $teacher)
                        @php
                            $st = $teacher->status ?? 'approved';
                            $isActive = $teacher->is_active;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition {{ !$isActive ? 'opacity-60' : '' }}">
                            {{-- Teacher info --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                                         class="w-9 h-9 rounded-lg object-cover border border-slate-200">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm leading-snug" style="font-family: var(--font-display);">{{ $teacher->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $teacher->email }}</div>
                                        @if($teacher->subject_specialization)
                                            <div class="text-[9px] font-bold uppercase tracking-wider text-orange-600 mt-0.5">{{ $teacher->subject_specialization }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-5 py-4 font-semibold text-slate-700">
                                {{ $teacher->phone ?? '—' }}
                            </td>

                            {{-- Qualification --}}
                            <td class="px-5 py-4 font-semibold text-slate-700">
                                {{ $teacher->qualification ?? '—' }}
                            </td>

                            {{-- Registered --}}
                            <td class="px-5 py-4 text-slate-400 font-semibold">
                                {{ $teacher->created_at->format('d M Y') }}<br>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $teacher->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- Approval Status --}}
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider
                                    {{ $st === 'approved' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' :
                                       ($st === 'rejected' ? 'bg-red-50 border-red-250 text-red-700' : 'bg-amber-50 border-amber-250 text-amber-750') }}">
                                    {{ $st }}
                                </span>
                            </td>

                            {{-- Account Active --}}
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider
                                    {{ $isActive ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                                    {{ $isActive ? 'Active' : 'Suspended' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    {{-- Approve --}}
                                    @if($st !== 'approved')
                                        <form method="POST" action="{{ route('admin.teachers.approve', $teacher) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1.5 bg-emerald-50 border border-emerald-150 hover:bg-emerald-100 text-emerald-700 rounded-md transition shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject --}}
                                    @if($st !== 'rejected')
                                        <form method="POST" action="{{ route('admin.teachers.reject', $teacher) }}"
                                              onsubmit="return confirm('Reject {{ addslashes($teacher->name) }}?')" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1.5 bg-red-50 border border-red-150 hover:bg-red-100 text-red-700 rounded-md transition shadow-sm">
                                                Reject
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Suspend / Unsuspend --}}
                                    <form method="POST" action="{{ route('admin.teachers.suspend', $teacher) }}"
                                          onsubmit="return confirm('{{ $isActive ? 'Suspend' : 'Reactivate' }} {{ addslashes($teacher->name) }}?')" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1.5 rounded-md border transition shadow-sm
                                                    {{ $isActive
                                                        ? 'bg-yellow-50 text-yellow-750 border-yellow-250 hover:bg-yellow-100'
                                                        : 'bg-emerald-50 text-emerald-700 border-emerald-250 hover:bg-emerald-100' }}">
                                            {{ $isActive ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                          onsubmit="return confirm('PERMANENTLY DELETE {{ addslashes($teacher->name) }}? This cannot be undone.')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-[9px] font-bold uppercase tracking-wider bg-red-50 hover:bg-red-105 text-red-700 border border-red-155 px-2.5 py-1.5 rounded-md transition shadow-sm">
                                            Delete
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
        <div class="md:hidden divide-y divide-slate-150">
            @foreach($teachers as $teacher)
                @php $st = $teacher->status ?? 'approved'; @endphp
                <div class="p-4 space-y-3 {{ !$teacher->is_active ? 'opacity-60' : '' }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                             class="w-11 h-11 rounded-lg object-cover border border-slate-205">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-900 text-xs truncate" style="font-family: var(--font-display);">{{ $teacher->name }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold truncate mt-0.5">{{ $teacher->email }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $teacher->phone ?? 'No phone' }}</p>
                        </div>
                        <div class="flex flex-col gap-1.5 items-end shrink-0">
                            <span class="px-2 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider
                                {{ $st === 'approved' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' :
                                   ($st === 'rejected' ? 'bg-red-50 border-red-250 text-red-700' : 'bg-amber-50 border-amber-250 text-amber-755') }}">
                                {{ $st }}
                            </span>
                            <span class="text-[9px] font-bold px-2 py-0.5 border rounded-md uppercase tracking-wider {{ $teacher->is_active ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 'bg-slate-50 border-slate-205 text-slate-600' }}">
                                {{ $teacher->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-450 font-bold uppercase tracking-wider flex justify-between">
                        <span>{{ $teacher->subject_specialization ?? 'No specialization' }}</span>
                        <span>{{ $teacher->qualification ?? 'No qualification' }}</span>
                        <span>{{ $teacher->created_at->format('d M Y') }}</span>
                    </div>

                    <div class="flex gap-2 flex-wrap pt-2 border-t border-slate-150">
                        @if($st !== 'approved')
                            <form method="POST" action="{{ route('admin.teachers.approve', $teacher) }}" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg transition shadow-sm">Approve</button>
                            </form>
                        @endif
                        @if($st !== 'rejected')
                            <form method="POST" action="{{ route('admin.teachers.reject', $teacher) }}" class="flex-1"
                                  onsubmit="return confirm('Reject?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full py-2 bg-red-650 hover:bg-red-750 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg transition shadow-sm">Reject</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.teachers.suspend', $teacher) }}"
                              onsubmit="return confirm('{{ $teacher->is_active ? 'Suspend' : 'Activate' }} this teacher?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="py-2 px-3 text-[10px] font-bold uppercase tracking-wider rounded-lg transition border {{ $teacher->is_active ? 'bg-yellow-50 text-yellow-750 border-yellow-250 hover:bg-yellow-100' : 'bg-emerald-50 text-emerald-705 border-emerald-250 hover:bg-emerald-100' }}">
                                {{ $teacher->is_active ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                              onsubmit="return confirm('PERMANENTLY DELETE this teacher?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="py-2 px-3 bg-red-50 hover:bg-red-105 text-red-700 border border-red-155 text-[10px] font-bold uppercase tracking-wider rounded-lg transition shadow-sm">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($teachers->hasPages())
            <div class="px-5 py-4 border-t border-slate-150 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50">
                <p class="text-xs text-slate-500 font-semibold">
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
