@extends('layouts.app')

@section('title', 'Asesmen Keahlian')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ step: 1 }">
    <!-- Stepper -->
    <div class="mb-12 relative flex justify-between items-center max-w-lg mx-auto">
        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-800 z-0"></div>
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-blue-600 transition-all duration-500 z-0" :style="'width: ' + ((step-1)*50) + '%'"></div>
        
        @foreach([1, 2, 3] as $i)
        <div class="relative z-10 w-10 h-10 rounded-full border-4 flex items-center justify-center transition-all duration-500"
             :class="step >= {{ $i }} ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-600/40' : 'bg-[#0f172a] border-slate-800 text-slate-500'">
            <span class="font-bold">{{ $i }}</span>
        </div>
        @endforeach
    </div>

    <form action="{{ route('expert.calculate') }}" method="POST">
        @csrf
        
        <!-- Step 1: Technical Skills -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="glass p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-2">Keahlian Teknis</h2>
                <p class="text-slate-400 mb-8">Nilai kemampuan Anda di bidang teknis berikut (1-5).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($skills->where('type', 'technical') as $skill)
                    <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                        <label class="block text-sm font-bold text-white mb-3">{{ $skill->name }}</label>
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="skills[{{ $skill->id }}]" value="{{ $i }}" class="hidden peer">
                                <div class="h-8 rounded-lg bg-slate-700 peer-checked:bg-blue-600 flex items-center justify-center text-xs font-bold transition-all text-white">
                                    {{ $i }}
                                </div>
                            </label>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" @click="step = 2" class="btn-premium">Lanjutkan <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>

        <!-- Step 2: Interests -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="glass p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-2">Minat Karir</h2>
                <p class="text-slate-400 mb-8">Pilih topik dan bidang yang benar-benar menarik bagi Anda.</p>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($interests as $interest)
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="interests[]" value="{{ $interest->id }}" class="hidden peer">
                        <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/50 peer-checked:bg-emerald-600/10 peer-checked:border-emerald-600 transition-all text-center group-hover:bg-slate-800">
                            <span class="text-sm font-medium text-slate-400 peer-checked:text-emerald-400">{{ $interest->name }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" @click="step = 1" class="px-8 py-3 rounded-xl bg-slate-800 text-white font-semibold"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                <button type="button" @click="step = 3" class="btn-premium">Lanjutkan <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>

        <!-- Step 3: Behavioral Assessment -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="glass p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-2">Konsultasi Ahli</h2>
                <p class="text-slate-400 mb-8">Jawab pertanyaan terakhir ini untuk membantu kami menyempurnakan rekomendasi Anda.</p>

                <div class="space-y-8">
                    @foreach($questions as $q)
                    <div>
                        <p class="text-white font-medium mb-4">{{ $loop->iteration }}. {{ $q->question }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                            @foreach($q->options as $idx => $option)
                            <label class="cursor-pointer">
                                <input type="radio" name="q[{{ $q->id }}]" value="{{ $idx }}" class="hidden peer">
                                <div class="px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-[10px] text-center text-slate-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all font-bold uppercase tracking-wider">
                                    {{ $option }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" @click="step = 2" class="px-8 py-3 rounded-xl bg-slate-800 text-white font-semibold"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                <button type="submit" class="btn-premium">Kirim Asesmen <i class="fas fa-check ml-2"></i></button>
            </div>
        </div>
    </form>
</div>
@endsection
