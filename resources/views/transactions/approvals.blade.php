<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-secondary"></i>
                {{ __('Approve Transactions') }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition duration-150 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                All Transactions
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-3">

            <!-- Pending Count Banner -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 overflow-hidden shadow-sm rounded-2xl border border-amber-200/60 p-4 sm:p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 bg-amber-100 rounded-2xl flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-hourglass-half text-amber-600 text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-amber-900">Pending Approvals</h3>
                        <p class="text-[10px] sm:text-xs text-amber-600 font-bold mt-0.5">Transactions awaiting your review</p>
                    </div>
                </div>
                <div class="text-right">
                    <span id="pending-count-badge" class="inline-flex items-center justify-center h-9 w-9 sm:h-11 sm:w-11 bg-amber-500 text-white text-sm sm:text-lg font-black rounded-2xl shadow-md">
                        0
                    </span>
                </div>
            </div>

            <!-- Transactions List Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-4 sm:p-6">

                <!-- Transactions Container -->
                <div id="approvals-list" class="space-y-3">
                    <!-- Cards inserted by JS -->
                </div>

                <!-- Empty State -->
                <div id="approvals-empty" class="hidden py-12 text-center">
                    <div class="mx-auto h-16 w-16 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-check-double text-emerald-500 text-2xl"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-700">All Caught Up!</h4>
                    <p class="text-sm text-slate-400 mt-1">No pending transactions to review.</p>
                </div>

                <!-- Load More -->
                <div id="approvals-load-more" class="hidden mt-4 flex justify-center">
                    <button type="button" id="btn-load-more" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-md transition duration-150 flex items-center gap-2 cursor-pointer">
                        Show More
                    </button>
                </div>

                <!-- Loading Spinner -->
                <div id="approvals-loading" class="mt-6 flex justify-center">
                    <svg class="animate-spin h-6 w-6 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div id="reject-modal" class="fixed inset-0 z-[200] hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>
        <!-- Modal Content -->
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md p-6 relative">
                <button type="button" onclick="closeRejectModal()" class="absolute top-4 right-4 h-8 w-8 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 bg-rose-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-ban text-rose-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Reject Transaction</h3>
                        <p class="text-xs text-slate-400 font-bold" id="reject-txn-info"></p>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="reject-reason-input" class="block text-sm font-bold text-slate-700 mb-1.5">Reason for Rejection</label>
                    <textarea id="reject-reason-input" rows="3" class="block w-full rounded-xl border-slate-200 focus:border-rose-400 focus:ring-rose-400 text-sm placeholder-slate-300" placeholder="Explain why this transaction is being rejected..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" id="btn-confirm-reject" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl transition shadow-md cursor-pointer">
                        <i class="fa-solid fa-ban mr-1"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const LOAD_URL = "{{ route('transactions.approvals.load') }}";
        const APPROVE_URL = "{{ url('/transactions') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const listEl = document.getElementById('approvals-list');
        const emptyEl = document.getElementById('approvals-empty');
        const loadMoreWrap = document.getElementById('approvals-load-more');
        const loadingEl = document.getElementById('approvals-loading');
        const btnLoadMore = document.getElementById('btn-load-more');
        const pendingBadge = document.getElementById('pending-count-badge');

        let lastId = null;
        let hasMore = false;
        let loading = false;
        let totalLoaded = 0;

        // Reject modal state
        let rejectTxnId = null;

        function buildCard(txn) {
            const isCredit = txn.type === 'credit';
            const amountClass = isCredit ? 'text-emerald-600' : 'text-rose-600';
            const typeBadgeClass = isCredit 
                ? 'bg-emerald-50 text-emerald-700 border-emerald-100' 
                : 'bg-rose-50 text-rose-700 border-rose-100';
            const typeIcon = isCredit ? 'fa-arrow-up' : 'fa-arrow-down';

            // User avatar
            let avatarHtml = '';
            if (txn.user_profile) {
                avatarHtml = `<img src="${txn.user_profile}" alt="${txn.user_name}" class="h-10 w-10 rounded-full object-cover border-2 border-slate-100">`;
            } else {
                const initials = txn.user_name.substring(0, 2).toUpperCase();
                avatarHtml = `<div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm border-2 border-slate-100">${initials}</div>`;
            }

            // Document link
            let docHtml = '';
            if (txn.document_url) {
                docHtml = `<a href="${txn.document_url}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-secondary hover:text-secondary-hover transition">
                    <i class="fa-solid fa-file-invoice text-xs"></i> View Receipt
                </a>`;
            }

            // Remark
            let remarkHtml = '';
            if (txn.remark) {
                remarkHtml = `<div class="text-xs text-slate-500 bg-slate-50 rounded-lg p-2 border border-slate-100 mt-2 line-clamp-2">${txn.remark}</div>`;
            }

            const card = document.createElement('div');
            card.id = 'txn-card-' + txn.id;
            card.className = 'bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition duration-200';
            card.innerHTML = `
                <!-- Header: User + Amount -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        ${avatarHtml}
                        <div class="min-w-0">
                            <h4 class="text-sm font-extrabold text-slate-800 truncate">${txn.user_name}</h4>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="font-mono text-[11px] text-slate-400 font-bold">#${txn.transaction_id || 'PENDING'}</span>
                                <span class="text-[10px] text-slate-300">•</span>
                                <span class="text-[11px] text-slate-400">${txn.created_at}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-lg font-extrabold ${amountClass}">₹${txn.amount}</span>
                        <div class="mt-0.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-lg border ${typeBadgeClass}">
                                <i class="fa-solid ${typeIcon} text-[8px]"></i> ${txn.type}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Meta: Method + Doc -->
                <div class="flex items-center gap-3 mt-3 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-600 text-[11px] font-bold rounded-lg border border-slate-100">
                        <i class="fa-solid ${txn.method === 'bank' ? 'fa-building-columns' : 'fa-money-bill'} text-[10px]"></i>
                        ${txn.method === 'bank' ? 'Bank Transfer' : 'Cash'}
                    </span>
                    ${docHtml}
                    ${txn.created_by_name ? `<span class="text-[11px] text-slate-400">Submitted by <strong class="text-slate-500">${txn.created_by_name}</strong></span>` : ''}
                </div>

                ${remarkHtml}

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="approveTransaction(${txn.id})" class="flex-1 px-3 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i> Approve
                    </button>
                    <button type="button" onclick="openRejectModal(${txn.id}, '${txn.transaction_id || ''} - ₹${txn.amount}')" class="flex-1 px-3 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition border border-rose-200 flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i> Reject
                    </button>
                </div>
            `;
            return card;
        }

        function fetchTransactions() {
            if (loading) return;
            loading = true;
            loadingEl.classList.remove('hidden');
            loadMoreWrap.classList.add('hidden');

            const params = new URLSearchParams();
            if (lastId) params.set('last_id', lastId);

            fetch(LOAD_URL + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                hasMore = data.has_more;
                pendingBadge.textContent = data.pending_count;

                data.transactions.forEach(function(txn) {
                    listEl.appendChild(buildCard(txn));
                    lastId = txn.id;
                });

                totalLoaded += data.transactions.length;

                if (totalLoaded === 0) {
                    emptyEl.classList.remove('hidden');
                } else {
                    emptyEl.classList.add('hidden');
                }

                if (hasMore) {
                    loadMoreWrap.classList.remove('hidden');
                } else {
                    loadMoreWrap.classList.add('hidden');
                }

                loading = false;
                loadingEl.classList.add('hidden');
            })
            .catch(function(err) {
                console.error('Error loading approvals:', err);
                loading = false;
                loadingEl.classList.add('hidden');
            });
        }

        // Approve a transaction
        window.approveTransaction = function(id) {
            const card = document.getElementById('txn-card-' + id);
            if (!card) return;

            // Disable buttons
            const btns = card.querySelectorAll('button');
            btns.forEach(function(b) { b.disabled = true; b.style.opacity = '0.5'; });

            fetch(APPROVE_URL + '/' + id + '/approve', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    // Animate removal
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'translateX(100%)';
                    card.style.opacity = '0';
                    setTimeout(function() {
                        card.remove();
                        totalLoaded--;
                        // Update count
                        const current = parseInt(pendingBadge.textContent) || 0;
                        pendingBadge.textContent = Math.max(0, current - 1);
                        if (totalLoaded === 0 && !hasMore) {
                            emptyEl.classList.remove('hidden');
                        }
                    }, 300);
                } else {
                    alert(data.error || 'Failed to approve transaction.');
                    btns.forEach(function(b) { b.disabled = false; b.style.opacity = '1'; });
                }
            })
            .catch(function(err) {
                console.error('Error approving:', err);
                alert('Network error. Please try again.');
                btns.forEach(function(b) { b.disabled = false; b.style.opacity = '1'; });
            });
        };

        // Reject modal
        window.openRejectModal = function(id, info) {
            rejectTxnId = id;
            document.getElementById('reject-txn-info').textContent = info;
            document.getElementById('reject-reason-input').value = '';
            document.getElementById('reject-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        window.closeRejectModal = function() {
            rejectTxnId = null;
            document.getElementById('reject-modal').classList.add('hidden');
            document.body.style.overflow = '';
        };

        document.getElementById('btn-confirm-reject').addEventListener('click', function() {
            if (!rejectTxnId) return;
            const reason = document.getElementById('reject-reason-input').value.trim();
            const id = rejectTxnId;
            closeRejectModal();

            const card = document.getElementById('txn-card-' + id);
            if (!card) return;

            const btns = card.querySelectorAll('button');
            btns.forEach(function(b) { b.disabled = true; b.style.opacity = '0.5'; });

            fetch(APPROVE_URL + '/' + id + '/reject', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ reject_reason: reason || 'N/A' }),
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'translateX(-100%)';
                    card.style.opacity = '0';
                    setTimeout(function() {
                        card.remove();
                        totalLoaded--;
                        const current = parseInt(pendingBadge.textContent) || 0;
                        pendingBadge.textContent = Math.max(0, current - 1);
                        if (totalLoaded === 0 && !hasMore) {
                            emptyEl.classList.remove('hidden');
                        }
                    }, 300);
                } else {
                    alert(data.error || 'Failed to reject transaction.');
                    btns.forEach(function(b) { b.disabled = false; b.style.opacity = '1'; });
                }
            })
            .catch(function(err) {
                console.error('Error rejecting:', err);
                alert('Network error. Please try again.');
                btns.forEach(function(b) { b.disabled = false; b.style.opacity = '1'; });
            });
        });

        btnLoadMore.addEventListener('click', fetchTransactions);

        // Initial load
        fetchTransactions();
    })();
    </script>
</x-app-layout>
