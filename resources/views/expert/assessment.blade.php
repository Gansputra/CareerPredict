@extends('layouts.app')

@section('title', 'Career DNA Test')

@section('content')
<div class="max-w-4xl mx-auto" x-data="assessmentWizard()">
    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-white mb-3">Discover Your <span class="text-gradient">Career DNA</span></h1>
        <p class="text-slate-400 max-w-xl mx-auto">Complete all steps to unlock your personalized career recommendations powered by Certainty Factor analysis.</p>

        @if($hasHistory)
        <div class="mt-6 inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
            <i class="fas fa-info-circle text-amber-400"></i>
            <span class="text-amber-400 text-sm font-medium">You have a previous assessment.</span>
            <form action="{{ route('assessment.reset') }}" method="POST" class="inline" id="resetForm">
                @csrf
                <button type="button" onclick="confirmReset()" class="text-sm font-bold text-red-400 hover:text-red-300 underline transition-colors">
                    Reset & Start Fresh
                </button>
            </form>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-xl mb-8 flex items-center gap-3 animate-fade-in">
        <i class="fas fa-check-circle"></i>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Step Progress Bar -->
    <div class="mb-10 glass p-5 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <template x-for="(label, idx) in stepLabels" :key="idx">
                <div class="flex items-center" :class="idx < stepLabels.length - 1 ? 'flex-1' : ''">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold transition-all duration-500 shrink-0"
                         :class="currentStep > idx ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : (currentStep === idx ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/40 scale-110' : 'bg-slate-800 text-slate-500')">
                        <i x-show="currentStep > idx" class="fas fa-check text-[10px]"></i>
                        <span x-show="currentStep <= idx" x-text="idx + 1"></span>
                    </div>
                    <div x-show="idx < stepLabels.length - 1" class="flex-1 h-0.5 mx-1.5 sm:mx-2 rounded-full transition-all duration-500"
                         :class="currentStep > idx ? 'bg-emerald-500' : 'bg-slate-800'"></div>
                </div>
            </template>
        </div>
        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-500 h-full transition-all duration-700 ease-out rounded-full" :style="'width: ' + progress + '%'"></div>
        </div>
        <div class="flex justify-between mt-2">
            <span class="text-[10px] text-slate-500 font-bold" x-text="progressLabel"></span>
            <span class="text-[10px] text-blue-400 font-bold" x-text="Math.round(progress) + '% Complete'"></span>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ route('assessment.store') }}" method="POST" @submit="isSubmitting = true">
        @csrf

        <!-- PERSONALITY STEPS (dynamic from categories) -->
        <template x-for="(cat, catIdx) in categories" :key="'cat-' + catIdx">
            <div x-show="currentStep === catIdx"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0">
                 
                <div class="glass p-8 lg:p-10 relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-800">
                            <div class="w-14 h-14 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 text-2xl">
                                <i :class="'fas ' + cat.icon"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white" x-text="cat.name"></h2>
                                <p class="text-slate-400 text-sm" x-text="cat.description"></p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(q, qIdx) in cat.questions" :key="q.id">
                                <div class="bg-slate-900/50 p-5 rounded-2xl border border-slate-800 transition-all"
                                     :class="answers[q.id] ? 'border-blue-600/30 bg-blue-600/5' : ''">
                                    <div class="flex items-start gap-3 mb-5">
                                        <span class="w-7 h-7 bg-slate-800 rounded-lg flex items-center justify-center text-xs font-bold text-slate-500 shrink-0 mt-0.5" x-text="qIdx + 1"></span>
                                        <p class="text-white font-medium" x-text="q.question"></p>
                                    </div>

                                    <div class="max-w-xl mx-auto">
                                        <div class="flex items-center justify-between mb-1.5 sm:mb-0">
                                            <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest hidden sm:block">Disagree</span>
                                            <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest hidden sm:block">Agree</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-2 sm:gap-3">
                                            <span class="text-[8px] sm:hidden text-slate-500 font-bold uppercase shrink-0">No</span>
                                            <template x-for="val in [1, 2, 3, 4, 5]" :key="val">
                                                <label class="cursor-pointer group" @click="setAnswer(q.id, val)">
                                                    <input type="radio" :name="'answers[' + q.id + ']'" :value="val"
                                                           required class="hidden peer">
                                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 font-bold text-sm sm:text-base border-2 border-transparent transition-all duration-200 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-400 peer-checked:shadow-lg peer-checked:shadow-blue-600/30 hover:border-slate-600 group-hover:scale-105">
                                                        <span x-text="val"></span>
                                                    </div>
                                                </label>
                                            </template>
                                            <span class="text-[8px] sm:hidden text-slate-500 font-bold uppercase shrink-0">Yes</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 opacity-[0.03]">
                        <i :class="'fas ' + cat.icon + ' text-[200px] text-white'"></i>
                    </div>
                </div>
            </div>
        </template>

        <!-- SKILLS STEP -->
        <div x-show="currentStep === categories.length"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0">

            <div class="glass p-8 lg:p-10">
                <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-800">
                    <div class="w-14 h-14 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-500 text-2xl">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Technical & Soft Skills</h2>
                        <p class="text-slate-400 text-sm">Rate your proficiency in each skill area (1 = Beginner, 5 = Expert). Skip skills you don't have.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($skills as $skill)
                    <div class="p-4 rounded-2xl bg-slate-900/50 border border-slate-800 hover:border-slate-700 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-bold text-white">{{ $skill->name }}</label>
                            <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full {{ $skill->type === 'technical' ? 'bg-blue-500/10 text-blue-400' : 'bg-purple-500/10 text-purple-400' }}">{{ $skill->type }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] text-slate-600 w-6">Skip</span>
                            @for($i = 0; $i <= 5; $i++)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="skills[{{ $skill->id }}]" value="{{ $i }}" class="hidden peer" {{ $i === 0 ? 'checked' : '' }}>
                                <div class="h-8 rounded-lg {{ $i === 0 ? 'bg-slate-800/50 peer-checked:bg-slate-700' : 'bg-slate-800 peer-checked:bg-blue-600 peer-checked:shadow-lg peer-checked:shadow-blue-600/20' }} flex items-center justify-center text-xs font-bold transition-all text-slate-400 peer-checked:text-white">
                                    {{ $i === 0 ? '—' : $i }}
                                </div>
                            </label>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- INTERESTS STEP -->
        <div x-show="currentStep === categories.length + 1"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0">

            <div class="glass p-8 lg:p-10">
                <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-800">
                    <div class="w-14 h-14 bg-purple-600/10 rounded-2xl flex items-center justify-center text-purple-500 text-2xl">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Career Interests</h2>
                        <p class="text-slate-400 text-sm">Select topics and fields that genuinely excite you. Pick at least 2.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($interests as $interest)
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="interests[]" value="{{ $interest->id }}" class="hidden peer" @change="updateInterestCount($event)">
                        <div class="p-5 rounded-2xl bg-slate-900/50 border border-slate-800 peer-checked:bg-purple-600/10 peer-checked:border-purple-500 transition-all text-center group-hover:bg-slate-800/80 group-hover:border-slate-700">
                            <i class="fas fa-star text-slate-700 peer-checked:text-purple-400 mb-2 text-lg group-hover:text-slate-600 transition-colors"></i>
                            <p class="text-sm font-medium text-slate-400 group-hover:text-slate-300 transition-colors">{{ $interest->name }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-4 text-center">
                    <span x-text="interestCount"></span> selected — <span :class="interestCount >= 2 ? 'text-emerald-400' : 'text-amber-400'" x-text="interestCount >= 2 ? 'Ready to submit!' : 'Select at least 2'"></span>
                </p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center pt-8">
            <button type="button"
                    x-show="currentStep > 0"
                    @click="prevStep()"
                    class="px-6 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Previous
            </button>
            <div x-show="currentStep === 0"></div>

            <button type="button"
                    x-show="currentStep < totalSteps - 1"
                    @click="nextStep()"
                    class="btn-premium px-8 py-3">
                Continue <i class="fas fa-arrow-right ml-2"></i>
            </button>

            <button type="submit"
                    x-show="currentStep === totalSteps - 1"
                    :disabled="isSubmitting"
                    class="btn-premium px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting">
                    <i class="fas fa-dna mr-2"></i> Analyze My Career DNA
                </span>
                <span x-show="isSubmitting" class="flex items-center gap-3">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Analyzing with CF Engine...
                </span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function assessmentWizard() {
        return {
            currentStep: 0,
            categories: {!! json_encode($categories) !!},
            answers: {},
            interestCount: 0,
            isSubmitting: false,
            stepLabels: [
                ...{!! json_encode($categories->pluck('name')) !!},
                'Skills',
                'Interests'
            ],

            get totalSteps() {
                return this.categories.length + 2; // categories + skills + interests
            },
            get progress() {
                return ((this.currentStep + 1) / this.totalSteps) * 100;
            },
            get progressLabel() {
                if (this.currentStep < this.categories.length) {
                    return 'Step ' + (this.currentStep + 1) + ': ' + this.categories[this.currentStep].name + ' Assessment';
                } else if (this.currentStep === this.categories.length) {
                    return 'Step ' + (this.currentStep + 1) + ': Skills Rating';
                } else {
                    return 'Step ' + (this.currentStep + 1) + ': Interest Selection';
                }
            },
            nextStep() {
                // Validate personality steps
                if (this.currentStep < this.categories.length) {
                    const currentCat = this.categories[this.currentStep];
                    const allAnswered = currentCat.questions.every(q => this.answers[q.id]);

                    if (!allAnswered) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Incomplete',
                            text: 'Please answer all questions in this category before continuing.',
                            background: '#1e293b',
                            color: '#fff',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }
                }

                // Validate interests step
                if (this.currentStep === this.totalSteps - 2 && this.interestCount < 2) {
                    // This is the interests step validation, but we handle it on submit
                }

                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            prevStep() {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            setAnswer(qId, val) {
                this.answers[qId] = val;
            },
            updateInterestCount(event) {
                const checkboxes = document.querySelectorAll('input[name="interests[]"]:checked');
                this.interestCount = checkboxes.length;
            }
        }
    }

    function confirmReset() {
        Swal.fire({
            title: 'Reset Career DNA?',
            text: 'This will erase all your assessment data, skills, interests, and recommendations. You cannot undo this.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#334155',
            confirmButtonText: 'Yes, Reset Everything',
            cancelButtonText: 'Cancel',
            background: '#1e293b',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('resetForm').submit();
            }
        });
    }
</script>
@endpush
@endsection
