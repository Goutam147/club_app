<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
                <i class="fa-solid fa-receipt text-secondary"></i>
                {{ __('Transactions Log') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition duration-150">
                + Create Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-3">

            <!-- Filters & Stats Bar -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-4 sm:p-6 space-y-4">
                <!-- Stats Cards Grid -->
                <div class="grid grid-cols-3 gap-2 sm:gap-4 text-slate-700">
                    <!-- Total Credit Card -->
                    <div class="bg-emerald-50/40 border border-emerald-100 rounded-xl sm:rounded-2xl p-2.5 sm:p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] sm:text-xs text-emerald-700 font-bold uppercase tracking-wider">Credit</span>
                            <div class="h-5 w-5 sm:h-7 sm:w-7 bg-emerald-100/80 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-arrow-trend-up text-emerald-600 text-[10px] sm:text-xs"></i>
                            </div>
                        </div>
                        <div class="mt-1 sm:mt-2">
                            <span id="stat-credit" class="text-sm sm:text-xl md:text-2xl font-extrabold text-emerald-800 block truncate">₹0.00</span>
                        </div>
                    </div>

                    <!-- Total Debit Card -->
                    <div class="bg-rose-50/40 border border-rose-100 rounded-xl sm:rounded-2xl p-2.5 sm:p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] sm:text-xs text-rose-700 font-bold uppercase tracking-wider">Debit</span>
                            <div class="h-5 w-5 sm:h-7 sm:w-7 bg-rose-100/80 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-arrow-trend-down text-rose-600 text-[10px] sm:text-xs"></i>
                            </div>
                        </div>
                        <div class="mt-1 sm:mt-2">
                            <span id="stat-debit" class="text-sm sm:text-xl md:text-2xl font-extrabold text-rose-800 block truncate">₹0.00</span>
                        </div>
                    </div>

                    <!-- Current Balance Card -->
                    <div class="bg-indigo-50/40 border border-indigo-100 rounded-xl sm:rounded-2xl p-2.5 sm:p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] sm:text-xs text-indigo-700 font-bold uppercase tracking-wider">Balance</span>
                            <div class="h-5 w-5 sm:h-7 sm:w-7 bg-indigo-100/80 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-scale-balanced text-indigo-600 text-[10px] sm:text-xs"></i>
                            </div>
                        </div>
                        <div class="mt-1 sm:mt-2">
                            <span id="stat-balance" class="text-sm sm:text-xl md:text-2xl font-extrabold text-indigo-800 block truncate">₹0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="flex flex-row gap-2 sm:gap-4 items-end text-slate-700 border-t border-slate-100 pt-3 sm:pt-4">
                    @if(auth()->user()->hasAnyRole(['TH', 'President', 'Secretary', 'Cashier']))
                    <div class="flex-1 min-w-0">
                        <x-input-label for="filter-status" :value="__('Status')" />
                        <select id="filter-status" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Statuses</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <x-input-label for="filter-type" :value="__('Type')" />
                        <select id="filter-type" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All Types</option>
                            <option value="credit">Credit (Deposit)</option>
                            <option value="debit">Debit (Expense)</option>
                        </select>
                    </div>

                    <div class="flex-shrink-0">
                        <button type="button" id="btn-filter" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition duration-150 whitespace-nowrap flex items-center gap-1.5">
                            <i class="fa-solid fa-filter text-xs"></i>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Transactions List Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Transactions History</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm text-left">
                        <thead class="bg-light text-slate-700 uppercase tracking-wider text-xs font-bold">
                            <tr>
                                <th class="px-3 py-2 min-w-[150px]">TXN ID & Date</th>
                                <th class="px-3 py-2 min-w-[150px]">Member</th>
                                <th class="px-3 py-2">Amount</th>
                                <th class="px-3 py-2">Method & Type</th> 
                                <th class="px-3 py-2">Document</th>
                                <th class="px-3 py-2">Status & Details</th>
                            </tr>
                        </thead>
                        <tbody id="txn-tbody" class="divide-y divide-slate-50 text-slate-600">
                            <!-- Rows inserted by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State (hidden by default) -->
                <div id="txn-empty" class="hidden px-6 py-8 text-center text-slate-400 text-sm">
                    No transactions recorded.
                </div>

                <!-- Load More Button -->
                <div id="txn-load-more" class="hidden mt-6 flex justify-center">
                    <button type="button" id="btn-load-more" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-md transition duration-150 flex items-center gap-2">
                        Show More
                    </button>
                </div>

                <!-- Loading Spinner -->
                <div id="txn-loading" class="mt-6 flex justify-center">
                    <svg class="animate-spin h-6 w-6 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

        </div>
    </div>

    <script>
    (function() {
        const API_URL = "{{ route('transactions.load') }}";
        const tbody = document.getElementById('txn-tbody');
        const emptyEl = document.getElementById('txn-empty');
        const loadMoreWrap = document.getElementById('txn-load-more');
        const loadingEl = document.getElementById('txn-loading');
        const btnLoadMore = document.getElementById('btn-load-more');
        const btnFilter = document.getElementById('btn-filter');
        const filterStatus = document.getElementById('filter-status');
        const filterType = document.getElementById('filter-type');

        let lastId = null;
        let hasMore = false;
        let loading = false;
        let canManage = false;
        let totalLoaded = 0;

        function buildRow(txn) {
            // Amount color
            const amountClass = txn.type === 'credit' ? 'text-emerald-600' : 'text-rose-600';

            // Document cell
            let docHtml = '';
            if (txn.document_url) {
                docHtml = `<a href="${txn.document_url}" target="_blank" class="text-xs font-bold text-secondary hover:text-secondary-hover flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    View Receipt</a>`;
            } else {
                docHtml = `<span class="text-xs text-slate-400 italic">No Document</span>`;
            }

            // Status & Details cell
            let statusHtml = '';
            if (canManage) {
                // Status badge
                let badgeClass = 'bg-amber-100 text-amber-800';
                if (txn.status === 'approved') badgeClass = 'bg-emerald-100 text-emerald-800';
                else if (txn.status === 'rejected') badgeClass = 'bg-rose-100 text-rose-800';

                statusHtml = `<div><span class="px-2.5 py-1 text-xs font-extrabold rounded-full tracking-wide uppercase ${badgeClass}">${txn.status}</span></div>`;

                // Approver/Rejector details
                if (txn.status === 'approved' && txn.approved_by_name) {
                    statusHtml += `<div class="text-[10px] text-slate-400 mt-1">Approved by ${txn.approved_by_name} on ${txn.approved_at}</div>`;
                } else if (txn.status === 'rejected' && txn.rejected_by_name) {
                    statusHtml += `<div class="text-[10px] text-slate-400 mt-1">Rejected by ${txn.rejected_by_name} on ${txn.rejected_at}</div>`;
                }

                // Remark
                if (txn.remark) {
                    statusHtml += `<div class="text-xs text-slate-500 font-medium bg-slate-50 p-2 rounded-lg border border-slate-100 mt-1 max-w-xs truncate" title="${txn.remark}">${txn.remark}</div>`;
                }
            } else {
                // Non-manager: show only description
                if (txn.remark) {
                    statusHtml = `<div class="text-xs text-slate-500 font-medium bg-slate-50 p-2 rounded-lg border border-slate-100 max-w-xs truncate" title="${txn.remark}">${txn.remark}</div>`;
                } else {
                    statusHtml = `<span class="text-xs text-slate-400 italic">No description</span>`;
                }
            }

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/50 transition duration-150';
            tr.innerHTML = `
                <td class="px-3 py-2 min-w-[150px] whitespace-nowrap">
                    <span class="font-mono font-bold text-slate-800 text-sm block whitespace-nowrap">${txn.transaction_id || 'PENDING'}</span>
                    <span class="text-xs text-slate-400 block whitespace-nowrap">${txn.created_at}</span>
                </td>
                <td class="px-3 py-2">
                    <span class="font-semibold block text-slate-800">${txn.user_name}</span>
                </td>
                <td class="px-3 py-2">
                    <span class="font-extrabold text-base block ${amountClass}">₹${txn.amount}</span>
                </td>
                <td class="px-3 py-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">${txn.method}</div>
                    <div class="text-[10px] text-slate-400">${txn.type}</div>
                </td>
                <td class="px-3 py-2">${docHtml}</td>
                <td class="px-3 py-2 space-y-1">${statusHtml}</td>
            `;
            return tr;
        }

        function fetchTransactions() {
            if (loading) return;
            loading = true;
            loadingEl.classList.remove('hidden');
            loadMoreWrap.classList.add('hidden');

            // Build URL with params
            const params = new URLSearchParams();
            if (lastId) params.set('last_id', lastId);
            const status = filterStatus ? filterStatus.value : '';
            const type = filterType ? filterType.value : '';
            if (status) params.set('status', status);
            if (type) params.set('type', type);

            const url = API_URL + '?' + params.toString();

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                canManage = data.can_manage;
                hasMore = data.has_more;

                // Update stats cards
                document.getElementById('stat-credit').textContent = '₹' + data.total_credit;
                document.getElementById('stat-debit').textContent = '₹' + data.total_debit;
                document.getElementById('stat-balance').textContent = '₹' + data.current_balance;

                const txns = data.transactions;
                txns.forEach(function(txn) {
                    tbody.appendChild(buildRow(txn));
                    lastId = txn.id; // Track last id for cursor
                });

                totalLoaded += txns.length;

                // Show/hide empty state
                if (totalLoaded === 0) {
                    emptyEl.classList.remove('hidden');
                } else {
                    emptyEl.classList.add('hidden');
                }

                // Show/hide load more button
                if (hasMore) {
                    loadMoreWrap.classList.remove('hidden');
                } else {
                    loadMoreWrap.classList.add('hidden');
                }

                loading = false;
                loadingEl.classList.add('hidden');
            })
            .catch(function(err) {
                console.error('Error loading transactions:', err);
                loading = false;
                loadingEl.classList.add('hidden');
            });
        }

        // Load More button click
        btnLoadMore.addEventListener('click', function() {
            fetchTransactions();
        });

        // Apply Filters button click — reset and reload
        btnFilter.addEventListener('click', function() {
            lastId = null;
            totalLoaded = 0;
            hasMore = false;
            tbody.innerHTML = '';
            emptyEl.classList.add('hidden');
            fetchTransactions();
        });

        // Initial load on page ready
        fetchTransactions();
    })();
    </script>
</x-app-layout>
