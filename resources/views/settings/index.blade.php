<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-gear text-secondary"></i>
            {{ __('Club settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Club Master Settings Form -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 text-slate-700">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Club Master Profile Configuration</h3>
                
                <form method="POST" action="{{ route('settings.updateClub') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="flex items-center space-x-6 flex-col sm:flex-row text-center sm:text-left">
                        @if($club && $club->logo)
                            <img src="{{ asset($club->logo) }}" alt="Logo" class="h-20 w-auto object-contain rounded-lg border shadow-sm">
                        @endif
                        <div>
                            <x-input-label for="logo" :value="__('Change Club Logo')" />
                            <input id="logo" name="logo" type="file" accept="image/*" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm" />
                            <span class="text-xs text-slate-400">Accepted formats: jpg, jpeg, png. Max size: 2MB.</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Club Name')" />
                            <x-text-input id="name" name="name" class="block mt-1 w-full text-sm" type="text" :value="old('name', $club->name)" required />
                        </div>
                        <div>
                            <x-input-label for="estd" :value="__('Year Established (ESTD)')" />
                            <x-text-input id="estd" name="estd" class="block mt-1 w-full text-sm" type="text" :value="old('estd', $club->estd)" placeholder="e.g. 2020" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Club Office Address')" />
                        <textarea id="address" name="address" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Address...">{{ old('address', $club->address) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Save Club Profile') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Maintenance Mode Form -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 text-slate-700">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Maintenance Mode (Site Lockdown)</h3>
                
                <form method="POST" action="{{ route('settings.toggleMaintenance') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="maintenance_mode" :value="__('Maintenance Status')" />
                        <select name="maintenance_mode" id="maintenance_mode" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            <option value="0" @selected($maintenanceMode === '0')>Disabled (Site is Live & Public)</option>
                            <option value="1" @selected($maintenanceMode === '1')>Enabled (Complete Site Lockdown)</option>
                        </select>
                        <span class="text-xs text-rose-500 font-semibold mt-1.5 block">Warning: Enabling maintenance mode will block all routes for public/normal users! You can disable it here or run "php artisan site:up" from console.</span>
                    </div>

                    <div>
                        <x-input-label for="maintenance_message" :value="__('Custom Maintenance Message')" />
                        <textarea id="maintenance_message" name="maintenance_message" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="E.g. We are upgrading database systems, please wait...">{{ old('maintenance_message', $maintenanceMessage) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="bg-rose-600 hover:bg-rose-700 focus:ring-rose-500">
                            {{ __('Apply Maintenance Settings') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
