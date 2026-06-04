<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name ?? 'Bhimchak Sunrise Club' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-3">
                    @if($club && $club->logo)
                        <img src="{{ asset($club->logo) }}" alt="Logo" class="h-10 w-auto object-contain rounded">
                    @endif
                    <span class="font-extrabold text-lg text-primary tracking-tight">{{ $club->name ?? 'Bhimchak Sunrise Club' }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-md transition duration-150">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-primary hover:text-primary-hover transition duration-150">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-secondary hover:bg-secondary-hover rounded-xl shadow-md transition duration-150">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative bg-gradient-to-br from-primary to-secondary py-20 text-white text-center overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.1)_1px,transparent_1px)] bg-[size:20px_20px] opacity-35"></div>
        <div class="max-w-4xl mx-auto px-6 relative z-10">
            <h1 class="text-4xl sm:text-5xl font-black mb-4 tracking-tight drop-shadow-sm">Welcome to {{ $club->name ?? 'Bhimchak Sunrise Club' }}</h1>
            <p class="text-lg sm:text-xl font-light text-blue-100 max-w-2xl mx-auto mb-8">
                Fostering community growth, sportsmanship, and coordination through active member engagement and transparent administration.
            </p>
            @guest
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-6 py-3 font-semibold text-primary bg-white hover:bg-blue-50 rounded-xl shadow-lg transform hover:-translate-y-0.5 transition duration-150">Join Our Club</a>
                </div>
            @endguest
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">
        
        <!-- Notices & Events Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Notices Feed -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
                    <svg class="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Recent Announcements
                </h2>
                
                <div class="space-y-4">
                    @forelse($notices as $notice)
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-100 transition duration-150">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-lg text-slate-800">{{ $notice->title }}</h3>
                                <span class="text-xs text-slate-500 font-medium">{{ $notice->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed mb-4">{{ $notice->description }}</p>
                            @if($notice->note)
                                <div class="bg-light/50 border-l-4 border-secondary p-3 rounded-r-lg text-xs text-slate-700 italic">
                                    <strong>Note:</strong> {{ $notice->note }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white p-8 text-center rounded-2xl text-slate-500 text-sm shadow-sm border border-slate-100">
                            No notices posted yet. Check back later!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Events Sidebar -->
            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
                    <svg class="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Upcoming Events
                </h2>
                
                <div class="space-y-4">
                    @forelse($events as $event)
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition duration-150">
                            <h3 class="font-bold text-base text-slate-800 mb-1">{{ $event->title }}</h3>
                            <div class="text-xs text-secondary font-semibold mb-3">
                                {{ $event->start_date->format('M d, g:i A') }} - {{ $event->end_date->format('g:i A') }}
                            </div>
                            @if($event->description)
                                <p class="text-slate-600 text-xs leading-relaxed mb-3">{{ Str::limit($event->description, 100) }}</p>
                            @endif
                            @if($event->manager)
                                <div class="text-[10px] uppercase tracking-wider text-slate-400 font-bold flex items-center gap-1">
                                    <span>Coordinator:</span>
                                    <span class="text-slate-600">{{ $event->manager->name }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white p-8 text-center rounded-2xl text-slate-500 text-sm shadow-sm border border-slate-100">
                            No active events scheduled.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Gallery Section -->
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
                <svg class="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Club Gallery
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($galleries as $item)
                    <div class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-150 border border-slate-100">
                        <img src="{{ asset($item->doc_url) }}" alt="{{ $item->title }}" class="h-48 w-full object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-150 flex flex-col justify-end p-4 text-white">
                            <span class="font-bold text-xs">{{ $item->title }}</span>
                            @if($item->event)
                                <span class="text-[9px] text-blue-200 mt-1 uppercase">{{ $item->event->title }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-8 text-center rounded-2xl text-slate-500 text-sm shadow-sm border border-slate-100">
                        No gallery items uploaded yet.
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 py-8 text-slate-400 text-center text-xs mt-16 border-t border-slate-800">
        <p>&copy; {{ date('Y') }} {{ $club->name ?? 'Bhimchak Sunrise Club' }}. All rights reserved.</p>
        @if($club && $club->address)
            <p class="mt-2 text-slate-500">{{ $club->address }} @if($club->estd) | Established {{ $club->estd }} @endif</p>
        @endif
    </footer>

</body>
</html>
