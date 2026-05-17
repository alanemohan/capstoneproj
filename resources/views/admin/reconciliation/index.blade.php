@extends('layouts.admin')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="pb-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Payment Reconciliation</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Track and audit all course enrollments and payment statuses.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Revenue</p>
            <p class="text-lg font-extrabold text-emerald-600 mt-1 tabular-nums">₹{{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Pending Payments</p>
            <p class="text-lg font-extrabold text-orange-600 mt-1 tabular-nums">{{ $stats['pending_count'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Successful Enrollments</p>
            <p class="text-lg font-extrabold text-blue-600 mt-1 tabular-nums">{{ $stats['paid_count'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Failed Transactions</p>
            <p class="text-lg font-extrabold text-red-650 mt-1 tabular-nums">{{ $stats['failed_count'] }}</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.reconciliation.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by Transaction ID, Student, or Course..." 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="refund_status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                    <option value="">All Refund States</option>
                    <option value="none" {{ request('refund_status') == 'none' ? 'selected' : '' }}>No Refund</option>
                    <option value="requested" {{ request('refund_status') == 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="partial" {{ request('refund_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="full" {{ request('refund_status') == 'full' ? 'selected' : '' }}>Full</option>
                    <option value="rejected" {{ request('refund_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Filter
            </button>
            <a href="{{ route('admin.reconciliation.index') }}" class="text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-700 flex items-center px-2 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">Transaction ID</th>
                        <th class="px-5 py-3">Student</th>
                        <th class="px-5 py-3">Course</th>
                        <th class="px-5 py-3">Amount</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Refund</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-4 font-mono text-[10px] text-slate-500">{{ $enrollment->transaction_id }}</td>
                        <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">{{ $enrollment->user->name }}</td>
                        <td class="px-5 py-4 text-slate-650 font-semibold">{{ $enrollment->course->title }}</td>
                        <td class="px-5 py-4 font-extrabold text-slate-900 tabular-nums">₹{{ number_format($enrollment->amount_paid, 2) }}</td>
                        <td class="px-5 py-4">
                            @php
                                $statusClasses = [
                                    'paid' => 'bg-emerald-50 border-emerald-250 text-emerald-700',
                                    'pending' => 'bg-orange-50 border-orange-250 text-orange-700',
                                    'failed' => 'bg-red-50 border-red-250 text-red-700',
                                ][$enrollment->payment_status] ?? 'bg-slate-50 border-slate-200 text-slate-600';
                            @endphp
                            <span class="px-2.5 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider {{ $statusClasses }}">
                                {{ $enrollment->payment_status }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $refundClasses = [
                                    'none' => 'bg-slate-50 border-slate-200 text-slate-605',
                                    'requested' => 'bg-amber-50 border-amber-250 text-amber-750',
                                    'partial' => 'bg-blue-50 border-blue-250 text-blue-700',
                                    'full' => 'bg-emerald-50 border-emerald-250 text-emerald-700',
                                    'rejected' => 'bg-red-50 border-red-250 text-red-700',
                                ][$enrollment->refund_status] ?? 'bg-slate-50 border-slate-200 text-slate-605';
                            @endphp
                            <span class="px-2.5 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider {{ $refundClasses }}">
                                {{ $enrollment->refund_status ?? 'none' }}
                            </span>
                            @if($enrollment->refund_status === 'requested')
                                <div class="mt-2 text-[10px] text-slate-500 space-y-0.5 font-semibold">
                                    <p><span class="text-slate-400">Requested:</span> ₹{{ number_format((float) $enrollment->refund_amount, 2) }}</p>
                                    @if($enrollment->refund_reason)
                                        <p class="line-clamp-2"><span class="text-slate-400">Reason:</span> {{ $enrollment->refund_reason }}</p>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($enrollment->refund_status === 'requested')
                                <div class="space-y-2">
                                    <form method="POST" action="{{ route('admin.reconciliation.refund.approve', $enrollment) }}" class="flex items-center gap-1.5" data-no-loading>
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="refund_amount" min="0.01" max="{{ (float) $enrollment->amount_paid }}" step="0.01"
                                               value="{{ number_format((float) $enrollment->refund_amount, 2, '.', '') }}"
                                               class="w-24 px-2 py-1.5 border border-slate-350 rounded-md text-[10px] font-bold focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                                        <button type="submit" class="px-2.5 py-1.5 rounded-md bg-emerald-600 text-white text-[9px] font-bold uppercase tracking-wider hover:bg-emerald-700 transition shadow-sm shrink-0">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reconciliation.refund.reject', $enrollment) }}" class="flex items-center gap-1.5" data-no-loading>
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="notes" placeholder="Notes"
                                               class="w-24 px-2 py-1.5 border border-slate-355 rounded-md text-[10px] font-bold focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                                        <button type="submit" class="px-2.5 py-1.5 rounded-md bg-red-650 text-white text-[9px] font-bold uppercase tracking-wider hover:bg-red-750 transition shadow-sm shrink-0">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 font-semibold">No action</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-500 font-semibold tabular-nums">{{ $enrollment->enrolled_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 font-semibold">
                            No transactions found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">
            {{ $enrollments->links() }}
        </div>
    </div>
</div>
@endsection
