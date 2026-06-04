<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-primary leading-tight">
                {{ __('Transactions Log') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition duration-150">
                + Create Transaction
            </a>
        </div>
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

            <!-- Filters Bar -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end text-slate-700">
                    <div>
                        <x-input-label for="status" :value="__('Filter by Status')" />
                        <select name="status" id="status" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="type" :value="__('Filter by Type')" />
                        <select name="type" id="type" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Types</option>
                            <option value="credit" @selected(request('type') === 'credit')>Credit (Deposit)</option>
                            <option value="debit" @selected(request('type') === 'debit')>Debit (Expense)</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition duration-150">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions List Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Transactions History</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm text-left">
                        <thead class="bg-light text-slate-700 uppercase tracking-wider text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">TXN ID & Date</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Method & Type</th>
                                <th class="px-6 py-4">Document</th>
                                <th class="px-6 py-4">Status & Details</th>
                                @hasanyrole('TH|President')
                                    <th class="px-6 py-4 text-right">Actions</th>
                                @endhasanyrole
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-600">
                            @forelse($transactions as $txn)
                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <span class="font-mono font-bold text-slate-800 text-sm block">{{ $txn->transaction_id ?? 'PENDING' }}</span>
                                        <span class="text-xs text-slate-400">{{ $txn->created_at->format('M d, Y g:i A') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold block text-slate-800">{{ $txn->user->name ?? 'Deleted User' }}</span>
                                        <span class="text-xs text-slate-400">{{ $txn->user->email ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-extrabold text-base block @if($txn->type === 'credit') text-emerald-600 @else text-rose-600 @endif">
                                            @if($txn->type === 'credit') + @else - @endif ₹{{ number_format($txn->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $txn->method }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $txn->type }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($txn->document_url)
                                            <a href="{{ asset($txn->document_url) }}" target="_blank" class="text-xs font-bold text-secondary hover:text-secondary-hover flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                View Receipt
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 italic">No Document</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 space-y-1">
                                        <div>
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-full tracking-wide uppercase 
                                                @if($txn->status === 'approved') bg-emerald-100 text-emerald-800 
                                                @elseif($txn->status === 'rejected') bg-rose-100 text-rose-800 
                                                @else bg-amber-100 text-amber-800 @endif">
                                                {{ $txn->status }}
                                            </span>
                                        </div>
                                        
                                        <!-- Approver details -->
                                        @if($txn->status === 'approved' && $txn->approvedBy)
                                            <div class="text-[10px] text-slate-400">
                                                Approved by {{ $txn->approvedBy->name }} on {{ $txn->approved_at->format('M d') }}
                                            </div>
                                        @elseif($txn->status === 'rejected' && $txn->rejectedBy)
                                            <div class="text-[10px] text-slate-400">
                                                Rejected by {{ $txn->rejectedBy->name }} on {{ $txn->rejected_at->format('M d') }}
                                            </div>
                                        @endif

                                        @if($txn->remark)
                                            <div class="text-xs text-slate-500 font-medium bg-slate-50 p-2 rounded-lg border border-slate-100 mt-1 max-w-xs truncate" title="{{ $txn->remark }}">
                                                {{ $txn->remark }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    @hasanyrole('TH|President')
                                        <td class="px-6 py-4 text-right space-y-2">
                                            @if($txn->status === 'pending')
                                                <!-- Approve -->
                                                <form method="POST" action="{{ route('transactions.approve', $txn) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-sm transition duration-150">
                                                        Approve
                                                    </button>
                                                </form>

                                                <!-- Reject Form inline text -->
                                                <form method="POST" action="{{ route('transactions.reject', $txn) }}" class="inline-block ms-1">
                                                    @csrf
                                                    <input type="text" name="reject_reason" placeholder="Reason" class="text-[10px] px-2 py-1 rounded-lg border-slate-200 w-24 focus:ring-rose-500 focus:border-rose-500">
                                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-lg shadow-sm transition duration-150">
                                                        Reject
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>
                                    @endhasanyrole
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">
                                        No transactions recorded.
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
