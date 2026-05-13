@extends('layouts.app')

@section('title', 'Import Job Dataset')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass p-10 mb-8" data-aos="fade-up">
        <div class="flex items-center gap-6 mb-8 pb-8 border-b border-slate-800">
            <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 text-3xl">
                <i class="fas fa-file-csv"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Bulk Job Importer</h2>
                <p class="text-slate-400 text-sm">Upload a CSV file to automatically populate your job listings.</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-500 p-4 rounded-xl mb-8 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <form action="{{ route('admin.jobs.import.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-8">
                <div class="border-2 border-dashed border-slate-700 rounded-3xl p-12 text-center hover:border-blue-500 transition-all group relative">
                    <input type="file" name="csv_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                    <div class="space-y-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-slate-600 group-hover:text-blue-500 transition-all"></i>
                        <div class="text-white font-medium">Click to upload or drag and drop</div>
                        <p class="text-xs text-slate-500">CSV or TXT files only (Max 10MB)</p>
                    </div>
                </div>

                <div class="bg-slate-900/50 p-6 rounded-2xl border border-slate-800">
                    <h4 class="text-sm font-bold text-white mb-4 uppercase tracking-widest">CSV Structure Requirements</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span><strong>job_title</strong>: Role name</span>
                        </div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span><strong>category</strong>: Industry category</span>
                        </div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span><strong>location</strong>: City or remote</span>
                        </div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span><strong>description</strong>: Job details</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-800">
                        <code class="text-[10px] text-blue-400">job_title,category,location,salary,skills,description</code>
                    </div>
                </div>

                <button type="submit" class="w-full btn-premium py-4">
                    <i class="fas fa-file-import mr-2"></i> Start Import Process
                </button>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="glass-dark p-8" data-aos="fade-up" data-aos-delay="100">
        <h3 class="text-lg font-bold text-white mb-4">Why use bulk import?</h3>
        <p class="text-sm text-slate-400 leading-relaxed">
            Bulk importing allows you to scale your job board rapidly by integrating with external datasets or migration tools. The system automatically maps categories and creates unique slugs for each listing.
        </p>
    </div>
</div>
@endsection
