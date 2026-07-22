<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-secondary"></i>
            {{ __('Roles & Permissions Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Add Role Card -->
            <div class="max-w-2xl bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 text-slate-700">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Add New Custom Role</h3>
                <form method="POST" action="{{ route('roles.store') }}" class="flex flex-col sm:flex-row gap-4">
                    @csrf
                    <div class="flex-grow">
                        <x-text-input name="name" class="w-full text-sm" placeholder="Role Name (e.g. Treasurer)" required />
                    </div>
                    <x-primary-button class="justify-center">
                        Add Role
                    </x-primary-button>
                </form>
            </div>

            <!-- Roles Matrix List -->
            <div class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 text-slate-700">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b border-slate-100 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-primary">Roles & Permissions Matrix</h3>
                            <p class="text-sm text-slate-500 mt-1">Manage feature-level permissions for each user role in the system. Use the toggles to assign or revoke permissions.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('roles-permissions.sync') }}">
                        @csrf
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="py-4 px-6 text-left font-bold text-slate-700 uppercase tracking-wider">
                                            Permission Name
                                        </th>
                                        @foreach($roles as $role)
                                            <th scope="col" class="py-4 px-6 text-center font-bold text-slate-700 uppercase tracking-wider">
                                                <div class="flex flex-col items-center gap-2">
                                                    <span class="px-3 py-1.5 bg-light text-primary text-xs font-black rounded-lg uppercase border border-blue-100 shadow-sm">
                                                        {{ $role->name }}
                                                    </span>
                                                    @if(!in_array($role->name, ['TH', 'President', 'Secretary', 'Cashier', 'Member']))
                                                        <button type="button" 
                                                            onclick="if(confirm('Are you sure you want to delete the role \'{{ $role->name }}\'?')) { 
                                                                let form = document.getElementById('delete-role-form'); 
                                                                form.action = '{{ route('roles.destroy', $role) }}'; 
                                                                form.submit(); 
                                                            }"
                                                            class="text-rose-500 hover:text-rose-700 text-xs font-medium transition hover:underline">
                                                            Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse($permissions as $permission)
                                        <tr class="hover:bg-slate-50/55 transition">
                                            <td class="py-4 px-6 font-medium text-slate-800">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-slate-700 text-sm tracking-wide">
                                                        {{ str_replace('_', ' ', ucwords($permission->name, '_')) }}
                                                    </span>
                                                    <span class="text-xs text-slate-400 font-normal">
                                                        <code>{{ $permission->name }}</code>
                                                    </span>
                                                </div>
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                                    <div class="flex justify-center items-center">
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" name="matrix[{{ $role->id }}][]" value="{{ $permission->name }}"
                                                                @checked($role->permissions->contains('name', $permission->name))
                                                                class="sr-only peer">
                                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                                                        </label>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($roles) + 1 }}" class="py-8 px-6 text-center text-slate-400 italic">
                                                No permissions defined yet. Use the forms above to add custom permissions.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($permissions->isNotEmpty())
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-secondary hover:bg-secondary-hover text-white text-sm font-bold rounded-xl shadow-md transition duration-150 transform hover:-translate-y-[1px] hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                    </svg>
                                    Save Permissions Matrix
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Hidden form for deleting roles to avoid nesting forms in HTML -->
    <form id="delete-role-form" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-app-layout>
