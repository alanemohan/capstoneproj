@extends('layouts.teacher')

@section('title', 'Add Lesson — ' . $course->title)

@section('teacher-content')
<div class="max-w-3xl mx-auto animate-fade-in" x-data="lessonBuilder()">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.courses.show', $course) }}"
           class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">
            ← Back
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Add Lesson</h1>
            <p class="text-xs text-gray-500 mt-1">
                Course: <span class="font-bold text-emerald-600">{{ $course->title }}</span>
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-4 text-xs leading-relaxed font-semibold">
            <p class="font-bold mb-1">Please fix the following errors:</p>
            <ul class="space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.courses.store-lesson', $course) }}"
          enctype="multipart/form-data" class="space-y-6" id="lesson-form">
        @csrf

        {{-- ── Lesson Details ── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm">
            <h2 class="font-bold text-xs text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-5 h-5 rounded-md bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-extrabold">1</span>
                Lesson Details
            </h2>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Lesson Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                       placeholder="e.g., Chapter 1: Introduction to Algebra">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Description <span class="text-red-400">*</span></label>
                <textarea name="description" rows="3" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800 resize-none leading-relaxed"
                          placeholder="What will students learn in this lesson?">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- ── Content Blocks ── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xs text-gray-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-extrabold">2</span>
                    Content Blocks
                </h2>
                <button type="button" @click="addBlock()"
                        class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition shadow-sm">
                    Add Content Block
                </button>
            </div>

            <p class="text-[10px] text-gray-400 font-semibold leading-relaxed">
                Add one or more content blocks to this lesson. Each block can be a different type (PDF, Video, Image, Text).
            </p>

            <div class="space-y-4">
                <template x-for="(block, index) in blocks" :key="block.id">
                    <div class="border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 bg-gray-50/30">

                        {{-- Block header --}}
                        <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-200 bg-gray-50">
                            <div class="flex-1 flex items-center gap-3">
                                <span class="text-[10px] font-extrabold text-gray-800 uppercase tracking-wider" x-text="'Block ' + (index + 1)"></span>
                                <span class="text-[9px] px-2 py-0.5 rounded-md border font-extrabold uppercase tracking-wider bg-white text-gray-700 border-gray-200"
                                      x-text="block.type"></span>
                            </div>

                            <button type="button" @click="removeBlock(index)"
                                    x-show="blocks.length > 1"
                                    class="text-[9px] font-bold uppercase tracking-wider text-red-500 hover:text-red-700 px-2 py-1 bg-red-50 border border-red-150 rounded-md transition">
                                Remove
                            </button>
                        </div>

                        {{-- Block fields --}}
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Type selector --}}
                                <div>
                                    <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Content Type <span class="text-red-400">*</span></label>
                                    <select :name="'contents[' + index + '][type]'" x-model="block.type" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 bg-white text-gray-850">
                                        <option value="pdf">PDF Document</option>
                                        <option value="video">Video</option>
                                        <option value="image">Image</option>
                                        <option value="text">Text / Notes</option>
                                    </select>
                                </div>

                                {{-- Block title --}}
                                <div>
                                    <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Block Title</label>
                                    <input type="text" :name="'contents[' + index + '][title]'"
                                           x-model="block.title"
                                           placeholder="Optional — e.g., Lecture Notes"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-gray-850">
                                </div>
                            </div>

                            {{-- File upload (non-text) --}}
                            <div x-show="block.type !== 'text'" x-transition>
                                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                    Upload File
                                    <span class="text-gray-400 font-semibold ml-1">
                                        (max 50MB — <span x-text="acceptLabel(block.type)"></span>)
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 border-2 border-dashed border-gray-200 hover:border-emerald-500/35 rounded-lg p-4 cursor-pointer transition bg-white group">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-600 group-hover:text-emerald-600 transition font-bold">
                                            Click to choose file
                                        </p>
                                        <p class="text-[9px] text-gray-400 font-semibold mt-0.5" x-text="acceptLabel(block.type)"></p>
                                    </div>
                                    <input type="file"
                                           :name="'contents[' + index + '][file]'"
                                           :accept="acceptAttr(block.type)"
                                           @change="onFileChange($event, block)"
                                           class="hidden">
                                </label>
                                <div class="flex items-center justify-between mt-2">
                                    <p x-show="block.fileName" class="text-[10px] font-bold text-emerald-600">
                                        ✓ <span x-text="block.fileName"></span>
                                        <span x-show="block.fileSize" class="text-gray-400 font-semibold ml-1">(<span x-text="block.fileSize"></span>)</span>
                                    </p>
                                    <p x-show="block.fileSizeWarning" class="text-[10px] text-red-500 font-bold" x-text="block.fileSizeWarning"></p>
                                </div>
                            </div>

                            {{-- Text content --}}
                            <div x-show="block.type === 'text'" x-transition>
                                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Text Content <span class="text-red-400">*</span></label>
                                <textarea :name="'contents[' + index + '][content_text]'"
                                          rows="8"
                                          @input="block.charCount = $event.target.value.length"
                                          placeholder="Type or paste your lesson notes, explanations, or content here..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-gray-850 resize-y leading-relaxed"></textarea>
                                <p class="text-[9px] text-gray-400 text-right mt-1 font-bold uppercase tracking-wider">
                                    <span x-text="block.charCount"></span> characters
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Add block CTA (bottom) --}}
            <button type="button" @click="addBlock()"
                    class="w-full border border-dashed border-gray-300 hover:border-emerald-500/35 hover:bg-emerald-50/10 text-gray-400 hover:text-emerald-600 py-3.5 rounded-lg text-xs font-bold uppercase tracking-wider transition">
                Add Another Content Block
            </button>
        </div>

        {{-- Info note --}}
        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-xs text-blue-800 leading-relaxed font-semibold">
            <p class="font-bold uppercase tracking-wider">After adding this lesson:</p>
            <ul class="mt-1 space-y-0.5">
                <li>• The lesson will be pending admin approval.</li>
                <li>• Students can access it after the course is approved.</li>
            </ul>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pb-20">
            <button type="submit" id="submit-btn"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3.5 rounded-lg transition-all text-xs flex items-center justify-center gap-2">
                <span id="btn-text">Add Lesson to Course</span>
                <span id="btn-spinner" class="hidden">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Uploading…
                </span>
            </button>
            <a href="{{ route('teacher.courses.show', $course) }}"
               class="px-6 py-3.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition font-bold text-xs uppercase tracking-wider flex items-center">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function lessonBuilder() {
    return {
        blockCounter: 1,
        blocks: [{ id: 0, type: 'pdf', title: '', fileName: '', fileSize: '', charCount: 0 }],

        addBlock() {
            this.blocks.push({ id: ++this.blockCounter, type: 'pdf', title: '', fileName: '', fileSize: '', charCount: 0 });
        },

        removeBlock(index) {
            if (this.blocks.length > 1) {
                this.blocks.splice(index, 1);
            }
        },

        onFileChange(event, block) {
            const file = event.target.files[0];
            if (!file) return;
            block.fileName = file.name;
            const mb = (file.size / 1024 / 1024).toFixed(1);
            block.fileSize = mb + ' MB';
            if (file.size > 100 * 1024 * 1024) {
                block.fileSizeWarning = '⚠️ File exceeds 100MB limit!';
            } else {
                block.fileSizeWarning = '';
            }
        },

        acceptLabel(type) {
            const labels = {
                pdf:   'PDF files only',
                video: 'MP4, WebM, MOV, AVI (max 100MB)',
                image: 'JPG, PNG, GIF, WebP',
                text:  '',
            };
            return labels[type] ?? '';
        },

        acceptAttr(type) {
            const attrs = {
                pdf:   '.pdf',
                video: '.mp4,.webm,.mov,.avi',
                image: '.jpg,.jpeg,.png,.gif,.webp',
                text:  '',
            };
            return attrs[type] ?? '';
        },
    };
}

// Show spinner on submit
document.getElementById('lesson-form')?.addEventListener('submit', function() {
    const btn  = document.getElementById('submit-btn');
    const text = document.getElementById('btn-text');
    const spin = document.getElementById('btn-spinner');
    if (btn) btn.disabled = true;
    if (text) text.classList.add('hidden');
    if (spin) spin.classList.remove('hidden');
});
</script>
@endpush
@endsection
