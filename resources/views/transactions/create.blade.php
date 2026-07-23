<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-wallet text-secondary"></i>
            {{ __('New Transaction Record') }}
        </h2>
    </x-slot>

    <div class="pt-4 pb-12 sm:pt-6 sm:pb-12" x-data="transactionBuilder()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Submit Transaction Record</h3>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-secondary font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left"></i>
                        {{ __('Back') }}
                    </a>
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="space-y-6 text-slate-700">
                    @csrf

                    <!-- Select User (Admins / Managers) -->
                    @can('manage_transactions')
                        <div>
                            <x-input-label for="user_id">
                                <span x-text="txnType === 'debit' ? 'Select Member (Optional for Club Expenses)' : 'Select Member'"></span>
                            </x-input-label>
                            <select name="user_id" id="user_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" :required="txnType === 'credit'">
                                <option value="" x-text="txnType === 'debit' ? '-- None (General Club Expense) --' : '-- Choose Member --'"></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>
                    @endcan

                    @can('manage_transactions')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type -->
                            <div>
                                <x-input-label for="type" :value="__('Transaction Type')" />
                                <select name="type" id="type" x-model="txnType" @change="onTxnTypeChange()" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="credit" @selected(old('type', 'credit') === 'credit')>Credit (Deposit / Member Dues)</option>
                                    <option value="debit" @selected(old('type') === 'debit')>Debit (Expense / Club Purchase)</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Method -->
                            <div>
                                <x-input-label for="method" :value="__('Payment Method')" />
                                <select name="method" id="method" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="bank" @selected(old('method') === 'bank')>Bank Transfer / Online</option>
                                    <option value="cash" @selected(old('method') === 'cash')>Cash</option>
                                </select>
                                <x-input-error :messages="$errors->get('method')" class="mt-2" />
                            </div>
                        </div>
                    @else
                        <!-- Method -->
                        <div>
                            <x-input-label for="method" :value="__('Payment Method')" />
                            <select name="method" id="method" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="bank" @selected(old('method') === 'bank')>Bank Transfer / Online</option>
                                <option value="cash" @selected(old('method') === 'cash')>Cash</option>
                            </select>
                            <x-input-error :messages="$errors->get('method')" class="mt-2" />
                        </div>
                    @endcan

                    <!-- Itemized Breakdown Section -->
                    <div class="border border-slate-200 rounded-2xl p-4 sm:p-5 bg-slate-50/50 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                            <div>
                                <h4 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                                    <i class="fa-solid fa-list-check text-secondary"></i> Itemized Breakdown
                                </h4>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="txnType === 'debit' ? 'Add event costs or custom expense descriptions.' : 'Add months for dues or select specific event/special fee heads.'"></p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" x-show="txnType === 'credit'" @click="addMonthlyItem()" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-bold rounded-xl transition">
                                    + Add Month Dues
                                </button>
                                <button type="button" @click="addFeeHeadItem()" class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-800 text-xs font-bold rounded-xl transition">
                                    + Add Event / Fee Head
                                </button>
                                <button type="button" x-show="txnType === 'debit'" @click="addCustomExpenseItem()" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold rounded-xl transition">
                                    + Add Custom Expense
                                </button>
                            </div>
                        </div>

                        <!-- Breakdown Rows -->
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm space-y-3 sm:space-y-0 sm:grid sm:grid-cols-12 sm:gap-3 sm:items-center">
                                    <!-- Item Title / Description -->
                                    <div class="sm:col-span-5">
                                        <template x-if="item.item_type === 'monthly'">
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Month</label>
                                                    <select :name="'items['+index+'][month]'" x-model="item.month" @change="updateItemTitle(index)" class="w-full text-xs rounded-lg border-slate-200 py-1.5">
                                                        @foreach($months as $mNum => $mName)
                                                            <option value="{{ $mNum }}">{{ $mName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Year</label>
                                                    <select :name="'items['+index+'][year]'" x-model="item.year" @change="updateItemTitle(index)" class="w-full text-xs rounded-lg border-slate-200 py-1.5">
                                                        @for($y = date('Y'); $y <= date('Y') + 3; $y++)
                                                            <option value="{{ $y }}">{{ $y }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="item.item_type === 'fee_head'">
                                            <div>
                                                <label class="text-[10px] font-bold text-slate-400 uppercase block">Fee Head / Event</label>
                                                <select :name="'items['+index+'][fee_type_id]'" x-model="item.fee_type_id" @change="onFeeHeadChange(index)" class="w-full text-xs rounded-lg border-slate-200 py-1.5">
                                                    <option value="">-- Select Fee Head --</option>
                                                    @foreach($feeTypes as $ft)
                                                        <option value="{{ $ft->id }}" data-amount="{{ $ft->default_amount }}" data-title="{{ $ft->title }}">{{ $ft->title }} (₹{{ number_format($ft->default_amount, 2) }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </template>

                                        <template x-if="item.item_type === 'custom'">
                                            <div>
                                                <label class="text-[10px] font-bold text-slate-400 uppercase block">Expense Description</label>
                                                <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="w-full text-xs rounded-lg border-slate-200 py-1.5" placeholder="e.g. Sound Rent, Snacks, Decoration..." required>
                                            </div>
                                        </template>

                                        <input type="hidden" :name="'items['+index+'][title]'" :value="item.title">
                                    </div>

                                    <!-- Item Category Badge -->
                                    <div class="sm:col-span-3 text-xs font-bold">
                                        <span x-text="item.title || 'Expense Item'" class="truncate block text-slate-800"></span>
                                    </div>

                                    <!-- Item Amount & Remove Button Inline Flex -->
                                    <div class="sm:col-span-4 flex items-center justify-between gap-2">
                                        <div class="flex-1">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block sm:hidden">Amount (₹)</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs text-slate-400 font-bold">₹</span>
                                                <input type="text" inputmode="decimal" :name="'items['+index+'][amount]'" x-model="item.amount" @keydown="preventInvalidAmountKeys($event)" @input="sanitizeAmount(index)" class="w-full text-xs font-bold pl-7 pr-2.5 py-1.5 rounded-lg border-slate-200 text-slate-800" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeItem(index)" class="h-8 w-8 mt-3.5 sm:mt-0 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center shrink-0 transition" title="Remove Item">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Total Calculation Row -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                            <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Total Calculated Amount:</span>
                            <span class="text-lg font-black text-emerald-600">₹<span x-text="totalAmount.toFixed(2)"></span></span>
                        </div>
                    </div>

                    <!-- Aggregate Amount Input (Readonly / Synced with Breakdown Total) -->
                    <div>
                        <x-input-label for="amount" :value="__('Total Payment Amount (INR)')" />
                        <x-text-input id="amount" class="block mt-1 w-full text-sm font-bold bg-slate-50 text-emerald-700" type="number" step="0.01" name="amount" x-model="totalAmount" required readonly />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <!-- Receipt Upload -->
                    <div>
                        <x-input-label for="document" :value="__('Upload Document / Receipt (Optional)')" />
                        <input id="document" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 text-sm" type="file" name="document" accept="image/*,application/pdf" />
                        <x-input-error :messages="$errors->get('document')" class="mt-2" />
                    </div>

                    <!-- Remark -->
                    <div>
                        <x-input-label for="remark" :value="__('Remark / Description (Optional)')" />
                        <textarea id="remark" name="remark" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" rows="2" placeholder="Additional notes about this payment...">{{ old('remark') }}</textarea>
                        <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <x-primary-button class="px-6 py-3">
                            {{ __('Submit Transaction') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
    function transactionBuilder() {
        return {
            items: [],
            totalAmount: 0.00,
            txnType: '{{ old('type', 'credit') }}',
            feeTypes: @json($feeTypes),
            monthsMap: {
                1: 'January', 2: 'February', 3: 'March', 4: 'April',
                5: 'May', 6: 'June', 7: 'July', 8: 'August',
                9: 'September', 10: 'October', 11: 'November', 12: 'December'
            },

            init() {
                const now = new Date();
                const curMonth = now.getMonth() + 1;
                const curYear = now.getFullYear();
                
                if (this.txnType === 'debit') {
                    this.addCustomExpenseItem();
                } else {
                    const monthlyFt = this.feeTypes.find(f => f.type === 'monthly');
                    this.items.push({
                        item_type: 'monthly',
                        fee_type_id: monthlyFt ? monthlyFt.id : null,
                        month: curMonth,
                        year: curYear,
                        title: this.monthsMap[curMonth] + ' ' + curYear + ' Dues',
                        amount: ''
                    });
                }

                this.calculateTotal();
            },

            onTxnTypeChange() {
                if (this.txnType === 'debit') {
                    this.items = this.items.filter(i => i.item_type !== 'monthly');
                    if (this.items.length === 0) {
                        this.addCustomExpenseItem();
                    }
                }
                this.calculateTotal();
            },

            addMonthlyItem() {
                if (this.txnType === 'debit') return;

                const now = new Date();
                let lastMonth = now.getMonth() + 1;
                let lastYear = now.getFullYear();

                if (this.items.length > 0) {
                    const lastMonthly = [...this.items].reverse().find(i => i.item_type === 'monthly');
                    if (lastMonthly) {
                        lastMonth = parseInt(lastMonthly.month) + 1;
                        lastYear = parseInt(lastMonthly.year);
                        if (lastMonth > 12) {
                            lastMonth = 1;
                            lastYear += 1;
                        }
                    }
                }

                const monthlyFt = this.feeTypes.find(f => f.type === 'monthly');

                this.items.push({
                    item_type: 'monthly',
                    fee_type_id: monthlyFt ? monthlyFt.id : null,
                    month: lastMonth,
                    year: lastYear,
                    title: this.monthsMap[lastMonth] + ' ' + lastYear + ' Dues',
                    amount: ''
                });

                this.calculateTotal();
            },

            addFeeHeadItem() {
                const eventFt = this.feeTypes.find(f => f.type === 'event' || f.type === 'general');
                this.items.push({
                    item_type: 'fee_head',
                    fee_type_id: eventFt ? eventFt.id : '',
                    month: null,
                    year: null,
                    title: eventFt ? eventFt.title : 'Special Fee',
                    amount: ''
                });

                this.calculateTotal();
            },

            addCustomExpenseItem() {
                this.items.push({
                    item_type: 'custom',
                    fee_type_id: null,
                    month: null,
                    year: null,
                    title: '',
                    amount: ''
                });

                this.calculateTotal();
            },

            updateItemTitle(index) {
                const item = this.items[index];
                if (item.item_type === 'monthly') {
                    item.title = this.monthsMap[item.month] + ' ' + item.year + ' Dues';
                }
                this.calculateTotal();
            },

            onFeeHeadChange(index) {
                const item = this.items[index];
                const ft = this.feeTypes.find(f => f.id == item.fee_type_id);
                if (ft) {
                    item.title = ft.title;
                }
                this.calculateTotal();
            },

            preventInvalidAmountKeys(e) {
                if (['e', 'E', '-', '+'].includes(e.key)) {
                    e.preventDefault();
                }
            },

            sanitizeAmount(index) {
                let val = String(this.items[index].amount || '');
                val = val.replace(/[^0-9.]/g, '');

                const parts = val.split('.');
                if (parts.length > 2) {
                    val = parts[0] + '.' + parts.slice(1).join('');
                }

                if (val.includes('.')) {
                    const [integerPart, decimalPart] = val.split('.');
                    val = integerPart + '.' + decimalPart.substring(0, 2);
                }

                this.items[index].amount = val;
                this.calculateTotal();
            },

            removeItem(index) {
                if (this.items.length <= 1) {
                    alert('At least one payment item is required.');
                    return;
                }
                this.items.splice(index, 1);
                this.calculateTotal();
            },

            calculateTotal() {
                let sum = 0;
                this.items.forEach(i => {
                    sum += (parseFloat(i.amount) || 0);
                });
                this.totalAmount = sum;
            }
        };
    }
    </script>
</x-app-layout>
