<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-secondary"></i>
            {{ __('Club Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Admin Create Event form -->
            @hasanyrole('TH|President|Secretary')
                <div x-data="{ open: false }" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Admin Actions</h3>
                        <button @click="open = !open" class="px-4 py-2 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition duration-150">
                            <span x-show="!open">+ Add New Event</span>
                            <span x-show="open">Close Form</span>
                        </button>
                    </div>

                    <form x-show="open" method="POST" action="{{ route('events.store') }}" class="mt-6 space-y-4 border-t border-slate-100 pt-6 text-slate-700">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="title" :value="__('Event Title')" />
                                <x-text-input id="title" name="title" class="block mt-1 w-full text-sm" type="text" required />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select name="status" id="status" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Event Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date & Time')" />
                                <x-text-input id="start_date" name="start_date" class="block mt-1 w-full text-sm" type="datetime-local" required />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('End Date & Time')" />
                                <x-text-input id="end_date" name="end_date" class="block mt-1 w-full text-sm" type="datetime-local" required />
                            </div>
                            <div>
                                <x-input-label for="manager_id" :value="__('Event Manager / Coordinator')" />
                                <select name="manager_id" id="manager_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="">-- Select Manager --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-primary-button>
                                {{ __('Schedule Event') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endhasanyrole

            <!-- Events Listing -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($events as $event)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-slate-800">{{ $event->title }}</h3>
                                
                                @hasanyrole('TH|President|Secretary')
                                    <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition duration-150">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endhasanyrole
                            </div>

                            <div class="text-sm text-secondary font-bold mb-4 flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $event->start_date->format('M d, Y g:i A') }} - {{ $event->end_date->format('M d, Y g:i A') }}
                            </div>

                            @if($event->description)
                                <p class="text-slate-600 text-sm leading-relaxed mb-4">{!! nl2br(e($event->description)) !!}</p>
                            @endif
                        </div>

                        <div class="border-t border-slate-50 pt-4 mt-4 flex justify-between items-center text-xs text-slate-400 font-semibold">
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Coordinator: <span class="text-slate-600">{{ $event->manager->name ?? 'None' }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] uppercase font-bold 
                                @if($event->status === 'active') bg-emerald-100 text-emerald-800 @else bg-slate-100 text-slate-800 @endif">
                                {{ $event->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-2xl text-slate-400 text-base border border-slate-100">
                        No events planned.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
