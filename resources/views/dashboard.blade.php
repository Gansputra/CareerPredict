@extends('layouts.app')

@section('title', 'Your Career Companion')

@section('content')
<div class="space-y-8">
    <!-- Welcome Card & Match Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 relative overflow-hidden glass p-10 rounded-3xl" data-aos="fade-up">
            <div class="relative z-10">
                <h1 class="text-4xl font-extrabold text-white mb-4">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-slate-400 text-lg max-w-xl leading-relaxed">
                    “The best way to predict your future is to create it.” 
                    <br>Let’s discover your ideal career path today.
                </p>
                
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('assessment.index') }}" class="btn-premium px-8 py-3">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i> Retake Assessment
                    </a>
                    <a href="{{ route('jobs.index') }}" class="px-8 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition-all text-white font-semibold">
                        Explore Open Roles
                    </a>
                </div>
            </div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl"></div>
            <div class="absolute right-12 bottom-12 hidden xl:block opacity-30">
                <i class="fas fa-briefcase text-[160px] text-blue-500 animate-float"></i>
            </div>
        </div>

        <!-- Top Match Card -->
        <div class="glass-dark p-8 border-t-4 border-emerald-500 shadow-2xl" data-aos="fade-left">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">Top Career Match</h3>
            @if(count($topMatches) > 0)
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-1">{{ $topMatches[0]['job_title'] }}</h2>
                    <p class="text-emerald-500 font-bold text-lg">{{ $topMatches[0]['confidence'] }}% Compatibility</p>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 mb-6">
                    <p class="text-sm text-slate-300 italic">"{{ $topMatches[0]['explanation'] }}"</p>
                </div>
                <a href="{{ route('jobs.show', \App\Models\JobListing::find($topMatches[0]['job_id'])->slug) }}" class="text-blue-500 font-bold text-sm hover:underline">
                    View Role Requirements <i class="fas fa-arrow-right ml-1"></i>
                </a>
            @else
                <div class="text-center py-10">
                    <i class="fas fa-radar text-slate-700 text-4xl mb-4"></i>
                    <p class="text-slate-500 text-sm">Complete your assessment to see your top match.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Skill Radar Chart -->
        <div class="glass-dark p-8" data-aos="fade-right">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white">Personality Insights</h3>
                    <p class="text-xs text-slate-500 mt-1">Based on your recent assessment scores</p>
                </div>
                <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-chart-radar"></i>
                </div>
            </div>
            <div class="h-80">
                <canvas id="personalityRadar"></canvas>
            </div>
        </div>

        <!-- Progress & Suggestions -->
        <div class="space-y-8" data-aos="fade-left">
            <!-- Assessment Progress -->
            <div class="glass-dark p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Profile Strength</h3>
                    <span class="text-blue-500 font-bold text-sm">{{ round($progress) }}%</span>
                </div>
                <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    @if($progress < 100)
                        Complete all modules to unlock deeper AI-driven career insights.
                    @else
                        Your profile is fully optimized. Our AI is now delivering maximum precision matches.
                    @endif
                </p>
            </div>

            <!-- Smart Suggestions -->
            <div class="glass-dark p-8">
                <h3 class="text-lg font-bold text-white mb-6">AI Career Suggestions</h3>
                <div class="space-y-4">
                    @forelse(array_slice($topMatches, 1, 3) as $match)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-800 transition-colors cursor-pointer group">
                        <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center text-slate-500 group-hover:bg-blue-600/20 group-hover:text-blue-500 transition-all">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">Consider {{ $match['job_title'] }}</p>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest">{{ $match['confidence'] }}% Match Score</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500 italic">Start assessment to get suggestions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="glass-dark p-8" data-aos="fade-up">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold text-white">Recent Recommendation History</h3>
            <a href="#" class="text-blue-500 text-sm hover:underline">View All History</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($recentRecommendations as $rec)
            <div class="p-5 rounded-2xl bg-slate-800/30 border border-slate-700/50 card-hover">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-2">{{ $rec->job->category->name }}</p>
                <h4 class="text-sm font-bold text-white mb-4 line-clamp-1">{{ $rec->job->title }}</h4>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">{{ $rec->created_at->format('M d') }}</span>
                    <span class="text-xs font-bold text-white">{{ number_format($rec->confidence, 0) }}% CF</span>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-slate-600">
                <p>No recommendation history yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    const radarCtx = document.getElementById('personalityRadar').getContext('2d');
    
    const labels = {!! json_encode($personalityScores->pluck('category.name')) !!};
    const scores = {!! json_encode($personalityScores->pluck('score')) !!};

    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: labels.length ? labels : ['Analytical', 'Creative', 'Technical', 'Communication', 'Leadership'],
            datasets: [{
                label: 'Personality Profile',
                data: scores.length ? scores : [0, 0, 0, 0, 0],
                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(37, 99, 235, 1)'
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    pointLabels: { 
                        color: '#94a3b8', 
                        font: { size: 12, weight: '600' } 
                    },
                    ticks: { display: false, stepSize: 1 },
                    min: 0,
                    max: 5
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
@endsection
