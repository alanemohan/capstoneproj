@extends('layouts.admin')

@section('title', __('messages.complaints_title'))

@section('admin-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.complaints_title') }}</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">{{ __('messages.review_resolve') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-755 px-4.5 py-3 rounded-lg text-xs font-semibold animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">{{ __('messages.student') }}</th>
                        <th class="px-5 py-3">{{ __('messages.subject') }}</th>
                        <th class="px-5 py-3">{{ __('messages.message') }}</th>
                        <th class="px-5 py-3">{{ __('messages.status') }}</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($complaints as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $item->student->name ?? __('messages.unknown_user') }}</div>
                                <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $item->student->email ?? '' }}</div>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-800">{{ $item->subject }}</td>
                            <td class="px-5 py-4 text-slate-555 leading-relaxed max-w-xs truncate" title="{{ $item->message }}">{{ Str::limit($item->message, 50) }}</td>
                            <td class="px-5 py-4">
                                <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md border uppercase tracking-wider
                                    {{ $item->status === 'resolved' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 
                                      ($item->status === 'rejected' ? 'bg-red-50 border-red-250 text-red-750' : 'bg-amber-50 border-amber-250 text-amber-750') }}">
                                    {{ __('messages.' . $item->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.complaints.status', $item->id) }}" class="flex items-center justify-end gap-2">
                                    @csrf @method('PATCH')
                                    <select name="status" class="px-2.5 py-1.5 border border-slate-300 rounded-md text-[10px] font-bold uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 bg-white text-slate-700">
                                        <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                                        <option value="resolved" {{ $item->status === 'resolved' ? 'selected' : '' }}>{{ __('messages.resolved') }}</option>
                                        <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>{{ __('messages.rejected') }}</option>
                                    </select>
                                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-wider transition shadow-sm">
                                        {{ __('messages.update') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-semibold">{{ __('messages.no_complaints_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($complaints->hasPages())
            <div class="px-5 py-4 border-t border-slate-150">{{ $complaints->links() }}</div>
        @endif
    </div>
</div>
@endsection
