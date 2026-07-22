@php
    $club = \App\Models\ClubMaster::first();
    $logoPath = $club && $club->logo ? $club->logo : null;
@endphp

@if($logoPath)
    <img src="{{ asset($logoPath) }}" alt="{{ $club->name ?? 'Club' }} Logo" width="36" height="36" decoding="async" fetchpriority="high" {{ $attributes->merge(['class' => 'h-9 w-9 object-cover rounded-lg shrink-0']) }}>
@else
    <div {{ $attributes->merge(['class' => 'h-9 w-9 bg-primary rounded-lg flex items-center justify-center text-white font-black text-sm shadow-sm shrink-0']) }}>
        {{ substr($club->name ?? 'BSC', 0, 1) }}
    </div>
@endif
