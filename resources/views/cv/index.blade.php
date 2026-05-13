@extends('layouts.app')

@section('title', 'CV Analyzer')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-bold uppercase tracking-widest mb-6">
            <i class="fas fa-robot"></i> AI-Powered Analysis
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-3">CV <span class="text-gradient">Intelligence</span></h1>
        <p class="text-slate-400 max-w-xl mx-auto">Upload your CV/Resume and let our Certainty Factor engine analyze your skills, interests, and recommend the best career paths for you.</p>
    </div>

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-exclamation-circle"></i>
        <p class="font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Upload Card -->
    <form action="{{ route('cv.analyze') }}" method="POST" enctype="multipart/form-data" id="cvForm" x-data="cvUploader()" @submit="if(fileName) isAnalyzing = true">
        @csrf
        <div class="glass p-8 lg:p-12">
            <!-- Drag & Drop Zone -->
            <div class="relative border-2 border-dashed rounded-2xl p-12 text-center transition-all duration-300 cursor-pointer"
                 :class="isDragging ? 'border-blue-500 bg-blue-500/5' : (fileName ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-700 hover:border-slate-500 hover:bg-slate-800/30')"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleDrop($event)"
                 @click="$refs.fileInput.click()">

                <input type="file" name="cv_file" x-ref="fileInput" @change="handleFileSelect($event)"
                       accept=".pdf" class="hidden" required>

                <!-- Icon -->
                <div class="mb-6" x-show="!fileName">
                    <div class="w-20 h-20 mx-auto bg-slate-800 rounded-3xl flex items-center justify-center mb-4">
                        <i class="fas fa-cloud-arrow-up text-3xl" :class="isDragging ? 'text-blue-400' : 'text-slate-500'"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Drop your CV here</h3>
                    <p class="text-slate-400 text-sm">or click to browse files</p>
                    <p class="text-slate-600 text-xs mt-3">Supports PDF • Max 10MB</p>
                </div>

                <!-- Selected File -->
                <div class="mb-2" x-show="fileName" x-cloak>
                    <div class="w-20 h-20 mx-auto bg-emerald-500/10 rounded-3xl flex items-center justify-center mb-4">
                        <i class="fas fa-file-pdf text-3xl text-emerald-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1" x-text="fileName"></h3>
                    <p class="text-emerald-400 text-sm font-medium"><i class="fas fa-check-circle mr-1"></i> Ready to analyze</p>
                    <button type="button" @click.stop="removeFile()" class="text-xs text-red-400 hover:text-red-300 mt-2 underline">Remove file</button>
                </div>
            </div>

            <!-- How it works -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
                <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-900/50">
                    <div class="w-8 h-8 bg-blue-600/10 rounded-lg flex items-center justify-center text-blue-400 shrink-0 text-sm">
                        <i class="fas fa-magnifying-glass-chart"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white mb-1">Skill Extraction</p>
                        <p class="text-[11px] text-slate-500 leading-relaxed">AI scans your CV to detect technical & soft skills.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-900/50">
                    <div class="w-8 h-8 bg-purple-600/10 rounded-lg flex items-center justify-center text-purple-400 shrink-0 text-sm">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white mb-1">CF Analysis</p>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Certainty Factor engine matches your profile to careers.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-900/50">
                    <div class="w-8 h-8 bg-emerald-600/10 rounded-lg flex items-center justify-center text-emerald-400 shrink-0 text-sm">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white mb-1">Smart Results</p>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Get personalized job recommendations ranked by fit.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-8 text-center">
                <button type="submit" :disabled="!fileName || isAnalyzing"
                        class="btn-premium px-10 py-4 text-base disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none">
                    <span x-show="!isAnalyzing">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i> Analyze My CV
                    </span>
                    <span x-show="isAnalyzing" class="flex items-center gap-3">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Analyzing your CV...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function cvUploader() {
    return {
        isDragging: false,
        fileName: null,
        isAnalyzing: false,
        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length && files[0].type === 'application/pdf') {
                this.$refs.fileInput.files = files;
                this.fileName = files[0].name;
            } else {
                Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Only PDF files are supported.', background: '#1e293b', color: '#fff', confirmButtonColor: '#2563eb' });
            }
        },
        handleFileSelect(e) {
            if (e.target.files.length) {
                this.fileName = e.target.files[0].name;
            }
        },
        removeFile() {
            this.$refs.fileInput.value = '';
            this.fileName = null;
        }
    }
}
</script>
@endpush
@endsection
