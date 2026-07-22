<!-- Desktop Sticky Top Navigation Bar (Hidden on Mobile) -->
<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 sticky top-0 z-40 hidden sm:block">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
        <div class="flex justify-between h-16 items-center gap-2">
            <div class="flex items-center gap-2 lg:gap-4 min-w-0 flex-1">
                <!-- Logo -->
                <div class="shrink-0 flex items-center me-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-black text-primary tracking-tight whitespace-nowrap">
                        <x-application-logo class="block h-8 w-auto fill-current text-primary" />
                        <span class="hidden md:inline">BSC PORTAL</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex items-center space-x-1 md:space-x-2 lg:space-x-3 h-16 overflow-x-auto no-scrollbar">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('Members') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.index')">
                        {{ __('Transactions') }}
                    </x-nav-link>
                    @can('approve_transactions')
                        <x-nav-link :href="route('transactions.approvals')" :active="request()->routeIs('transactions.approvals')">
                            {{ __('Approvals') }}
                            @php
                                $pendingTxnCount = \App\Models\Transaction::where('status', 'pending')->count();
                            @endphp
                            @if($pendingTxnCount > 0)
                                <span class="ms-1 px-1.5 py-0.5 text-[10px] rounded-full bg-amber-500 text-white font-bold">{{ $pendingTxnCount }}</span>
                            @endif
                        </x-nav-link>
                    @endcan
                    <x-nav-link :href="route('notices.index')" :active="request()->routeIs('notices.index')">
                        {{ __('Notices') }}
                    </x-nav-link>
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
                        {{ __('Events') }}
                    </x-nav-link>
                    <x-nav-link :href="route('gallery.index')" :active="request()->routeIs('gallery.index')">
                        {{ __('Gallery') }}
                    </x-nav-link>
                    @hasanyrole('TH|President|Secretary')
                        <x-nav-link :href="route('users.pending')" :active="request()->routeIs('users.pending')">
                            {{ __('Pending') }}
                            @php
                                $pendingCount = \App\Models\User::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="ms-1 px-1.5 py-0.5 text-[10px] rounded-full bg-red-500 text-white font-bold">{{ $pendingCount }}</span>
                            @endif
                        </x-nav-link>
                        <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.index')">
                            {{ __('Settings') }}
                        </x-nav-link>
                    @endhasanyrole
                    @role('TH')
                        <x-nav-link :href="route('roles-permissions.index')" :active="request()->routeIs('roles-permissions.index')">
                            {{ __('Roles') }}
                        </x-nav-link>
                    @endrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2.5 py-1.5 border border-slate-200 text-xs sm:text-sm font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition ease-in-out duration-150 whitespace-nowrap shadow-sm">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile layout state wrapper -->
