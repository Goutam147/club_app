<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            {{ __('Club Photo Gallery') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Admin upload photo -->
            @hasanyrole('TH|President|Secretary')
                <div x-data="{ open: false }" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Admin Actions</h3>
                        <button @click="open = !open" class="px-4 py-2 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition duration-150">
                            <span x-show="!open">+ Upload Photo / Document</span>
                            <span x-show="open">Close Form</span>
                        </button>
                    </div>

                    <form x-show="open" method="POST" action="{{ route('gallery.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4 border-t border-slate-100 pt-6 text-slate-700">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="title" :value="__('Title / Caption')" />
                                <x-text-input id="title" name="title" class="block mt-1 w-full text-sm" type="text" required />
                            </div>
                            <div>
                                <x-input-label for="event_id" :value="__('Associated Event (Optional)')" />
                                <select name="event_id" id="event_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- Select Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="document" :value="__('Select Photo / Document')" />
                            <input id="document" name="document" type="file" accept="image/*" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm" required />
                            <span class="text-xs text-slate-400">Max size 4MB. Only images allowed.</span>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description / Story (Optional)')" />
                            <textarea id="description" name="description" rows="2" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-primary-button>
                                {{ __('Upload Photo') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endhasanyrole

            <!-- Photos Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @forelse($galleries as $item)
                    <div class="group bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md transition duration-150 relative">
                        <img src="{{ asset($item->doc_url) }}" alt="{{ $item->title }}" class="h-48 w-full object-cover group-hover:scale-105 transition duration-300">
                        
                        <div class="p-4">
                            <h4 class="font-bold text-slate-800 text-sm truncate">{{ $item->title }}</h4>
                            @if($item->event)
                                <div class="text-[10px] text-secondary font-bold uppercase tracking-wider mt-1 truncate">
                                    Event: {{ $item->event->title }}
                                </div>
                            @endif
                            @if($item->description)
                                <p class="text-slate-500 text-xs mt-2 leading-relaxed line-clamp-2">{{ $item->description }}</p>
                            @endif
                            
                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wide mt-3 flex justify-between items-center">
                                <span>By {{ $item->creator->name ?? 'System' }}</span>
                                @hasanyrole('TH|President|Secretary')
                                    <form method="POST" action="{{ route('gallery.destroy', $item) }}" onsubmit="return confirm('Delete this image?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-extrabold uppercase">
                                            Delete
                                        </button>
                                    </form>
                                @endhasanyrole
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-2xl text-slate-400 text-base border border-slate-100">
                        No photos uploaded yet in the gallery.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
