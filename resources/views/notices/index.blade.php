<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-bullhorn text-secondary"></i>
            {{ __('Club Notices Board') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Admin Create Notice form -->
            @hasanyrole('TH|President|Secretary')
                <div x-data="{ open: false }" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Admin Actions</h3>
                        <button @click="open = !open" class="px-4 py-2 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition duration-150">
                            <span x-show="!open">+ Post New Notice</span>
                            <span x-show="open">Close Form</span>
                        </button>
                    </div>

                    <form x-show="open" method="POST" action="{{ route('notices.store') }}" class="mt-6 space-y-4 border-t border-slate-100 pt-6 text-slate-700">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="title" :value="__('Notice Title')" />
                                <x-text-input id="title" name="title" class="block mt-1 w-full text-sm" type="text" required />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select name="status" id="status" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="active">Active (Visible)</option>
                                    <option value="inactive">Inactive (Hidden)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Notice Content / Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required></textarea>
                        </div>

                        <div>
                            <x-input-label for="note" :value="__('Internal / Additional Note (Optional)')" />
                            <x-text-input id="note" name="note" class="block mt-1 w-full text-sm" type="text" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_at" :value="__('Start Date (Optional)')" />
                                <x-text-input id="start_at" name="start_at" class="block mt-1 w-full text-sm" type="datetime-local" />
                            </div>
                            <div>
                                <x-input-label for="expiry_at" :value="__('Expiry Date (Optional)')" />
                                <x-text-input id="expiry_at" name="expiry_at" class="block mt-1 w-full text-sm" type="datetime-local" />
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-primary-button>
                                {{ __('Post Notice') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endhasanyrole

            <!-- Notices Listing -->
            <div class="grid grid-cols-1 gap-6">
                @forelse($notices as $notice)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 relative">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">{{ $notice->title }}</h3>
                                <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                    <span>Posted by <strong>{{ $notice->creator->name ?? 'System' }}</strong></span>
                                    <span>•</span>
                                    <span>{{ $notice->created_at->format('M d, Y g:i A') }}</span>
                                    @hasanyrole('TH|President|Secretary')
                                        <span>•</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold 
                                            @if($notice->status === 'active') bg-emerald-100 text-emerald-800 @else bg-slate-100 text-slate-800 @endif">
                                            {{ $notice->status }}
                                        </span>
                                    @endhasanyrole
                                </div>
                            </div>
                            
                            @hasanyrole('TH|President|Secretary')
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('notices.destroy', $notice) }}" onsubmit="return confirm('Are you sure you want to delete this notice?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition duration-150">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endhasanyrole
                        </div>

                        <div class="text-slate-600 text-sm leading-relaxed mb-4">
                            {!! nl2br(e($notice->description)) !!}
                        </div>

                        @if($notice->note)
                            <div class="bg-light/60 border-l-4 border-secondary p-3 rounded-r-lg text-xs text-slate-700 italic">
                                <strong>Note:</strong> {{ $notice->note }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white p-12 text-center rounded-2xl text-slate-400 text-base shadow-sm border border-slate-100">
                        No club notices posted at this time.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
