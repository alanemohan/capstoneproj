@extends('layouts.teacher')

@section('title', isset($lesson) ? 'Edit Lesson' : 'Upload Lesson - Nabha Learning')

@section('teacher-content')
<div class="max-w-3xl mx-auto animate-fade-in">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.lessons') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back</a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">{{ isset($lesson) ? 'Edit Lesson' : 'Upload New Lesson' }}</h1>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-xs leading-relaxed font-semibold">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          id="lesson-form"
          action="{{ isset($lesson) ? route('teacher.lessons.update', $lesson->id) : route('teacher.lessons.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-6 shadow-sm"
          x-data="{ 
              fileType: '{{ old('file_type', $lesson->file_type ?? 'pdf') }}',
              fileName: '',
              fileSize: '',
              fileSizeWarning: '',
              previewUrl: '',
              isDragging: false,
              onFileChange(event) {
                  const file = event.target.files[0];
                  this.processFile(file);
              },
              onFileDrop(event) {
                  this.isDragging = false;
                  const file = event.dataTransfer.files[0];
                  if (!file) return;
                  const input = event.currentTarget.querySelector('input[type=\'file\']');
                  if (input) {
                      const dt = new DataTransfer();
                      dt.items.add(file);
                      input.files = dt.files;
                  }
                  this.processFile(file);
              },
              processFile(file) {
                  if (!file) return;
                  this.fileName = file.name;
                  const mb = (file.size / 1024 / 1024).toFixed(1);
                  this.fileSize = mb + ' MB';
                  if (file.size > 500 * 1024 * 1024) {
                      this.fileSizeWarning = '⚠️ File exceeds 500MB limit!';
                  } else {
                      this.fileSizeWarning = '';
                  }
                  if (this.previewUrl) {
                      URL.revokeObjectURL(this.previewUrl);
                      this.previewUrl = '';
                  }
                  if (this.fileType === 'video' || this.fileType === 'image' || this.fileType === 'pdf') {
                      this.previewUrl = URL.createObjectURL(file);
                  }
              },
              acceptLabel() {
                  const labels = {
                      pdf:   'PDF files only',
                      video: 'MP4, WebM, MOV, AVI, MKV (max 500MB)',
                      image: 'JPG, PNG, JPEG',
                  };
                  return labels[this.fileType] ?? '';
              },
              acceptAttr() {
                  const attrs = {
                      pdf:   '.pdf',
                      video: '.mp4,.webm,.mov,.avi,.mkv',
                      image: '.jpg,.jpeg,.png',
                  };
                  return attrs[this.fileType] ?? '';
              }
          }">
        @csrf
        @if(isset($lesson)) @method('PUT') @endif
        <input type="hidden" name="file_path" id="file_path_input" value="{{ old('file_path', $lesson->file_path ?? '') }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Lesson Title *</label>
                <input type="text" name="title" value="{{ old('title', $lesson->title ?? '') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                       placeholder="e.g., Introduction to Photosynthesis">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Link to Course (Optional)</label>
                <select name="course_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                    <option value="">Select Course (None - Standalone)</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $lesson->course_id ?? '') == $course->id ? 'selected':'' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject', $lesson->subject ?? '') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                       placeholder="e.g., Mathematics, Physics, AI, Web Dev">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Class Level *</label>
                <select name="class_level" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                    <option value="">Select Class</option>
                    @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                        <option value="{{ $class }}" {{ old('class_level', $lesson->class_level ?? '') === $class ? 'selected':'' }}>{{ $class }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Language *</label>
                <select name="language" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                    <option value="">Select Language</option>
                    @foreach(['en' => 'English', 'hi' => 'Hindi', 'pa' => 'Punjabi'] as $val => $label)
                        <option value="{{ $val }}" {{ old('language', $lesson->language ?? 'en') === $val ? 'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Description *</label>
                <textarea name="description" rows="3" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800 resize-none leading-relaxed"
                          placeholder="Brief description of what this lesson covers...">{{ old('description', $lesson->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Content Type *</label>
                <select name="file_type" x-model="fileType" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                    <option value="pdf">PDF Document</option>
                    <option value="video">Video</option>
                    <option value="text">Text Content (online only)</option>
                    <option value="image">Image</option>
                </select>
            </div>

            <div x-show="fileType !== 'text'" class="md:col-span-2">
                <label class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">
                    Upload File
                    <span class="text-gray-400 font-semibold ml-1">
                        (max 50MB — <span x-text="acceptLabel()"></span>)
                    </span>
                </label>
                <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-205 dark:border-white/[0.08] hover:border-emerald-500/35 dark:hover:border-emerald-500/35 rounded-xl p-6 cursor-pointer transition bg-white dark:bg-black/20 group relative overflow-hidden"
                     :class="isDragging ? 'border-emerald-500 bg-emerald-500/5 dark:bg-emerald-500/10' : ''"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="onFileDrop($event)"
                     @click="$refs.fileInput.click()">
                    <div class="text-center pointer-events-none">
                        <span class="text-2xl block mb-2 group-hover:scale-110 transition-transform">📁</span>
                        <p class="text-xs text-gray-650 dark:text-white/60 group-hover:text-emerald-655 dark:group-hover:text-emerald-400 transition font-bold">
                            Drag & drop or Click to browse
                        </p>
                        <p class="text-[9px] text-gray-400 dark:text-white/30 font-semibold mt-0.5" x-text="acceptLabel()"></p>
                    </div>
                    <input type="file"
                           x-ref="fileInput"
                           name="file"
                           :accept="acceptAttr()"
                           @change="onFileChange($event)"
                           class="hidden">
                </div>
                
                @if(isset($lesson) && $lesson->file_path)
                    <p class="text-[10px] text-gray-450 font-bold mt-1.5 flex items-center gap-1">
                        <span>ℹ️</span> Current file: <span class="text-violet-650 dark:text-violet-400">{{ basename($lesson->file_path) }}</span> (leave empty to keep)
                    </p>
                @endif

                <div class="flex items-center justify-between mt-2">
                    <p x-show="fileName" class="text-[10px] font-bold text-emerald-600">
                        ✓ <span x-text="fileName"></span>
                        <span x-show="fileSize" class="text-gray-450 font-semibold ml-1">(<span x-text="fileSize"></span>)</span>
                    </p>
                    <p x-show="fileSizeWarning" class="text-[10px] text-red-500 font-bold" x-text="fileSizeWarning"></p>
                </div>

                {{-- Video Preview --}}
                <div x-show="fileType === 'video' && previewUrl" class="mt-3 rounded-lg overflow-hidden border border-gray-200 dark:border-white/[0.08] bg-black">
                    <video :src="previewUrl" controls class="w-full max-h-48"></video>
                </div>

                {{-- Image Preview --}}
                <div x-show="fileType === 'image' && previewUrl" class="mt-3 rounded-lg overflow-hidden border border-gray-200 dark:border-white/[0.08] flex justify-center bg-gray-50 dark:bg-black/20 p-2">
                    <img :src="previewUrl" class="max-h-48 object-contain rounded-lg">
                </div>

                {{-- PDF Preview --}}
                <div x-show="fileType === 'pdf' && previewUrl" class="mt-3 p-3 rounded-lg border border-gray-200 dark:border-white/[0.08] bg-gray-50 dark:bg-black/20 flex items-center gap-3">
                    <span class="text-2xl">📄</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800 dark:text-white/80 truncate" x-text="fileName"></p>
                        <p class="text-[10px] text-gray-450 dark:text-white/40" x-text="fileSize"></p>
                    </div>
                    <a :href="previewUrl" target="_blank" class="text-[10px] text-violet-650 dark:text-violet-400 font-bold uppercase hover:underline">
                        Preview PDF ↗
                    </a>
                </div>
            </div>

            <div x-show="fileType === 'video'">
                <label class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Video Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes ?? '') }}" min="1"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                       placeholder="e.g., 15">
            </div>

            <div x-show="fileType === 'text'" class="md:col-span-2">
                <label class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Lesson Text Content</label>
                <textarea name="content" rows="10"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800 leading-relaxed"
                          placeholder="Type or paste your lesson content here...">{{ old('content', $lesson->content ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-250 rounded-lg p-4.5 text-xs text-yellow-800 leading-relaxed font-semibold">
            <p class="font-bold uppercase tracking-wider">Important:</p>
            <ul class="mt-1 space-y-1">
                <li>• Your lesson will be reviewed by Admin before it appears to students.</li>
                <li>• Upload clear, school-appropriate content aligned with the curriculum.</li>
                <li>• Supported formats: PDF, MP4/WebM videos, JPG/PNG images</li>
            </ul>
        </div>

        <div class="flex gap-3">
            <button type="submit" id="submit-btn"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3 rounded-lg transition-all text-xs flex items-center justify-center gap-2">
                <span id="btn-text">{{ isset($lesson) ? 'Update Lesson' : 'Submit for Review' }}</span>
                <span id="btn-spinner" class="hidden">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Uploading…
                </span>
            </button>
            <a href="{{ route('teacher.lessons') }}"
               class="px-6 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition font-bold text-xs uppercase tracking-wider flex items-center">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- Premium Glassy Progress Overlay --}}
<div id="progress-overlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden animate-fade-in">
    <div class="bg-[#0b0f19]/90 border border-white/[0.08] max-w-sm w-full mx-4 p-6 rounded-2xl text-center space-y-4 shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-3xl mx-auto border border-emerald-500/10 animate-bounce">
            🚀
        </div>
        <div>
            <h3 class="text-sm font-bold text-white/95 tracking-wide uppercase">Uploading Lesson</h3>
            <p class="text-[11px] text-white/40 mt-1 leading-relaxed">Please keep this window open while we upload and validate your lesson files.</p>
        </div>
        
        {{-- Progress Bar --}}
        <div class="space-y-2">
            <div class="w-full h-2.5 bg-white/[0.06] rounded-full overflow-hidden border border-white/[0.04]">
                <div id="progress-bar" class="h-full bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-500 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-white/50 uppercase tracking-wider">
                <span>Progress</span>
                <span id="progress-percent">0%</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('lesson-form')?.addEventListener('submit', function(e) {
    const fileInput = document.querySelector('input[type="file"]');
    const fileTypeSelect = document.querySelector('select[name="file_type"]');
    const filePathInput = document.getElementById('file_path_input');
    
    if (fileTypeSelect && fileTypeSelect.value === 'text') {
        return; // Standard submit is fine for purely textual lessons
    }
    
    // If no new file is chosen, or it was already uploaded and saved in file_path
    if (fileInput && !fileInput.files.length) {
        return; 
    }

    e.preventDefault();
    const form = this;
    const file = fileInput.files[0];
    
    const submitBtn = document.getElementById('submit-btn');
    const text = document.getElementById('btn-text');
    const spin = document.getElementById('btn-spinner');
    
    const progressOverlay = document.getElementById('progress-overlay');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');
    
    if (submitBtn) submitBtn.disabled = true;
    if (text) text.classList.add('hidden');
    if (spin) spin.classList.remove('hidden');
    if (progressOverlay) progressOverlay.classList.remove('hidden');

    const chunkSize = 5 * 1024 * 1024; // 5MB chunks
    const totalChunks = Math.ceil(file.size / chunkSize);
    const uploadId = 'up_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    let chunkIndex = 0;

    function uploadNextChunk() {
        const start = chunkIndex * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('upload_id', uploadId);
        formData.append('chunk_index', chunkIndex);
        formData.append('total_chunks', totalChunks);
        formData.append('file', chunk);
        formData.append('filename', file.name);
        formData.append('type', 'lessons');
        
        const token = document.querySelector('input[name="_token"]')?.value;
        if (token) {
            formData.append('_token', token);
        }

        let retries = 0;
        const maxRetries = 3;

        function attemptUpload() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('teacher.upload-chunk') }}', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function(event) {
                if (event.lengthComputable) {
                    const chunkPercent = (event.loaded / event.total);
                    const totalPercent = Math.min(99, Math.round(((chunkIndex + chunkPercent) / totalChunks) * 100));
                    if (progressBar) progressBar.style.width = totalPercent + '%';
                    if (progressPercent) progressPercent.textContent = totalPercent + '%';
                }
            });

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.completed) {
                        if (progressBar) progressBar.style.width = '100%';
                        if (progressPercent) progressPercent.textContent = '100%';
                        
                        // Set the hidden file path input
                        if (filePathInput) {
                            filePathInput.value = res.file_path;
                        }
                        
                        // Remove name attribute from file input to prevent uploading the raw file again on form submit
                        fileInput.removeAttribute('name');
                        
                        // Submit form
                        form.submit();
                    } else {
                        chunkIndex++;
                        uploadNextChunk();
                    }
                } else {
                    handleFailure();
                }
            };

            xhr.onerror = handleFailure;

            function handleFailure() {
                if (retries < maxRetries) {
                    retries++;
                    console.warn(`Chunk ${chunkIndex} failed. Retrying attempt ${retries}/${maxRetries}...`);
                    setTimeout(attemptUpload, 2000); // retry after 2 seconds
                } else {
                    if (progressOverlay) progressOverlay.classList.add('hidden');
                    if (submitBtn) submitBtn.disabled = false;
                    if (text) text.classList.remove('hidden');
                    if (spin) spin.classList.add('hidden');
                    alert('Upload failed: a connection or server error occurred. Please try again.');
                }
            }

            xhr.send(formData);
        }

        attemptUpload();
    }

    uploadNextChunk();
});
</script>
@endpush
@endsection
