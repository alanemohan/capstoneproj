@extends('layouts.admin')

@section('title', __('messages.complaints_title'))

@section('admin-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.complaints_title') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ __('messages.review_resolve') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4">{{ __('messages.student') }}</th>
                    <th class="px-6 py-4">{{ __('messages.subject') }}</th>
                    <th class="px-6 py-4">{{ __('messages.message') }}</th>
                    <th class="px-6 py-4">{{ __('messages.status') }}</th>
                    <th class="px-6 py-4 text-right">{{ __('messages.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($complaints as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $item->student->name ?? __('messages.unknown_user') }}</div>
                            <div class="text-xs text-gray-500">{{ $item->student->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $item->subject }}</td>
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $item->message }}">{{ Str::limit($item->message, 50) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $item->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 
                                  ($item->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ __('messages.' . $item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.complaints.status', $item->id) }}" class="flex items-center justify-end gap-2">
                                @csrf @method('PATCH')
                                <select name="status" class="px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                                    <option value="resolved" {{ $item->status === 'resolved' ? 'selected' : '' }}>{{ __('messages.resolved') }}</option>
                                    <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>{{ __('messages.rejected') }}</option>
                                </select>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-medium">
                                    {{ __('messages.update') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('messages.no_complaints_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($complaints->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $complaints->links() }}</div>
        @endif
    </div>
</div>
@endsection
