<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-house text-secondary"></i>
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Club Banner -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center space-x-6 text-center md:text-left flex-col md:flex-row">
                    @if($club && $club->logo)
                        <img src="{{ asset($club->logo) }}" alt="Logo" class="h-20 w-auto object-contain rounded-lg shadow-sm">
                    @else
                        <div class="h-16 w-16 bg-primary/10 rounded-full flex items-center justify-center font-bold text-primary">BSC</div>
                    @endif
                    <div>
                        <h3 class="text-2xl font-black text-slate-800">{{ $club->name ?? 'Bhimchak Sunrise Club' }}</h3>
                        <p class="text-sm text-slate-500 mt-1">
                            Welcome back, <strong>{{ Auth::user()->name }}</strong> (Role: {{ Auth::user()->roles->pluck('name')->join(', ') }})
                        </p>
                        @if($club && $club->address)
                            <p class="text-xs text-slate-400 mt-1">{{ $club->address }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('transactions.create') }}" class="px-5 py-2.5 bg-secondary hover:bg-secondary-hover text-white font-semibold text-sm rounded-xl shadow-md transition duration-150 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Submit Receipt
                    </a>
                </div>
            </div>

            <!-- Dashboard Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Notices Section -->
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="text-xl font-bold text-primary flex items-center gap-2">
                        <svg class="h-5 w-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Latest Bulletins & Notices
                    </h3>
                    
                    <div class="space-y-4">
                        @forelse($notices as $notice)
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-lg text-slate-800">{{ $notice->title }}</h4>
                                    <span class="text-xs text-slate-400 font-medium">{{ $notice->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <p class="text-slate-600 text-sm leading-relaxed mb-3">{{ $notice->description }}</p>
                                @if($notice->note)
                                    <div class="bg-light/60 border-l-4 border-secondary p-3 rounded-r-lg text-xs text-slate-600 italic">
                                        <strong>Note:</strong> {{ $notice->note }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white p-8 text-center rounded-2xl text-slate-400 text-sm border border-slate-100">
                                No active bulletins posted.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Events Section -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-primary flex items-center gap-2">
                        <svg class="h-5 w-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Club Events
                    </h3>
                    
                    <div class="space-y-4">
                        @forelse($events as $event)
                            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition duration-150">
                                <h4 class="font-bold text-slate-800 text-sm">{{ $event->title }}</h4>
                                <div class="text-xs text-secondary font-semibold mt-1 mb-2">
                                    {{ $event->start_date->format('M d, g:i A') }} - {{ $event->end_date->format('g:i A') }}
                                </div>
                                @if($event->description)
                                    <p class="text-slate-500 text-xs leading-relaxed mb-3">{{ $event->description }}</p>
                                @endif
                                @if($event->manager)
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        Manager: <span class="text-slate-600">{{ $event->manager->name }}</span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white p-6 text-center rounded-2xl text-slate-400 text-sm border border-slate-100">
                                No events planned.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
