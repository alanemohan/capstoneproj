@extends('layouts.admin')

@section('admin-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment Reconciliation</h1>
            <p class="text-gray-500">Track and audit all course enrollments and payment statuses.</p>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600">₹{{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Pending Payments</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_count'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Successful Enrollments</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['paid_count'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Failed Transactions</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['failed_count'] }}</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.reconciliation.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by Transaction ID, Student, or Course..." 
                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="w-full md:w-48">
                <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <select name="refund_status" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Refund States</option>
                    <option value="none" {{ request('refund_status') == 'none' ? 'selected' : '' }}>No Refund</option>
                    <option value="requested" {{ request('refund_status') == 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="partial" {{ request('refund_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="full" {{ request('refund_status') == 'full' ? 'selected' : '' }}>Full</option>
                    <option value="rejected" {{ request('refund_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                Filter
            </button>
            <a href="{{ route('admin.reconciliation.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center px-2">
                Reset
            </a>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">Transaction ID</th>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Refund</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $enrollment->transaction_id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $enrollment->user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $enrollment->course->title }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">₹{{ number_format($enrollment->amount_paid, 2) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'paid' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-orange-100 text-orange-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                ][$enrollment->payment_status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                {{ ucfirst($enrollment->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $refundClasses = [
                                    'none' => 'bg-gray-100 text-gray-600',
                                    'requested' => 'bg-amber-100 text-amber-700',
                                    'partial' => 'bg-blue-100 text-blue-700',
                                    'full' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ][$enrollment->refund_status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $refundClasses }}">
                                {{ ucfirst($enrollment->refund_status ?? 'none') }}
                            </span>
                            @if($enrollment->refund_status === 'requested')
                                <div class="mt-2 text-xs text-gray-500 space-y-1">
                                    <p><span class="font-semibold">Requested:</span> ₹{{ number_format((float) $enrollment->refund_amount, 2) }}</p>
                                    @if($enrollment->refund_reason)
                                        <p class="line-clamp-2"><span class="font-semibold">Reason:</span> {{ $enrollment->refund_reason }}</p>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($enrollment->refund_status === 'requested')
                                <div class="space-y-2">
                                    <form method="POST" action="{{ route('admin.reconciliation.refund.approve', $enrollment) }}" class="flex items-center gap-2" data-no-loading>
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="refund_amount" min="0.01" max="{{ (float) $enrollment->amount_paid }}" step="0.01"
                                               value="{{ number_format((float) $enrollment->refund_amount, 2, '.', '') }}"
                                               class="w-28 rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reconciliation.refund.reject', $enrollment) }}" class="flex items-center gap-2" data-no-loading>
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="notes" placeholder="Optional note"
                                               class="w-28 rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">No action</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $enrollment->enrolled_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No transactions found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $enrollments->links() }}
        </div>
    </div>
</div>
@endsection
