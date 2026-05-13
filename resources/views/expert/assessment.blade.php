@extends('layouts.app')

@section('title', 'AI Career Assessment')

@section('content')
<div class="max-w-4xl mx-auto" x-data="assessmentWizard()">
    <!-- Header -->
    <div class="text-center mb-12" data-aos="fade-down">
        <h1 class="text-4xl font-extrabold text-white mb-4">Discover Your <span class="text-gradient">Career DNA</span></h1>
        <p class="text-slate-400">Answer truthfully to help our AI construct your professional personality profile.</p>
    </div>

    <!-- Progress Indicator -->
    <div class="mb-12 glass p-4 flex items-center justify-between" data-aos="fade-up">
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-blue-500 uppercase tracking-widest">Progress</span>
            <div class="w-48 h-2 bg-slate-800 rounded-full overflow-hidden">
                <div class="bg-blue-600 h-full transition-all duration-500" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest">
            Category <span x-text="currentStep + 1"></span> of <span x-text="categories.length"></span>
        </div>
    </div>

    <!-- Step Container -->
    <form action="{{ route('assessment.store') }}" method="POST">
        @csrf
        <template x-for="(cat, index) in categories" :key="index">
            <div x-show="currentStep === index" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-8">
                
                <!-- Category Info -->
                <div class="glass p-10 relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-6 mb-6">
                            <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 text-3xl">
                                <i :class="'fas ' + cat.icon"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white" x-text="cat.name"></h2>
                                <p class="text-slate-400 text-sm" x-text="cat.description"></p>
                            </div>
                        </div>

                        <!-- Questions -->
                        <div class="space-y-10">
                            <template x-for="q in cat.questions" :key="q.id">
                                <div class="bg-slate-900/50 p-6 rounded-2xl border border-slate-800">
                                    <p class="text-white font-medium mb-6 text-lg" x-text="q.question"></p>
                                    
                                    <div class="flex items-center justify-between gap-2 max-w-2xl mx-auto">
                                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Strongly Dislike</span>
                                        <div class="flex items-center gap-4">
                                            <template x-for="val in [1, 2, 3, 4, 5]" :key="val">
                                                <label class="cursor-pointer group">
                                                    <input type="radio" :name="'answers[' + q.id + ']'" :value="val" 
                                                           required class="hidden peer"
                                                           @change="setAnswer(q.id, val)">
                                                    <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 font-bold border-2 border-transparent transition-all peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-400 hover:border-slate-600 group-hover:scale-110">
                                                        <span x-text="val"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Strongly Like</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 opacity-5">
                        <i :class="'fas ' + cat.icon + ' text-[200px] text-white'"></i>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="flex justify-between items-center pt-8">
                    <button type="button" 
                            x-show="currentStep > 0"
                            @click="prevStep()"
                            class="px-8 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Previous
                    </button>
                    <div x-show="currentStep === 0"></div>

                    <button type="button" 
                            x-show="currentStep < categories.length - 1"
                            @click="nextStep()"
                            class="btn-premium px-10 py-3">
                        Continue <i class="fas fa-arrow-right ml-2"></i>
                    </button>

                    <button type="submit" 
                            x-show="currentStep === categories.length - 1"
                            class="btn-premium px-10 py-3 bg-gradient-to-r from-emerald-600 to-teal-600">
                        Finalize Assessment <i class="fas fa-check-circle ml-2"></i>
                    </button>
                </div>
            </div>
        </template>
    </form>
</div>

@push('scripts')
<script>
    function assessmentWizard() {
        return {
            currentStep: 0,
            categories: {!! json_encode($categories) !!},
            answers: {},
            get progress() {
                return ((this.currentStep + 1) / this.categories.length) * 100;
            },
            nextStep() {
                // Basic validation: ensure all questions in current category are answered
                const currentCat = this.categories[this.currentStep];
                const allAnswered = currentCat.questions.every(q => this.answers[q.id]);
                
                if (!allAnswered) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Please answer all questions before continuing.',
                        background: '#1e293b',
                        color: '#fff',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
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
            }
        }
    }
</script>
@endpush
@endsection
