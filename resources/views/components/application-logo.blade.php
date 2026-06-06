@php
    $club = \App\Models\ClubMaster::first();
    $logoPath = $club && $club->logo ? $club->logo : null;
    $logoExists = $logoPath && file_exists(public_path($logoPath));
@endphp

@if($logoExists)
    <img src="{{ asset($logoPath) }}" alt="{{ $club->name ?? 'Club' }} Logo" {{ $attributes->merge(['class' => 'h-9 w-auto object-contain rounded']) }}>
@else
    <div {{ $attributes->merge(['class' => 'h-9 w-9 bg-primary rounded-lg flex items-center justify-center text-white font-black text-sm shadow-sm']) }}>
        {{ substr($club->name ?? 'BSC', 0, 1) }}
    </div>
@endif
