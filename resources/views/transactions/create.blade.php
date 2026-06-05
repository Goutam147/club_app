<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-wallet text-secondary"></i>
            {{ __('New Transaction Record') }}
        </h2>
    </x-slot>

    <div class="pt-4 pb-12 sm:pt-6 sm:pb-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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
                            <x-input-label for="user_id" :value="__('Select Member')" />
                            <select name="user_id" id="user_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">-- Choose Member --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>
                    @endcan

                    <!-- Amount -->
                    <div>
                        <x-input-label for="amount" :value="__('Amount (INR)')" />
                        <x-text-input id="amount" class="block mt-1 w-full text-sm" type="number" step="0.01" name="amount" :value="old('amount')" required placeholder="0.00" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    @can('manage_transactions')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type -->
                            <div>
                                <x-input-label for="type" :value="__('Transaction Type')" />
                                <select name="type" id="type" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
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

                    <!-- Receipt Upload -->
                    <div>
                        <x-input-label for="document" :value="__('Upload Document / Receipt Receipt (Optional)')" />
                        <input id="document" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 text-sm" type="file" name="document" accept="image/*,application/pdf" />
                        <x-input-error :messages="$errors->get('document')" class="mt-2" />
                    </div>

                    <!-- Remark -->
                    <div>
                        <x-input-label for="remark" :value="__('Remark / Description')" />
                        <textarea id="remark" name="remark" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" rows="3" placeholder="Explain the purpose of this transaction...">{{ old('remark') }}</textarea>
                        <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            {{ __('Submit Transaction') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
