<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-user text-secondary"></i>
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-2 sm:py-12" x-data="{ activeForm: null }">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Premium Mobile-Style Profile Card -->
            <div class="bg-white overflow-hidden shadow-xl rounded-3xl border border-slate-100">
                <!-- Top Gradient Banner -->
                <div class="h-32 bg-gradient-to-tr from-primary to-secondary relative">
                    <!-- Role Badge absolute top-right -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-xs font-black rounded-full uppercase tracking-wider border border-white/10 shadow-sm">
                            {{ $user->roles->pluck('name')->join(', ') ?: 'Member' }}
                        </span>
                    </div>
                </div>

                <!-- Avatar Profile Info Container -->
                <div class="px-6 pb-6 text-center -mt-16 relative z-10">
                    <div class="inline-block relative">
                        @if($user->profile)
                            <img src="{{ asset($user->profile) }}" alt="Avatar" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto">
                        @else
                            <div class="h-32 w-32 rounded-full bg-light border-4 border-white shadow-lg mx-auto flex items-center justify-center text-primary font-black text-3xl shadow-inner">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                        @endif
                        <!-- Status indicator dot (active) -->
                        <span class="absolute bottom-2 right-2 block h-5 w-5 rounded-full border-4 border-white bg-emerald-500 shadow-md"></span>
                    </div>

                    <h3 class="text-2xl font-black text-slate-800 mt-4 leading-tight">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Club Member</p>

                    <!-- Profile Info details -->
                    <div class="mt-6 border-t border-slate-100 pt-6 space-y-4 text-left text-slate-600">
                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <div class="h-9 w-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="flex-grow">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Email Address</span>
                                <span class="text-slate-800 font-semibold break-all">{{ $user->email }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <div class="h-9 w-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div class="flex-grow">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Phone Number</span>
                                <span class="text-slate-800 font-semibold">{{ $user->phone ?? 'Not set' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <div class="h-9 w-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="flex-grow">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Date Joined</span>
                                <span class="text-slate-800 font-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5 text-sm font-medium">
                            <div class="h-9 w-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                            <div class="flex-grow">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Total Donated</span>
                                <span class="text-emerald-600 font-black text-sm">₹{{ number_format($totalDonated, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2 Main Action Buttons -->
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <button type="button" 
                            @click="activeForm = (activeForm === 'profile' ? null : 'profile')"
                            :class="activeForm === 'profile' ? 'bg-secondary text-white border-secondary' : 'bg-slate-55 text-slate-700 hover:bg-slate-100 border-slate-200'"
                            class="w-full py-3 px-4 text-xs font-bold rounded-2xl border transition duration-150 flex items-center justify-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path>
                            </svg>
                            Edit Profile
                        </button>
                        <button type="button" 
                            @click="activeForm = (activeForm === 'password' ? null : 'password')"
                            :class="activeForm === 'password' ? 'bg-primary text-white border-primary' : 'bg-slate-55 text-slate-700 hover:bg-slate-100 border-slate-200'"
                            class="w-full py-3 px-4 text-xs font-bold rounded-2xl border transition duration-150 flex items-center justify-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path>
                            </svg>
                            Password
                        </button>
                    </div>
                </div>
            </div>

            <!-- Update Profile Form Expandable Card -->
            <div x-show="activeForm === 'profile'" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4"
                class="p-6 bg-white shadow-xl rounded-3xl border border-slate-100"
                style="display: none;">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password Form Expandable Card -->
            <div x-show="activeForm === 'password'" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4"
                class="p-6 bg-white shadow-xl rounded-3xl border border-slate-100"
                style="display: none;">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Premium Mobile-Style Transaction History Card -->
            <div class="bg-white overflow-hidden shadow-xl rounded-3xl border border-slate-100 p-6 space-y-4"
                x-data="{
                    transactions: [],
                    page: 1,
                    hasMore: false,
                    loading: false,
                    expandedRows: [],
                    init() {
                        this.fetchTransactions();
                    },
                    fetchTransactions() {
                        if (this.loading) return;
                        this.loading = true;
                        fetch('{{ route('profile.transactions') }}?page=' + this.page)
                            .then(res => res.json())
                            .then(data => {
                                this.transactions = [...this.transactions, ...data.transactions];
                                this.hasMore = data.has_more;
                                this.page++;
                                this.loading = false;
                            })
                            .catch(err => {
                                this.loading = false;
                                console.error('Error fetching transactions:', err);
                            });
                    },
                    toggleRow(id) {
                        if (this.expandedRows.includes(id)) {
                            this.expandedRows = this.expandedRows.filter(r => r !== id);
                        } else {
                            this.expandedRows.push(id);
                        }
                    }
                }">
                
                <h4 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-secondary"></i>
                    Transaction History
                </h4>

                <div class="space-y-1.5">
                    <!-- Table Column Header (shown once at top) -->
                    <div class="grid grid-cols-4 gap-2 px-3.5 py-2.5 bg-slate-50/70 rounded-xl text-slate-400 text-[9px] font-bold uppercase tracking-wider items-center text-center border border-slate-100/80">
                        <div class="text-left">Date & Time</div>
                        <div>Amount</div>
                        <div>Method</div>
                        <div class="text-right">Status</div>
                    </div>

                    <div class="space-y-2">
                        <!-- Loading skeleton when list is empty and loading is true -->
                        <template x-if="transactions.length === 0 && loading">
                            <div class="space-y-2">
                                <template x-for="i in 3">
                                    <div class="animate-pulse bg-slate-50 h-12 rounded-xl border border-slate-100"></div>
                                </template>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <template x-if="transactions.length === 0 && !loading">
                            <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                No transactions found.
                            </div>
                        </template>

                        <!-- Transaction Cards List -->
                        <template x-for="(tx, index) in transactions" :key="tx.id">
                            <div class="bg-slate-50/50 rounded-2xl border border-slate-100 overflow-hidden shadow-sm hover:shadow transition duration-150">
                                <!-- Clickable Header Row -->
                                <div @click="toggleRow(tx.id)" class="grid grid-cols-4 gap-2 p-3.5 items-center cursor-pointer hover:bg-slate-100/50 transition text-center text-[11px] leading-tight">
                                    <!-- Date & Time -->
                                    <div class="text-left font-bold text-slate-700" x-text="tx.created_at"></div>
                                    <!-- Amount -->
                                    <div class="font-black text-slate-800" x-text="'₹' + tx.amount"></div>
                                    <!-- Method -->
                                    <div class="font-semibold text-slate-650 uppercase" x-text="tx.method"></div>
                                    <!-- Status -->
                                    <div class="text-right">
                                        <span :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': tx.status === 'approved',
                                                'bg-amber-50 text-amber-700 border-amber-200': tx.status === 'pending',
                                                'bg-rose-50 text-rose-700 border-rose-200': tx.status === 'rejected'
                                            }" 
                                            class="inline-block px-1.5 py-0.5 border text-[9px] font-extrabold rounded-full uppercase tracking-wider" 
                                            x-text="tx.status">
                                        </span>
                                    </div>
                                </div>

                                <!-- Expandable Details -->
                                <div x-show="expandedRows.includes(tx.id)" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 max-h-0"
                                     x-transition:enter-end="opacity-100 max-h-40"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 max-h-40"
                                     x-transition:leave-end="opacity-0 max-h-0"
                                     class="bg-white border-t border-slate-100 p-4 space-y-3 text-xs text-slate-600 overflow-hidden">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider mb-0.5">Transaction ID</span>
                                            <span class="font-mono text-slate-800 font-bold" x-text="tx.transaction_id"></span>
                                        </div>
                                        <!-- Approved fields -->
                                        <template x-if="tx.status === 'approved'">
                                            <div>
                                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider mb-0.5">Approved By</span>
                                                <span class="text-slate-800 font-bold text-primary" x-text="tx.approved_by || 'N/A'"></span>
                                            </div>
                                        </template>
                                        <template x-if="tx.status === 'approved'">
                                            <div class="col-span-2">
                                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider mb-0.5">Approved At</span>
                                                <span class="text-slate-800 font-semibold" x-text="tx.approved_at || 'N/A'"></span>
                                            </div>
                                        </template>
                                        <!-- Rejected fields -->
                                        <template x-if="tx.status === 'rejected'">
                                            <div>
                                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider mb-0.5">Rejected By</span>
                                                <span class="text-slate-800 font-bold text-rose-600" x-text="tx.rejected_by || 'N/A'"></span>
                                            </div>
                                        </template>
                                        <template x-if="tx.status === 'rejected'">
                                            <div class="col-span-2">
                                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider mb-0.5">Rejected At</span>
                                                <span class="text-slate-800 font-semibold" x-text="tx.rejected_at || 'N/A'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Show More button -->
                <template x-if="hasMore">
                    <div class="pt-2 text-center">
                        <button type="button" 
                            @click="fetchTransactions()" 
                            :disabled="loading"
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black rounded-xl transition duration-150 disabled:opacity-50 cursor-pointer">
                            <span x-show="!loading">Show More</span>
                            <span x-show="loading" class="flex items-center gap-1 justify-center">
                                <svg class="animate-spin h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading...
                            </span>
                        </button>
                    </div>
                </template>
            </div>



        </div>
    </div>
</x-app-layout>