<div x-data="{ moreOpen: false }" class="sm:hidden" x-init="$watch('moreOpen', value => document.body.style.overflow = value ? 'hidden' : '')">
    

    <!-- Mobile sticky bottom navigation bar (Flutter BottomNavigationBar style) -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-150 h-16 flex items-center justify-around shadow-[0_-4px_12px_rgba(0,0,0,0.06)] px-2">
        <!-- Home/Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-extrabold tracking-tight transition duration-150 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="fa-solid fa-house text-lg"></i>
            <span>Home</span>
        </a>

        <!-- Member Directory -->
        <a href="{{ route('users.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-extrabold tracking-tight transition duration-150 {{ request()->routeIs('users.index') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="fa-solid fa-users text-lg"></i>
            <span>Directory</span>
        </a>

        <!-- Transactions -->
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-extrabold tracking-tight transition duration-150 {{ request()->routeIs('transactions.index') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="fa-solid fa-wallet text-lg"></i>
            <span>Wallet</span>
        </a>

        <!-- My Profile -->
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-extrabold tracking-tight transition duration-150 {{ request()->routeIs('profile.edit') ? 'text-primary' : 'text-slate-400 hover:text-slate-600' }}">
            <i class="fa-solid fa-user text-lg"></i>
            <span>Profile</span>
        </a>

        <!-- More Drawer trigger -->
        <button type="button" @click="moreOpen = !moreOpen" :class="moreOpen ? 'text-primary' : 'text-slate-400 hover:text-slate-600'" class="flex flex-col items-center gap-0.5 text-[10px] font-extrabold tracking-tight transition duration-150 focus:outline-none cursor-pointer">
            <i class="fa-solid fa-bars text-lg pointer-events-none"></i>
            <span class="pointer-events-none">More</span>
        </button>
    </nav>

    <!-- Mobile Bottom Sheet Backdrop -->
    <div x-show="moreOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="moreOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100]"
         style="display: none;">
    </div>

    <!-- Mobile Bottom Sheet Panel -->
    <div x-show="moreOpen" x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 bg-white rounded-t-[32px] shadow-2xl border-t border-slate-100 z-[101] max-h-[85vh] overflow-y-auto pb-10"
         style="display: none;">
         
        <!-- Drag/Close bar indicator -->
        <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto my-4 cursor-pointer" @click="moreOpen = false"></div>

        <div class="px-6 pb-4">
            <h4 class="text-lg font-black text-slate-800 tracking-tight">More Features</h4>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-0.5">Bhimchak Sunrise Club Portal</p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-3 gap-y-6 px-6 py-4 text-center">
            <!-- Notices -->
            <a href="{{ route('notices.index') }}" class="flex flex-col items-center gap-2 group">
                <div class="h-12 w-12 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-secondary hover:bg-secondary hover:text-white transition duration-150 shadow-sm">
                    <i class="fa-solid fa-bullhorn text-lg"></i>
                </div>
                <span class="text-xs font-bold text-slate-750">Notices</span>
            </a>

            <!-- Events -->
            <a href="{{ route('events.index') }}" class="flex flex-col items-center gap-2 group">
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 hover:bg-emerald-600 hover:text-white transition duration-150 shadow-sm">
                    <i class="fa-solid fa-calendar-days text-lg"></i>
                </div>
                <span class="text-xs font-bold text-slate-750">Events</span>
            </a>

            <!-- Gallery -->
            <a href="{{ route('gallery.index') }}" class="flex flex-col items-center gap-2 group">
                <div class="h-12 w-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 hover:bg-purple-600 hover:text-white transition duration-150 shadow-sm">
                    <i class="fa-solid fa-image text-lg"></i>
                </div>
                <span class="text-xs font-bold text-slate-750">Gallery</span>
            </a>

            @can('approve_transactions')
                <!-- Approve Transactions -->
                <a href="{{ route('transactions.approvals') }}" class="flex flex-col items-center gap-2 group relative">
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 hover:bg-amber-600 hover:text-white transition duration-150 shadow-sm">
                        <i class="fa-solid fa-clipboard-check text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-750">Approvals</span>
                    @php
                        $mobilePendingTxnCount = \App\Models\Transaction::where('status', 'pending')->count();
                    @endphp
                    @if($mobilePendingTxnCount > 0)
                        <span class="absolute top-0 right-4 h-4 w-4 bg-amber-500 text-white rounded-full flex items-center justify-center text-[9px] font-black shadow-sm">
                            {{ $mobilePendingTxnCount }}
                        </span>
                    @endif
                </a>
            @endcan

            @hasanyrole('TH|President|Secretary')
                <!-- Pending Approvals -->
                <a href="{{ route('users.pending') }}" class="flex flex-col items-center gap-2 group relative">
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 hover:bg-amber-600 hover:text-white transition duration-150 shadow-sm">
                        <i class="fa-solid fa-user-clock text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-755">Pending</span>
                    @if($pendingCount > 0)
                        <span class="absolute top-0 right-6 h-4 w-4 bg-red-500 text-white rounded-full flex items-center justify-center text-[9px] font-black shadow-sm">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="flex flex-col items-center gap-2 group">
                    <div class="h-12 w-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-650 hover:bg-slate-600 hover:text-white transition duration-150 shadow-sm">
                        <i class="fa-solid fa-gear text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-750">Settings</span>
                </a>
            @endhasanyrole

            @role('TH')
                <!-- Roles & Permissions -->
                <a href="{{ route('roles-permissions.index') }}" class="flex flex-col items-center gap-2 group">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-150 shadow-sm">
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-750">Roles</span>
                </a>
            @endrole
        </div>

        <!-- User Details & Logout Section -->
        <div class="mx-6 mt-6 pt-6 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if(Auth::user()->profile)
                    <img src="{{ asset(Auth::user()->profile) }}" alt="Avatar" class="h-10 w-10 rounded-full object-cover border border-slate-100">
                @else
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                @endif
                <div class="text-left">
                    <h5 class="text-sm font-bold text-slate-800 leading-none">{{ Auth::user()->name }}</h5>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1 block">{{ Auth::user()->roles->pluck('name')->first() ?: 'Member' }}</span>
                </div>
            </div>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-600 text-xs font-black rounded-xl transition duration-150 flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
