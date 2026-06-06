<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $club = \App\Models\ClubMaster::first();
        $logoPath = $club->logo ?? null;
        $logoExists = $logoPath && file_exists(public_path($logoPath));
        $clubName = $club->name ?? 'Bhimchak Sunrise Club';
    @endphp
    <body class="font-sans text-gray-900 antialiased min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-slate-100 to-blue-50/70">
        <!-- Guest Navbar -->
        <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Brand (redirects to Home) -->
                    <a href="/" class="flex items-center space-x-3 group">
                        @if($logoExists)
                            <img src="{{ asset($logoPath) }}" alt="Logo" class="h-10 w-auto object-contain rounded transition duration-300 group-hover:scale-105">
                        @endif
                        <span class="font-extrabold text-lg text-primary tracking-tight transition duration-300 group-hover:text-primary-hover">{{ $clubName }}</span>
                    </a>

                    <!-- Action Button -->
                    <div class="flex items-center">
                        @if(Route::currentRouteName() === 'login')
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-secondary hover:bg-secondary-hover rounded-xl shadow-md transition duration-150">Register</a>
                        @elseif(Route::currentRouteName() === 'register')
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white bg-secondary hover:bg-secondary-hover rounded-xl shadow-md transition duration-150">Login</a>
                        @else
                            <!-- Fallback for other auth pages like forgot-password, show login -->
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white bg-secondary hover:bg-secondary-hover rounded-xl shadow-md transition duration-150">Login</a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Centering Container -->
        <div class="flex-grow flex flex-col justify-start items-center px-3 py-4 sm:py-8 sm:px-6">
            <div class="mb-4 text-center">
                <a href="/" class="inline-flex flex-col items-center gap-2 group">
                    @if($logoExists)
                        <img src="{{ asset($logoPath) }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-md rounded-2xl transition duration-300 group-hover:scale-105">
                    @else
                        <div class="h-16 w-16 bg-primary rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-md transition duration-300 group-hover:scale-105">
                            {{ substr($clubName, 0, 1) }}
                        </div>
                    @endif
                    <span class="mt-2 text-lg font-black tracking-tight text-slate-800 uppercase">{{ $clubName }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white/80 backdrop-blur-md border border-slate-100/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl px-5 py-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>

        <x-toast />
    </body>
</html>
