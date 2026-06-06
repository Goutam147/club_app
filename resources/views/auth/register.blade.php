<x-guest-layout>
    <style>
        .register-input-wrapper {
            position: relative;
            width: 100%;
        }
        .register-input {
            width: 100%;
            padding-top: 10px;
            padding-bottom: 10px;
            padding-left: 42px;
            padding-right: 42px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            background-color: rgba(248, 250, 252, 0.8);
            transition: all 150ms ease-in-out;
            box-sizing: border-box;
        }
        .register-input:focus {
            outline: none;
            border-color: #1e3a8a; /* primary color */
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        }
        .register-icon-left {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .register-icon-right {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            width: 20px;
            height: 20px;
        }
        .register-icon-right:hover {
            color: #475569;
        }
        .register-file-input {
            width: 100%;
            padding: 8px 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            color: #475569;
            background-color: rgba(248, 250, 252, 0.8);
            cursor: pointer;
            box-sizing: border-box;
        }
        .register-submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #1e3a8a; /* primary */
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            border-radius: 12px;
            transition: all 150ms ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.1), 0 2px 4px -1px rgba(30, 58, 138, 0.06);
            box-sizing: border-box;
        }
        .register-submit-btn:hover {
            background-color: #1e40af; /* primary hover */
            box-shadow: 0 10px 15px -3px rgba(30, 58, 138, 0.2), 0 4px 6px -2px rgba(30, 58, 138, 0.05);
        }
        .register-submit-btn:active {
            transform: scale(0.98);
        }
    </style>

    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-800 text-center tracking-tight">Create Account</h2>
        <p class="text-xs text-slate-400 font-bold text-center mt-1">Join the Bhimchak Sunrise Club Portal</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
            <div class="register-input-wrapper">
                <div class="register-icon-left">
                    <i class="fa-solid fa-user"></i>
                </div>
                <input id="name" 
                       class="register-input" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       placeholder="John Doe"
                       autocomplete="name" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
            <div class="register-input-wrapper">
                <div class="register-icon-left">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <input id="email" 
                       class="register-input" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="johndoe@example.com"
                       autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone Number</label>
            <div class="register-input-wrapper">
                <div class="register-icon-left">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <input id="phone" 
                       class="register-input" 
                       type="text" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       required 
                       placeholder="9988776655"
                       autocomplete="tel" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
        </div>

        <!-- Profile Picture -->
        <div>
            <label for="profile" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Profile Picture</label>
            <input id="profile" 
                   class="register-file-input" 
                   type="file" 
                   name="profile" 
                   accept="image/*" />
            <x-input-error :messages="$errors->get('profile')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Password</label>
            <div class="register-input-wrapper">
                <div class="register-icon-left">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input id="password" 
                       :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       required 
                       placeholder="••••••••"
                       class="register-input"
                       autocomplete="new-password" />
                <button type="button" 
                        @click="showPassword = !showPassword" 
                        class="register-icon-right"
                        title="Toggle password visibility">
                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div x-data="{ showPassword: false }">
            <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Confirm Password</label>
            <div class="register-input-wrapper">
                <div class="register-icon-left">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input id="password_confirmation" 
                       :type="showPassword ? 'text' : 'password'" 
                       name="password_confirmation" 
                       required 
                       placeholder="••••••••"
                       class="register-input"
                       autocomplete="new-password" />
                <button type="button" 
                        @click="showPassword = !showPassword" 
                        class="register-icon-right"
                        title="Toggle password visibility">
                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="register-submit-btn">
                <i class="fa-solid fa-user-plus"></i>
                Create New Account
            </button>
        </div>
    </form>
</x-guest-layout>
