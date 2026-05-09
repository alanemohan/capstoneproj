@extends('layouts.admin')

@section('title', 'Notices & Announcements CMS')

@section('admin-content')
<div class="space-y-6" x-data="{ 
    showModal: false, 
    editMode: false,
    form: { id: '', title: '', content: '', target_class: [] },
    openCreate() {
        this.editMode = false;
        this.form = { id: '', title: '', content: '', target_class: [] };
        this.showModal = true;
    },
    openEdit(item) {
        this.editMode = true;
        this.form = { ...item, target_class: item.target_class ? (Array.isArray(item.target_class) ? item.target_class : JSON.parse(item.target_class)) : [] };
        this.showModal = true;
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notices & Announcements</h1>
            <p class="text-gray-500 text-sm mt-1">Manage global system announcements</p>
        </div>
        <button @click="openCreate()" class="bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition text-sm font-medium">
            + Post Notice
        </button>
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
                    <th class="px-6 py-4">Title</th>
                    <th class="px-6 py-4">Content</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($announcements as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $item->title }}</td>
                        <td class="px-6 py-4 truncate max-w-xs">{{ Str::limit($item->content, 50) }}</td>
                        <td class="px-6 py-4">{{ $item->created_at->format('d M, Y H:i') }}</td>
                        <td class="px-6 py-4 flex items-center justify-end gap-3">
                            <button @click="openEdit({{ json_encode($item) }})" class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $item->id) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No announcements found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($announcements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $announcements->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 mx-4 relative" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>
            <h2 class="text-xl font-bold mb-4" x-text="editMode ? 'Edit Notice' : 'Add Notice'"></h2>
            
            <form method="POST" :action="editMode ? `/admin/announcements/${form.id}` : '{{ route('admin.announcements.store') }}'" class="space-y-4">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" x-model="form.title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Class</label>
                    <select name="target_class[]" x-model="form.target_class" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm h-24">
                        <option value="All Classes">All Classes</option>
                        @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content" x-model="form.content" required rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-600 font-medium">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium" x-text="editMode ? 'Update' : 'Save'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
