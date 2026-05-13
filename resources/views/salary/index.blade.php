@extends('layouts.app')

@section('title', 'Salary Insights')

@section('content')
<div class="max-w-5xl mx-auto py-8 space-y-8" data-aos="fade-up">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-white mb-2">Salary Insights</h1>
        <p class="text-slate-400 text-sm max-w-2xl mx-auto">
            Explore the average salaries for the most in‑demand tech roles. These figures are for reference only; actual salaries may vary based on experience, location, and company.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($salaryData as $item)
        <div class="glass-dark rounded-2xl p-4 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600/20 text-blue-400">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h2 class="text-xl font-semibold text-white">{{ $item['role'] }}</h2>
            </div>
            <p class="text-slate-300 text-lg font-medium">Average Salary: <span class="text-amber-400">{{ $item['average'] }}</span></p>
        </div>
        @endforeach
    </div>
</div>
@endsection
