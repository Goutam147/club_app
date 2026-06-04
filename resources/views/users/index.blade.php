<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            {{ __('Member Directory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl font-semibold shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Users Table Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Bhimchak Sunrise Club Directory</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm text-left">
                        <thead class="bg-light text-slate-700 uppercase tracking-wider text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">Member</th>
                                <th class="px-6 py-4">Contact Info</th>
                                <th class="px-6 py-4">Role/Position</th>
                                <th class="px-6 py-4">Status</th>
                                @hasanyrole('TH|President|Secretary')
                                    <th class="px-6 py-4 text-right">Actions</th>
                                @endhasanyrole
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-600">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                    <td class="px-6 py-4 flex items-center space-x-4">
                                        @if($user->profile)
                                            <img src="{{ asset($user->profile) }}" alt="Avatar" class="h-10 w-10 rounded-full object-cover shadow-sm">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shadow-inner">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-bold text-slate-800 block text-base">{{ $user->name }}</span>
                                            <span class="text-xs text-slate-400">Registered {{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 space-y-1">
                                        <div class="flex items-center gap-1.5 text-xs font-medium">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            {{ $user->email }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs font-medium">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $user->phone }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            {{ $user->roles->pluck('name')->join(', ') ?: 'No Role Assigned' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-xs font-extrabold rounded-full tracking-wide uppercase 
                                            @if($user->status === 'active') bg-emerald-100 text-emerald-800 
                                            @elseif($user->status === 'inactive') bg-rose-100 text-rose-800 
                                            @else bg-amber-100 text-amber-800 @endif">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    
                                    @hasanyrole('TH|President|Secretary')
                                        <td class="px-6 py-4 text-right space-y-2">
                                            <!-- Role Modifier -->
                                            <form method="POST" action="{{ route('users.updateRole', $user) }}" class="inline-block">
                                                @csrf
                                                <select name="role" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-1 pl-2.5 pr-8 focus:border-indigo-500 focus:ring-indigo-500">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>

                                            <!-- Status Modifier -->
                                            <form method="POST" action="{{ route('users.updateStatus', $user) }}" class="inline-block ms-2">
                                                @csrf
                                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-1 pl-2.5 pr-8 focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="pending" @selected($user->status === 'pending')>Pending</option>
                                                    <option value="active" @selected($user->status === 'active')>Active</option>
                                                    <option value="inactive" @selected($user->status === 'inactive')>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                    @endhasanyrole
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
