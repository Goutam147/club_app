<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - {{ $club->name ?? 'Bhimchak Sunrise Club' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light min-h-screen flex items-center justify-center font-sans antialiased text-gray-800">
    <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-8 text-center border-t-8 border-primary mx-4">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            @if($club && $club->logo)
                <img src="{{ asset($club->logo) }}" alt="Logo" class="h-24 w-auto object-contain rounded-lg shadow-sm">
            @else
                <div class="h-20 w-20 bg-primary/10 rounded-full flex items-center justify-center">
                    <span class="text-primary font-bold text-2xl">BSC</span>
                </div>
            @endif
        </div>

        <!-- Heading -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-primary mb-2">
            Under Maintenance
        </h1>
        
        <!-- Club Name -->
        <p class="text-sm font-semibold text-secondary mb-6 tracking-wide uppercase">
            {{ $club->name ?? 'Bhimchak Sunrise Club' }}
        </p>

        <!-- Message -->
        <div class="bg-light rounded-xl p-4 mb-6 border border-primary/10 text-gray-600 text-sm leading-relaxed">
            {{ $message }}
        </div>

        <!-- Footer -->
        <div class="text-xs text-gray-500 font-medium">
            Please check back again later. Thank you for your patience!
        </div>
    </div>
</body>
</html>
