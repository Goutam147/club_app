<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            {{ __('Pending Registrations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl font-semibold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Users Table Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Pending User Approvals</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm text-left">
                        <thead class="bg-light text-slate-700 uppercase tracking-wider text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Contact Info</th>
                                <th class="px-6 py-4">Registered At</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-600">
                            @forelse($users as $user)
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
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 space-y-1">
                                        <div class="text-xs font-medium">{{ $user->email }}</div>
                                        <div class="text-xs font-medium">{{ $user->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-slate-400">
                                        {{ $user->created_at->format('M d, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('users.updateStatus', $user) }}" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl shadow-md transition duration-150">
                                                Approve & Activate
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('users.updateStatus', $user) }}" class="inline-block ms-2">
                                            @csrf
                                            <input type="hidden" name="status" value="inactive">
                                            <button type="submit" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition duration-150">
                                                Reject/Block
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">
                                        No pending member registrations.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
