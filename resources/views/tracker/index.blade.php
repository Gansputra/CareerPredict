@extends('layouts.app')

@section('title', 'Application Tracker')

@section('content')
<div class="max-w-full space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 animate-fade-in">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-bold tracking-widest uppercase mb-3 border border-amber-500/20">
                <i class="fas fa-clipboard-list"></i>
                Kanban Board
            </div>
            <h1 class="text-3xl font-bold text-white mb-1">
                Application <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-400">Tracker</span>
            </h1>
            <p class="text-slate-400 text-sm">Drag & drop cards between columns to update status. Save jobs from Job Explorer!</p>
        </div>

        {{-- Stats --}}
        <div class="flex flex-wrap gap-3">
            @php $total = 0; foreach ($columns as $col) { $total += $col['cards']->count(); } @endphp
            <div class="glass-dark px-4 py-2 rounded-xl text-center">
                <p class="text-xl font-extrabold text-white" id="stat-total">{{ $total }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-500">Total</p>
            </div>
            @foreach ($columns as $key => $col)
            <div class="glass-dark px-4 py-2 rounded-xl text-center">
                <p class="text-xl font-extrabold text-white" id="stat-{{ $key }}">{{ $col['cards']->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-500">{{ $col['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-check-circle"></i>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('warning'))
    <div class="bg-amber-500/10 border border-amber-500 text-amber-400 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fas fa-exclamation-triangle"></i>
        <p class="font-medium">{{ session('warning') }}</p>
    </div>
    @endif

    {{-- Kanban Board --}}
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-5 min-w-max">

            @foreach($columns as $key => $col)
            @php
                $colorMap = [
                    'slate'   => ['header' => 'bg-slate-700/50 text-slate-300',   'dot' => 'bg-slate-400',   'badge' => 'bg-slate-600 text-slate-200',   'ring' => 'ring-slate-700/50'],
                    'blue'    => ['header' => 'bg-blue-600/20 text-blue-300',     'dot' => 'bg-blue-400',    'badge' => 'bg-blue-600/30 text-blue-300',   'ring' => 'ring-blue-500/20'],
                    'amber'   => ['header' => 'bg-amber-500/20 text-amber-300',   'dot' => 'bg-amber-400',   'badge' => 'bg-amber-500/30 text-amber-300', 'ring' => 'ring-amber-500/20'],
                    'emerald' => ['header' => 'bg-emerald-500/20 text-emerald-300','dot' => 'bg-emerald-400','badge' => 'bg-emerald-500/30 text-emerald-300','ring'=> 'ring-emerald-500/20'],
                    'red'     => ['header' => 'bg-red-500/20 text-red-300',       'dot' => 'bg-red-400',     'badge' => 'bg-red-500/30 text-red-300',     'ring' => 'ring-red-500/20'],
                ];
                $c = $colorMap[$col['color']];
            @endphp

            <div class="w-72 flex flex-col gap-3 animate-fade-in" style="animation-delay: {{ $loop->iteration * 60 }}ms">

                {{-- Column header --}}
                <div class="flex items-center justify-between px-4 py-2.5 rounded-xl {{ $c['header'] }} ring-1 {{ $c['ring'] }}">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="{{ $col['icon'] }}"></i>
                        {{ $col['label'] }}
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $c['badge'] }}" id="badge-{{ $key }}">
                        {{ $col['cards']->count() }}
                    </span>
                </div>

                {{-- Droppable card list --}}
                <div class="kanban-column flex flex-col gap-3 min-h-[80px] rounded-2xl p-1 transition-colors"
                     data-status="{{ $key }}" id="column-{{ $key }}">

                    @forelse($col['cards'] as $app)
                    <div class="kanban-card glass-dark rounded-2xl p-4 ring-1 {{ $c['ring'] }} hover:shadow-lg transition-all duration-200 group cursor-grab active:cursor-grabbing"
                         data-id="{{ $app->id }}" x-data="{ showMenu: false }">

                        {{-- Company + Job --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-indigo-500/20 shrink-0">
                                    {{ strtoupper(substr($app->job->company_name ?? 'J', 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-white truncate group-hover:text-blue-400 transition-colors">{{ $app->job->company_name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ $app->job->title }}</p>
                                </div>
                            </div>

                            {{-- Actions dropdown --}}
                            <div class="relative">
                                <button @click="showMenu = !showMenu" class="p-1.5 rounded-lg hover:bg-slate-800 text-slate-500 hover:text-white transition-colors">
                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                </button>
                                <div x-show="showMenu" @click.away="showMenu = false" x-transition
                                     class="absolute right-0 top-8 z-20 w-44 bg-[#1e293b] border border-slate-700 rounded-xl shadow-2xl p-2 space-y-1">

                                    <a href="{{ route('jobs.show', $app->job->slug) }}" class="block px-3 py-2 rounded-lg text-xs text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                                        <i class="fas fa-external-link-alt text-[10px] mr-2"></i> View Job
                                    </a>

                                    <form action="{{ route('tracker.destroy', $app) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-xs text-red-400 hover:bg-red-500/10 transition-colors">
                                            <i class="fas fa-trash text-[10px] mr-2"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Meta --}}
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-slate-800 text-slate-400 text-[10px]">
                                <i class="fas fa-map-marker-alt"></i> {{ $app->job->location ?? 'Remote' }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-slate-800 text-slate-400 text-[10px]">
                                <i class="fas fa-tag"></i> {{ $app->job->category->name ?? 'General' }}
                            </span>
                        </div>

                        {{-- Date --}}
                        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-slate-700/50">
                            <span class="w-2 h-2 rounded-full {{ $c['dot'] }}"></span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">{{ $app->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="kanban-empty flex flex-col items-center justify-center py-10 text-slate-600 border-2 border-dashed border-slate-700/60 rounded-2xl">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-xs">Drop cards here</p>
                    </div>
                    @endforelse
                </div>

            </div>
            @endforeach

        </div>
    </div>

    {{-- Empty state CTA --}}
    @if($total === 0)
    <div class="glass p-10 text-center animate-fade-in">
        <div class="w-20 h-20 mx-auto bg-slate-800 rounded-3xl flex items-center justify-center mb-6">
            <i class="fas fa-briefcase text-slate-600 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-white mb-3">Start Tracking Your Applications</h3>
        <p class="text-slate-400 max-w-md mx-auto mb-6">Browse jobs and click "Save to Tracker" to add them here. Drag cards between columns to update status!</p>
        <a href="{{ route('jobs.index') }}" class="btn-premium px-8 py-3">
            <i class="fas fa-search mr-2"></i> Browse Jobs
        </a>
    </div>
    @endif

    {{-- Toast notification --}}
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
        <div class="bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-2xl shadow-emerald-500/30 flex items-center gap-3 text-sm font-bold animate-fade-in">
            <i class="fas fa-check-circle"></i>
            <span id="toast-msg">Status updated!</span>
        </div>
    </div>

    {{-- Safelist for Tailwind JIT --}}
    <div class="hidden">
        <div class="bg-slate-700/50 text-slate-300 bg-slate-600 text-slate-200 ring-slate-700/50 bg-slate-400"></div>
        <div class="bg-blue-600/20 text-blue-300 bg-blue-600/30 ring-blue-500/20 bg-blue-400"></div>
        <div class="bg-amber-500/20 text-amber-300 bg-amber-500/30 ring-amber-500/20 bg-amber-400"></div>
        <div class="bg-emerald-500/20 text-emerald-300 bg-emerald-500/30 ring-emerald-500/20 bg-emerald-400"></div>
        <div class="bg-red-500/20 text-red-300 bg-red-500/30 ring-red-500/20 bg-red-400"></div>
    </div>
</div>

@push('scripts')
{{-- SortableJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const columns = document.querySelectorAll('.kanban-column');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    columns.forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 250,
            ghostClass: 'opacity-30',
            chosenClass: 'scale-105',
            dragClass: 'shadow-2xl',
            easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
            filter: '.kanban-empty',
            onAdd: function(evt) {
                const cardId = evt.item.dataset.id;
                const newStatus = evt.to.dataset.status;

                // Hide empty placeholders if a card was added
                const emptyEl = evt.to.querySelector('.kanban-empty');
                if (emptyEl) emptyEl.remove();

                // Show empty placeholder if source column is now empty
                if (evt.from.children.length === 0) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'kanban-empty flex flex-col items-center justify-center py-10 text-slate-600 border-2 border-dashed border-slate-700/60 rounded-2xl';
                    placeholder.innerHTML = '<i class="fas fa-inbox text-3xl mb-2"></i><p class="text-xs">Drop cards here</p>';
                    evt.from.appendChild(placeholder);
                }

                // Update badge counts
                updateBadgeCounts();

                // AJAX update
                fetch(`/tracker/${cardId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ status: newStatus }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Moved to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + '!');
                    }
                })
                .catch(() => {
                    showToast('Error updating status', true);
                });
            }
        });
    });

    function updateBadgeCounts() {
        const statuses = ['wishlist', 'applied', 'interview', 'offered', 'rejected'];
        let total = 0;
        statuses.forEach(status => {
            const col = document.getElementById('column-' + status);
            const cards = col ? col.querySelectorAll('.kanban-card').length : 0;
            total += cards;
            const badge = document.getElementById('badge-' + status);
            const stat = document.getElementById('stat-' + status);
            if (badge) badge.textContent = cards;
            if (stat) stat.textContent = cards;
        });
        const totalEl = document.getElementById('stat-total');
        if (totalEl) totalEl.textContent = total;
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const msg = document.getElementById('toast-msg');
        msg.textContent = message;
        toast.classList.remove('hidden');
        toast.querySelector('div').className = toast.querySelector('div').className.replace(/bg-(emerald|red)-500/g, isError ? 'bg-red-500' : 'bg-emerald-500');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }
});
</script>
@endpush
@endsection
