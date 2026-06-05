<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-secondary"></i>
            {{ __('Add New Member') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-8 text-slate-700">
                <div class="mb-6 pb-4 border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-800">New Member Profile Configuration</h3>
                    <p class="text-sm text-slate-500 mt-1">Fill in the member's details. Accounts created here are automatically set to active by default.</p>
                </div>

                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Name, Email, Phone, Role -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" class="block mt-1 w-full text-sm" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" class="block mt-1 w-full text-sm" type="text" name="phone" :value="old('phone')" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Role / Position')" />
                            <select name="role" id="role" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected($role->name === 'Member')>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Passwords -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full text-sm" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm" type="password" name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Profile Upload -->
                    <div>
                        <x-input-label for="profile" :value="__('Profile Photo (Optional)')" />
                        <input id="profile" name="profile" type="file" accept="image/*" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm" />
                        <span class="text-xs text-slate-400">Accepted formats: jpg, jpeg, png. Max size: 2MB.</span>
                        <x-input-error :messages="$errors->get('profile')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-4 border-t border-slate-100 pt-6">
                        <a href="{{ route('users.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700 transition">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Create Active Member') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
