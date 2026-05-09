@extends('layouts.admin')

@section('title', __('messages.scholarships_cms'))

@php($confirmActionText = __('messages.confirm_action'))

@section('admin-content')
<div class="space-y-6" x-data="{ 
    showModal: false, 
    editMode: false,
    activeTab: 'en',
    form: {
        id: '', title: '', title_hi: '', title_pa: '', 
        amount: '', amount_hi: '', amount_pa: '',
        deadline: '', 
        description: '', description_hi: '', description_pa: '',
        eligibility_criteria: '', eligibility_criteria_hi: '', eligibility_criteria_pa: '',
        url: ''
    },
    openCreate() {
        this.editMode = false;
        this.activeTab = 'en';
        this.form = { 
            id: '', title: '', title_hi: '', title_pa: '', 
            amount: '', amount_hi: '', amount_pa: '',
            deadline: '', 
            description: '', description_hi: '', description_pa: '',
            eligibility_criteria: '', eligibility_criteria_hi: '', eligibility_criteria_pa: '',
            url: '' 
        };
        this.showModal = true;
    },
    openEdit(item) {
        this.editMode = true;
        this.activeTab = 'en';
        // Handle date formatting for input type=date
        let formattedItem = { ...item };
        if (formattedItem.deadline) {
            formattedItem.deadline = formattedItem.deadline.split('T')[0];
        }
        this.form = formattedItem;
        this.showModal = true;
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.scholarships_cms') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ __('messages.manage_scholarships_subtitle') }}</p>
        </div>
        <button @click="openCreate()" class="bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition text-sm font-medium">
            + {{ __('messages.add_scholarship') }}
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
                    <th class="px-6 py-4">{{ __('messages.title_en') }}</th>
                    <th class="px-6 py-4">{{ __('messages.amount') }}</th>
                    <th class="px-6 py-4">{{ __('messages.translations') }}</th>
                    <th class="px-6 py-4">{{ __('messages.deadline') }}</th>
                    <th class="px-6 py-4 text-right">{{ __('messages.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($scholarships as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $item->title }}</td>
                        <td class="px-6 py-4">{{ $item->amount }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->title_hi ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">HI</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->title_pa ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">PA</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->deadline)->format('d M, Y') }}</td>
                        <td class="px-6 py-4 flex items-center justify-end gap-3">
                            <button @click="openEdit({{ json_encode($item) }})" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ __('messages.edit') }}</button>
                            <form method="POST" action="{{ route('admin.scholarships.destroy', $item->id) }}" onsubmit="return confirm('{{ $confirmActionText }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('messages.no_scholarships_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($scholarships->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $scholarships->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 mx-4 relative max-h-[90vh] overflow-y-auto" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>
            <h2 class="text-xl font-bold mb-4" x-text="editMode ? '{{ __('messages.edit_scholarship') }}' : '{{ __('messages.add_scholarship') }}'"></h2>
            
            <!-- Language Tabs -->
            <div class="flex border-b mb-4">
                <button @click="activeTab = 'en'" :class="activeTab === 'en' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 font-medium text-sm">{{ __('messages.lang_english') }}</button>
                <button @click="activeTab = 'hi'" :class="activeTab === 'hi' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 font-medium text-sm">{{ __('messages.lang_hindi') }}</button>
                <button @click="activeTab = 'pa'" :class="activeTab === 'pa' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 font-medium text-sm">{{ __('messages.lang_punjabi') }}</button>
            </div>

            <form method="POST" :action="editMode ? `/admin/scholarships/${form.id}` : '{{ route('admin.scholarships.store') }}'" class="space-y-4">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <!-- English Fields -->
                <div x-show="activeTab === 'en'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.title_en') }}</label>
                        <input type="text" name="title" x-model="form.title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.amount_en') }}</label>
                        <input type="text" name="amount" x-model="form.amount" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.description_en') }}</label>
                        <textarea name="description" x-model="form.description" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.eligibility_en') }}</label>
                        <textarea name="eligibility_criteria" x-model="form.eligibility_criteria" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                </div>

                <!-- Hindi Fields -->
                <div x-show="activeTab === 'hi'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.title_hi') }}</label>
                        <input type="text" name="title_hi" x-model="form.title_hi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.amount_hi') }}</label>
                        <input type="text" name="amount_hi" x-model="form.amount_hi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.description_hi') }}</label>
                        <textarea name="description_hi" x-model="form.description_hi" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.eligibility_hi') }}</label>
                        <textarea name="eligibility_criteria_hi" x-model="form.eligibility_criteria_hi" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                </div>

                <!-- Punjabi Fields -->
                <div x-show="activeTab === 'pa'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.title_pa') }}</label>
                        <input type="text" name="title_pa" x-model="form.title_pa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.amount_pa') }}</label>
                        <input type="text" name="amount_pa" x-model="form.amount_pa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.description_pa') }}</label>
                        <textarea name="description_pa" x-model="form.description_pa" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.eligibility_pa') }}</label>
                        <textarea name="eligibility_criteria_pa" x-model="form.eligibility_criteria_pa" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.deadline') }}</label>
                        <input type="date" name="deadline" x-model="form.deadline" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.official_url_optional') }}</label>
                        <input type="url" name="url" x-model="form.url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-600 font-medium">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium" x-text="editMode ? '{{ __('messages.update') }}' : '{{ __('messages.save') }}'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
