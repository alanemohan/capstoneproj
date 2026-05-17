@extends('layouts.admin')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="pb-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Audit Logs</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">History of all administrative actions and system changes.</p>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.audit-logs') }}" method="GET" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by action, model, or admin user..." 
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Search
            </button>
            <a href="{{ route('admin.audit-logs') }}" class="text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-700 flex items-center px-2 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">Admin</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3">Resource</th>
                        <th class="px-5 py-3">Changes</th>
                        <th class="px-5 py-3">IP Address</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition" x-data="{ open: false }">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-150 text-orange-700 flex items-center justify-center font-bold text-xs">
                                    {{ substr($log->user->name ?? '?', 0, 1) }}
                                </div>
                                <span class="font-bold text-slate-900" style="font-family: var(--font-display);">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 bg-slate-50 border border-slate-200 text-slate-650 rounded text-[10px] font-mono font-bold tracking-tight">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($log->model_type)
                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">{{ class_basename($log->model_type) }}</span>
                                <span class="text-slate-800 font-semibold">ID: {{ $log->model_id }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div>
                                <button @click="open = true" class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-750 px-2.5 py-1.5 bg-orange-50 border border-orange-150 rounded-md transition shadow-sm">
                                    View Details
                                </button>
                                <div x-show="open" @click.away="open = false" class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4" x-cloak x-transition>
                                    <div class="bg-white rounded-xl border border-slate-200 max-w-2xl w-full p-6 shadow-2xl overflow-hidden relative">
                                        <button @click="open = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition text-sm">✕</button>
                                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-150 pb-3 mb-4">Audit Log Details: {{ $log->action }}</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Old Values</h4>
                                                <pre class="bg-slate-50 border border-slate-200 p-3 rounded-lg text-[10px] font-mono text-slate-700 overflow-auto max-h-60 leading-relaxed">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div>
                                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">New Values</h4>
                                                <pre class="bg-slate-50 border border-slate-200 p-3 rounded-lg text-[10px] font-mono text-slate-700 overflow-auto max-h-60 leading-relaxed">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-slate-150">
                                            <button @click="open = false" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider text-slate-500 border border-slate-300 hover:bg-slate-50 transition">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-500 font-mono font-semibold">{{ $log->ip_address }}</td>
                        <td class="px-5 py-4 text-slate-500 font-semibold">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-semibold">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
