@extends('layouts.admin')

@section('admin-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-gray-500">History of all administrative actions and system changes.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.audit-logs') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by action, model, or admin user..." 
                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                Search
            </button>
            <a href="{{ route('admin.audit-logs') }}" class="text-gray-500 hover:text-gray-700 flex items-center px-2">
                Reset
            </a>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Resource</th>
                        <th class="px-6 py-4">Changes</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                    {{ substr($log->user->name ?? '?', 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->model_type)
                                <span class="text-gray-500 text-xs block">{{ class_basename($log->model_type) }}</span>
                                <span class="text-gray-900">ID: {{ $log->model_id }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div x-data="{ open: false }">
                                <button @click="open = !open" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                    View Details
                                </button>
                                <div x-show="open" @click.away="open = false" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-cloak>
                                    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl overflow-hidden">
                                        <h3 class="text-lg font-bold mb-4">Audit Log Details: {{ $log->action }}</h3>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Old Values</h4>
                                                <pre class="bg-gray-50 p-3 rounded text-xs overflow-auto max-h-60">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">New Values</h4>
                                                <pre class="bg-gray-50 p-3 rounded text-xs overflow-auto max-h-60">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end">
                                            <button @click="open = false" class="bg-gray-900 text-white px-4 py-2 rounded-lg">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $log->ip_address }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
