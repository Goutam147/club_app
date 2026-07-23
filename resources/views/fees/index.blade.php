<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
                <i class="fa-solid fa-tags text-secondary"></i>
                {{ __('Fees Manager') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        editModalOpen: false,
        editFee: { id: null, title: '', type: 'event', default_amount: '0.00', event_id: '', status: 'active', actionUrl: '' },
        openEditModal(fee) {
            this.editFee = {
                id: fee.id,
                title: fee.title,
                type: fee.type,
                default_amount: fee.default_amount,
                event_id: fee.event_id || '',
                status: fee.status,
                actionUrl: '{{ url('/fees') }}/' + fee.id
            };
            this.editModalOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Create New Fee Head Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Add New Fee Head</h3>
                
                <form method="POST" action="{{ route('fees.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-slate-700 items-end">
                    @csrf
                    <div>
                        <x-input-label for="title" :value="__('Fee Title')" />
                        <x-text-input id="title" class="block mt-1 w-full text-sm" type="text" name="title" required placeholder="e.g. Independence Day Fee" />
                    </div>

                    <div>
                        <x-input-label for="type" :value="__('Fee Category')" />
                        <select name="type" id="type" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            <option value="event" selected>Event / Occasional Fee</option>
                            <option value="general">General / Special Donation</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="default_amount" :value="__('Default Amount (₹)')" />
                        <x-text-input id="default_amount" class="block mt-1 w-full text-sm" type="number" step="0.01" name="default_amount" required placeholder="100.00" />
                    </div>

                    <div>
                        <x-input-label for="event_id" :value="__('Linked Event (Optional)')" />
                        <select name="event_id" id="event_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- None (Standalone Fee) --</option>
                            @foreach($events as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4 flex justify-end mt-2">
                        <button type="submit" class="px-5 py-2.5 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition duration-150 flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Save Fee Head
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Fee Heads List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Active & Configured Fee Heads</h3>

                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full min-w-full divide-y divide-slate-100 text-sm text-left">
                        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3.5">Fee Title</th>
                                <th class="px-4 py-3.5">Category</th>
                                <th class="px-4 py-3.5">Default Amount</th>
                                <th class="px-4 py-3.5">Linked Event</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-600 bg-white">
                            @forelse($feeTypes as $fee)
                                <tr class="hover:bg-slate-50/60 transition duration-150">
                                    <td class="px-4 py-3.5 font-bold text-slate-800 whitespace-nowrap">
                                        {{ $fee->title }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        @if($fee->type === 'monthly')
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-full bg-blue-100 text-blue-800">Monthly Dues</span>
                                        @elseif($fee->type === 'event')
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-full bg-purple-100 text-purple-800">Event / Special</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-full bg-slate-100 text-slate-700">General</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-black text-slate-900 whitespace-nowrap">
                                        ₹{{ number_format($fee->default_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                        {{ $fee->event ? $fee->event->title : 'None' }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-extrabold rounded-full {{ $fee->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <button type="button" @click="openEditModal({{ json_encode($fee) }})" class="px-2.5 py-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition me-2">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                        </button>

                                        <form method="POST" action="{{ route('fees.destroy', $fee) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete/deactivate this fee head?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">
                                        No fee categories configured yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Edit Fee Head Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5" @click.away="editModalOpen = false">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Edit Fee Head</h3>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" :action="editFee.actionUrl" class="space-y-4 text-slate-700">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="edit_title" :value="__('Fee Title')" />
                        <x-text-input id="edit_title" class="block mt-1 w-full text-sm" type="text" name="title" x-model="editFee.title" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_type" :value="__('Fee Category')" />
                            <select name="type" id="edit_type" x-model="editFee.type" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="event">Event / Occasional Fee</option>
                                <option value="general">General / Special Donation</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="edit_default_amount" :value="__('Default Amount (₹)')" />
                            <x-text-input id="edit_default_amount" class="block mt-1 w-full text-sm" type="number" step="0.01" name="default_amount" x-model="editFee.default_amount" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_event_id" :value="__('Linked Event (Optional)')" />
                            <select name="event_id" id="edit_event_id" x-model="editFee.event_id" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- None (Standalone Fee) --</option>
                                @foreach($events as $ev)
                                    <option value="{{ $ev->id }}">{{ $ev->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="edit_status" :value="__('Status')" />
                            <select name="status" id="edit_status" x-model="editFee.status" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 bg-secondary hover:bg-secondary-hover text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i> Update Fee Head
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
